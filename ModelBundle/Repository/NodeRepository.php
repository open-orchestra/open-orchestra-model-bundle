<?php

namespace PHPOrchestra\ModelBundle\Repository;

use Doctrine\ODM\MongoDB\Cursor;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\Mapping;
use PHPOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use PHPOrchestra\ModelBundle\Repository\RepositoryTrait\AreaFinderTrait;
use PHPOrchestra\ModelInterface\Model\AreaInterface;
use PHPOrchestra\ModelInterface\Model\NodeInterface;
use PHPOrchestra\ModelInterface\Model\AreaContainerInterface;
use PHPOrchestra\ModelInterface\Repository\NodeRepositoryInterface;

/**
 * Class NodeRepository
 */
class NodeRepository extends DocumentRepository implements FieldAutoGenerableRepositoryInterface, NodeRepositoryInterface
{
    use AreaFinderTrait;

    /**
     * @var CurrentSiteIdInterface
     */
    protected $currentSiteManager;

    /**
     * @param CurrentSiteIdInterface $currentSiteManager
     */
    public function setCurrentSiteManager(CurrentSiteIdInterface $currentSiteManager)
    {
        $this->currentSiteManager = $currentSiteManager;
    }

    /**
     * @param string $language
     *
     * @return array
     */
    public function getFooterTree($language = null)
    {
        return $this->getTreeByLanguageAndField($language, 'inFooter');
    }

    /**
     * @param string $language
     *
     * @return array
     */
    public function getMenuTree($language = null)
    {
        return $this->getTreeByLanguageAndField($language, 'inMenu');
    }

    /**
     * @param string $nodeId
     * @param int    $nbLevel
     * @param string $language
     *
     * @return array
     */
    public function getSubMenu($nodeId, $nbLevel, $language = null)
    {
        $node = $this->findOneByNodeIdAndLanguageWithPublishedAndLastVersionAndSiteId($nodeId, $language);

        $list = array();
        $list[] = $node;
        $list = array_merge($list, $this->getTreeParentIdLevelAndLanguage($node->getNodeId(), $nbLevel, $language));

        return $list;
    }

    /**
     * @param string      $nodeId
     * @param string|null $language
     *
     * @return mixed
     */
    public function findOneByNodeIdAndLanguageWithPublishedAndLastVersionAndSiteId($nodeId, $language = null)
    {
        $qb = $this->buildTreeRequest($language);

        $qb->field('nodeId')->equals($nodeId);
        $qb->sort('version', 'desc');

        return $qb->getQuery()->getSingleResult();
    }

    /**
     * @param string      $nodeId
     * @param string|null $language
     * @param int|null    $version
     *
     * @return mixed
     */
    public function findOneByNodeIdAndLanguageAndVersionAndSiteId($nodeId, $language = null, $version = null)
    {
        if (!is_null($version)) {
            $qb = $this->createQueryBuilderWithSiteIdAndLanguage(null, $language);
            $qb->field('nodeId')->equals($nodeId);
            $qb->field('deleted')->equals(false);
            $qb->field('version')->equals((int) $version);

            return $qb->getQuery()->getSingleResult();
        }

        return $this->findOneByNodeIdAndLanguageAndSiteIdAndLastVersion($nodeId, $language);
    }

    /**
     * @param string      $nodeId
     * @param string|null $language
     * @param string|null $siteId
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     *
     * @return mixed
     */
    public function findByNodeIdAndLanguageAndSiteId($nodeId, $language = null, $siteId = null)
    {
        $qb = $this->createQueryBuilderWithSiteIdAndLanguage($siteId, $language);
        $qb->field('nodeId')->equals($nodeId);

        return $qb->getQuery()->execute();
    }

    /**
     * @param string $nodeId
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     *
     * @return mixed
     */
    public function findByNodeIdAndSiteId($nodeId)
    {
        $qb = $this->createQueryBuilderWithSiteId();
        $qb->field('nodeId')->equals($nodeId);

        return $qb->getQuery()->execute();
    }

    /**
     * @param string $parentId
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     *
     * @return mixed
     */
    public function findByParentIdAndSiteId($parentId)
    {
        $qb = $this->createQueryBuilderWithSiteId();
        $qb->field('parentId')->equals($parentId);

        return $qb->getQuery()->execute();
    }

    /**
     * @param string $nodeId
     *
     * @deprecated This method is not precise
     *
     * @return mixed
     */
    public function findOneByNodeIdAndSiteIdAndLastVersion($nodeId)
    {
        $qb = $this->createQueryBuilderWithSiteId();
        $qb->field('nodeId')->equals($nodeId);
        $qb->field('deleted')->equals(false);
        $qb->sort('version', 'desc');

        $node = $qb->getQuery()->getSingleResult();

        return $node;
    }

    /**
     * @param string      $nodeId
     * @param string|null $language
     * @param string|null $siteId
     *
     * @return mixed
     */
    public function findOneByNodeIdAndLanguageAndSiteIdAndLastVersion($nodeId, $language = null, $siteId = null)
    {
        $qb = $this->createQueryBuilderWithSiteIdAndLanguage($siteId, $language);
        $qb->field('nodeId')->equals($nodeId);
        $qb->field('deleted')->equals(false);

        $qb->sort('version', 'desc');

        $node = $qb->getQuery()->getSingleResult();

        return $node;
    }

    /**
     * @param string $type
     * @param string $siteId
     *
     * @return array
     */
    public function findLastVersionBySiteId($type = NodeInterface::TYPE_DEFAULT, $siteId = null)
    {
        return $this->prepareFindLastVersion($type, $siteId, false);
    }

