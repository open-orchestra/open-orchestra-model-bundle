<?php

namespace OpenOrchestra\ModelBundle\Repository;

use Doctrine\ODM\MongoDB\Cursor;
use Doctrine\ODM\MongoDB\Mapping;
use OpenOrchestra\ModelBundle\Repository\RepositoryTrait\AreaFinderTrait;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\ReadNodeInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\FieldAutoGenerableRepositoryInterface;
use Doctrine\ODM\MongoDB\Query\Builder;
use Solution\MongoAggregation\Pipeline\Stage;

/**
 * Class NodeRepository
 */
class NodeRepository extends AbstractRepository implements FieldAutoGenerableRepositoryInterface, NodeRepositoryInterface
{
    use AreaFinderTrait;

    /**
     * @param string $language
     * @param string $siteId
     *
     * @return array
     */
    public function getFooterTreeByLanguageAndSiteId($language, $siteId)
    {
        return $this->getTreeByLanguageAndFieldAndSiteId($language, 'inFooter', $siteId);
    }

    /**
     * @param string $language
     * @param string $siteId
     *
     * @return array
     */
    public function getMenuTreeByLanguageAndSiteId($language, $siteId)
    {
        return $this->getTreeByLanguageAndFieldAndSiteId($language, 'inMenu', $siteId);
    }

    /**
     * @param string $nodeId
     * @param int    $nbLevel
     * @param string $language
     * @param string $siteId
     *
     * @return array
     */
    public function getSubMenuByNodeIdAndNbLevelAndLanguageAndSiteId($nodeId, $nbLevel, $language, $siteId)
    {
        $node = $this->findOneByNodeIdAndLanguageWithPublishedAndLastVersionAndSiteId($nodeId, $language, $siteId);

        $list = array();
        $list[] = $node;
        $list = array_merge($list, $this->getTreeParentIdLevelAndLanguage($node->getNodeId(), $nbLevel, $language, $siteId));

        return $list;
    }

    /**
     * @param string $nodeId
     * @param string $language
     * @param string $siteId
     *
     * @return mixed
     */
    public function findOneByNodeIdAndLanguageWithPublishedAndLastVersionAndSiteId($nodeId, $language, $siteId)
    {
        $qb = $this->createQueryBuilderWithSiteIdAndLanguage($siteId, $language);
        if ($nodeId !== NodeInterface::TRANSVERSE_NODE_ID) {
            $qb->field('status.published')->equals(true);
        }
        $qb->field('deleted')->equals(false);

        $qb->field('nodeId')->equals($nodeId);
        $qb->sort('version', 'desc');

        return $qb->getQuery()->getSingleResult();
    }

    /**
     * @param string   $nodeId
     * @param string   $language
     * @param string   $siteId
     * @param int|null $version
     *
     * @return mixed
     */
    public function findOneByNodeIdAndLanguageAndVersionAndSiteId($nodeId, $language, $siteId, $version = null)
    {
        if (!is_null($version)) {
            $qb = $this->createQueryBuilderWithSiteIdAndLanguage($siteId, $language);
            $qb->field('nodeId')->equals($nodeId);
            $qb->field('deleted')->equals(false);
            $qb->field('version')->equals((int) $version);

            return $qb->getQuery()->getSingleResult();
        }

        return $this->findOneByNodeIdAndLanguageAndSiteIdAndLastVersion($nodeId, $language, $siteId);
    }

    /**
     * @param string $nodeId
     * @param string $language
     * @param string $siteId
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     *
     * @return mixed
     */
    public function findByNodeIdAndLanguageAndSiteId($nodeId, $language, $siteId)
    {
        $qb = $this->createQueryBuilderWithSiteIdAndLanguage($siteId, $language);
        $qb->field('nodeId')->equals($nodeId);

        return $qb->getQuery()->execute();
    }

    /**
     * @param string $nodeId
     * @param string $language
     * @param string $siteId
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     *
     * @return mixed
     */
    public function findByNodeIdAndLanguageAndSiteIdAndPublishedOrderedByVersion($nodeId, $language, $siteId)
    {
        $qb = $this->createQueryBuilderWithSiteIdAndLanguage($siteId, $language);
        $qb->field('nodeId')->equals($nodeId);
        $qb->field('status.published')->equals(true);
        $qb->sort('version', 'desc');

        return $qb->getQuery()->execute();
    }

    /**
     * @param string $nodeId
     * @param string $siteId
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     *
     * @return mixed
     */
    public function findByNodeIdAndSiteId($nodeId, $siteId)
    {
        $qb = $this->createQueryBuilderWithSiteId($siteId);
        $qb->field('nodeId')->equals($nodeId);

        return $qb->getQuery()->execute();
    }

    /**
     * @param string $parentId
     * @param string $siteId
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     *
     * @return mixed
     */
    public function findByParentIdAndSiteId($parentId, $siteId)
    {
        $qb = $this->createQueryBuilderWithSiteId($siteId);
        $qb->field('parentId')->equals($parentId);

        return $qb->getQuery()->execute();
    }

    /**
     * @param string $nodeId
     * @param string $language
     * @param string $siteId
     *
     * @return mixed
     */
    public function findOneByNodeIdAndLanguageAndSiteIdAndLastVersion($nodeId, $language, $siteId)
    {
        $qb = $this->createQueryBuilderWithSiteIdAndLanguage($siteId, $language);
        $qb->field('nodeId')->equals($nodeId);
        $qb->field('deleted')->equals(false);

        $qb->sort('version', 'desc');

        $node = $qb->getQuery()->getSingleResult();

        return $node;
    }

    /**
     * @param string $siteId
     * @param string $type
     *
     * @return array
     */
    public function findLastVersionBySiteId($siteId, $type = NodeInterface::TYPE_DEFAULT)
    {
        return $this->prepareFindLastVersion($type, $siteId, false);
    }

    /**
     * @param string $siteId
     * @param string $type
     *
     * @return array
     */
    public function findLastVersionByDeletedAndSiteId($siteId, $type = NodeInterface::TYPE_DEFAULT)
    {
        return $this->prepareFindLastVersion($type, $siteId, true);
    }

    /**
     * @param string $path
     * @param string $siteId
     * @param string $language
     *
     * @return mixed
     */
    public function findChildsByPathAndSiteIdAndLanguage($path, $siteId, $language)
    {
        $qb = $this->buildTreeRequest($language, $siteId);
        $qb->field('path')->equals(new \MongoRegex('/'.preg_quote($path).'.+/'));

        return $qb->getQuery()->execute();
    }

    /**
     * @param string $parentId
     * @param int    $nbLevel
     * @param string $language
     * @param string $siteId
     *
     * @return array
     */
    protected function getTreeParentIdLevelAndLanguage($parentId, $nbLevel, $language, $siteId)
    {
        $result = array();

        if ($nbLevel >= 0) {
            $qb = $this->buildTreeRequest($language, $siteId);
            $qb->field('parentId')->equals($parentId);

            $nodes = $qb->getQuery()->execute();
            $result = $nodes->toArray();

            if (is_array($nodes->toArray())) {
                foreach ($nodes as $node) {
                    $temp = $this->getTreeParentIdLevelAndLanguage($node->getNodeId(), $nbLevel-1, $language, $siteId);
                    $result = array_merge($result, $temp);
                }
            }
        }

        return $result;
    }

    /**
     * @param Stage $qa
     *
     * @return array
     */
    protected function findLastVersion(Stage $qa)
    {
        $elementName = 'node';
        $qa->group(array(
            '_id' => array('nodeId' => '$nodeId'),
            'version' => array('$max' => '$version'),
            $elementName => array('$last' => '$$ROOT')
        ));

        return $this->hydrateAggregateQuery($qa, $elementName, 'getNodeId');
    }

    /**
     * @param string $name
     *
     * @return boolean
     */
    public function testUnicityInContext($name)
    {
        $qb = $this->createQueryBuilder();
        $qb->field('name')->equals($name);

        return count($qb->getQuery()->execute()) > 0;
    }

    /**
     * @param string $parentId
     * @param string $routePattern
     * @param string $siteId
     *
     * @deprecated Used in dynamic routing only
     *
     * @return mixed
     */
    public function findOneByParendIdAndRoutePatternAndSiteId($parentId, $routePattern, $siteId)
    {
        return $this->findOneBy(array(
            'parentId' => $parentId,
            'routePattern' => $routePattern,
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
        $qa = $this->createAggregationQuery();
        $qa->match(
            array(
                'siteId'=> $siteId,
                'language'=> $language,
                'status.published' => true,
                'nodeType' => NodeInterface::TYPE_DEFAULT
            )
        );

        return $this->findLastVersion($qa);
    }

    /**
     * @param string $siteId
     *
     * @return \Doctrine\ODM\MongoDB\Query\Builder
     */
    protected function createQueryBuilderWithSiteId($siteId)
    {
        $qb = $this->createQueryBuilder();
        $qb->field('siteId')->equals($siteId);

        return $qb;
    }

    /**
     * @param string $language
     * @param string $siteId
     *
     * @return \Doctrine\ODM\MongoDB\Query\Builder
     */
    protected function buildTreeRequest($language, $siteId)
    {
        $qb = $this->createQueryBuilderWithSiteIdAndLanguage($siteId, $language);
        $qb->field('status.published')->equals(true);
        $qb->field('deleted')->equals(false);

        return $qb;
    }

    /**
     * @param string $siteId
     * @param string $language
     *
     * @return \Doctrine\ODM\MongoDB\Query\Builder
     */
    protected function createQueryBuilderWithSiteIdAndLanguage($siteId, $language)
    {
        $qb = $this->createQueryBuilderWithSiteId($siteId);
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
    protected function prepareFindLastVersion($type, $siteId, $deleted)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(
            array(
                'siteId' => $siteId,
                'deleted' => $deleted,
                'nodeType' => $type
            )
        );

        return $this->findLastVersion($qa);
    }

    /**
     * @param string $language
     * @param string $field
     * @param string $siteId
     *
     * @return array
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    protected function getTreeByLanguageAndFieldAndSiteId($language, $field, $siteId)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(
            array(
                'siteId' => $siteId,
                'language' => $language,
                'status.published' => true,
                'deleted' => false,
                $field => true
            )
        );

        return $this->findLastVersion($qa);
    }

    /**
     * @param string $parentId
     * @param string $routePattern
     * @param string $nodeId
     * @param string $siteId
     *
     * @return array
     */
    public function findByParentIdAndRoutePatternAndNotNodeIdAndSiteId($parentId, $routePattern, $nodeId, $siteId)
    {
        $qb = $this->createQueryBuilderWithSiteId($siteId);
        $qb->field('parentId')->equals($parentId);
        $qb->field('routePattern')->equals($routePattern);
        $qb->field('nodeId')->notEqual($nodeId);

        return $qb->getQuery()->execute();
    }

    /**
     * @return ReadNodeInterface
     */
    public function findLastPublished()
    {
        $qb = $this->createQueryBuilder();
        $qb->field('status.published')->equals(true);
        $qb->field('deleted')->equals(false);
        $qb->sort('updatedAt', 'desc');

        return $qb->getQuery()->getSingleResult();
    }
}