    /**
     * @param string $type
     *
     * @return array
     */
    public function findLastVersionByDeletedAndSiteId($type = NodeInterface::TYPE_DEFAULT)
    {
        return $this->prepareFindLastVersion($type, null, true);
    }

    /**
     * @param string $path
     *
     * @return Cursor
     */
    public function findChildsByPath($path)
    {
        $qb = $this->buildTreeRequest();
        $qb->field('path')->equals(new \MongoRegex('/'.preg_quote($path).'.+/'));

        return $qb->getQuery()->execute();
    }

    /**
     * @param string $parentId
     * @param int    $nbLevel
     * @param string $language
     *
     * @return array
     */
    protected function getTreeParentIdLevelAndLanguage($parentId, $nbLevel, $language = null)
    {
        $result = array();

        if ($nbLevel >= 0) {
            $qb = $this->buildTreeRequest($language);
            $qb->field('parentId')->equals($parentId);

            $nodes = $qb->getQuery()->execute();
            $result = $nodes->toArray();

            if (is_array($nodes->toArray())) {
                foreach ($nodes as $node) {
                    $temp = $this->getTreeParentIdLevelAndLanguage($node->getNodeId(), $nbLevel-1, $language);
                    $result = array_merge($result, $temp);
                }
            }
        }

        return $result;
    }

    /**
     * @param array $list
     *
     * @return array
     */
    protected function findLastVersion($list)
    {
        $nodes = array();

        foreach ($list as $node) {
            if (empty($nodes[$node->getNodeId()])) {
                $nodes[$node->getNodeId()] = $node;
                continue;
            }
            if ($nodes[$node->getNodeId()]->getVersion() < $node->getVersion()) {
                $nodes[$node->getNodeId()] = $node;
            }
        }

        return $nodes;
    }

    /**
     * @param string $name
     *
     * @return boolean
     */
    public function testUnicityInContext($name)
    {
        $qb = $this->createQueryBuilderWithSiteId();
        $qb->field('name')->equals($name);

        return count($qb->getQuery()->execute()) > 0;
    }

    /**
     * @param string $parentId
     * @param string $alias
     * @param string $siteId
     *
     * @return mixed
     */
    public function findOneByParendIdAndAliasAndSiteId($parentId, $alias, $siteId)
    {
        return $this->findOneBy(array(
            'parentId' => $parentId,
            'alias' => $alias,
            'siteId' => $siteId
        ));
    }

    /**
     * @param string $nodeId
     *
     * @return NodeInterface
     */
    public function findOneByNodeId($nodeId)
    {
        return $this->findOneBy(array('nodeId' => $nodeId));
    }

    /**
     * @param string $type
     *
     * @throws \Exception
     *
     * @return array
     */
    public function findByNodeType($type = NodeInterface::TYPE_DEFAULT)
    {
        return parent::findBy(array('nodeType' => $type));
    }

    /**
     * @param string $language
     * @param string $siteId
     *
     * @return array
     */
    public function findLastPublishedVersionByLanguageAndSiteId($language, $siteId)
    {
        $qb = $this->createQueryBuilderWithSiteIdAndLanguage($siteId, $language);
        $qb->field('status.published')->equals(true);
        $qb->field('nodeType')->equals(NodeInterface::TYPE_DEFAULT);

        $list = $qb->getQuery()->execute();

        return $this->findLastVersion($list);
    }

    /**
     * @param string|null $siteId
     *
     * @return \Doctrine\ODM\MongoDB\Query\Builder
     */
    protected function createQueryBuilderWithSiteId($siteId = null)
    {
        if (is_null($siteId)) {
            $siteId = $this->currentSiteManager->getCurrentSiteId();
        }
        $qb = $this->createQueryBuilder('n');
        $qb->field('siteId')->equals($siteId);

        return $qb;
    }

    /**
     * @param string|null $language
     *
     * @return \Doctrine\ODM\MongoDB\Query\Builder
     */
    protected function buildTreeRequest($language = null)
    {
        $qb = $this->createQueryBuilderWithSiteIdAndLanguage(null, $language);
        $qb->field('status.published')->equals(true);
        $qb->field('deleted')->equals(false);

        return $qb;
    }

    /**
     * @param string|null $siteId
     * @param string|null $language
     *
     * @return \Doctrine\ODM\MongoDB\Query\Builder
     */
    protected function createQueryBuilderWithSiteIdAndLanguage($siteId = null, $language = null)
    {
        $qb = $this->createQueryBuilderWithSiteId($siteId);
        if (is_null($language)) {
            $language = $this->currentSiteManager->getCurrentSiteDefaultLanguage();
        }
        $qb->field('language')->equals($language);

        return $qb;
    }

    /**
     * @param string $type
     * @param string $siteId
     * @param bool   $deleted
     *
     * @return array
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    protected function prepareFindLastVersion($type, $siteId = null, $deleted)
    {
        $qb = $this->createQueryBuilderWithSiteId($siteId);
        $qb->field('deleted')->equals($deleted);
        $qb->field('nodeType')->equals($type);

        $list = $qb->getQuery()->execute();

        return $this->findLastVersion($list);
    }

    /**
     * @param string|null $language
     * @param string      $field
     *
     * @return array
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    protected function getTreeByLanguageAndField($language = null, $field)
    {
        $qb = $this->buildTreeRequest($language);
        $qb->field($field)->equals(true);

        $list = $qb->getQuery()->execute();

        return $this->findLastVersion($list);
    }
}
