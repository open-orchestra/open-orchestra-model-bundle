<?php

namespace OpenOrchestra\ModelBundle\Repository;

use Doctrine\ODM\MongoDB\Mapping;
use OpenOrchestra\ModelBundle\Repository\RepositoryTrait\AreaFinderTrait;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\ReadNodeInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\FieldAutoGenerableRepositoryInterface;
use Solution\MongoAggregation\Pipeline\Stage;
use OpenOrchestra\Repository\AbstractAggregateRepository;

/**
 * Class NodeRepository
 */
class NodeRepository extends AbstractAggregateRepository implements FieldAutoGenerableRepositoryInterface, NodeRepositoryInterface
{
    use AreaFinderTrait;

    /**
     * @param string $mongoId
     * 
     * @return \OpenOrchestra\ModelInterface\Model\NodeInterface
     */
    public function findOneById($mongoId)
    {
        return $this->find(new \MongoId($mongoId));
    }

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
        $node = $this->findOnePublishedByNodeIdAndLanguageAndSiteIdInLastVersion($nodeId, $language, $siteId);

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
     * @deprecated will be removed in 0.3.0, use findOnePublishedByNodeIdAndLanguageAndSiteIdInLastVersion instead
     *
     * @return mixed
     */
    public function findOneByNodeIdAndLanguageWithPublishedAndLastVersionAndSiteId($nodeId, $language, $siteId)
    {
        return $this->findOnePublishedByNodeIdAndLanguageAndSiteIdInLastVersion($nodeId, $language, $siteId);
    }

    /**
     * @param string $nodeId
     * @param string $language
     * @param string $siteId
     *
     * @return mixed
     */
    public function findOnePublishedByNodeIdAndLanguageAndSiteIdInLastVersion($nodeId, $language, $siteId)
    {
        $qa = $this->createAggregationQueryBuilderWithSiteIdAndLanguage($siteId, $language);
        $filter = array();
        if ($nodeId !== NodeInterface::TRANSVERSE_NODE_ID) {
            $filter['status.published'] = true;
        }
        $filter['deleted'] = false;
        $filter['nodeId'] = $nodeId;
        $qa->match($filter);
        $qa->sort(array('version' => -1));

        return $this->singleHydrateAggregateQuery($qa);
    }

    /**
     * @param string   $nodeId
     * @param string   $language
     * @param string   $siteId
     * @param int|null $version
     *
     * @deprecated will be removed in 0.3.0, use findOneByNodeIdAndLanguageAndSiteIdAndVersion instead
     *
     * @return array
     */
    public function findOneByNodeIdAndLanguageAndVersionAndSiteId($nodeId, $language, $siteId, $version = null)
    {
        return $this->findOneByNodeIdAndLanguageAndSiteIdAndVersion($nodeId, $language, $siteId, $version);
    }

    /**
     * @param string   $nodeId
     * @param string   $language
     * @param string   $siteId
     * @param int|null $version
     *
     * @return array
     */
    public function findOneByNodeIdAndLanguageAndSiteIdAndVersion($nodeId, $language, $siteId, $version = null)
    {
        if (!is_null($version)) {
            $qa = $this->createAggregationQueryBuilderWithSiteIdAndLanguage($siteId, $language);
            $qa->match(
                array(
                    'nodeId'  => $nodeId,
                    'deleted' => false,
                    'version' => (int) $version,
                )
            );
            return $this->singleHydrateAggregateQuery($qa);
        }

        return $this->findOneByNodeIdAndLanguageAndSiteIdInLastVersion($nodeId, $language, $siteId);
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
        $qa = $this->createAggregationQueryBuilderWithSiteIdAndLanguage($siteId, $language);
        $qa->match(array('nodeId' => $nodeId));

        return $this->hydrateAggregateQuery($qa);
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
        $qa = $this->createAggregationQueryBuilderWithSiteIdAndLanguage($siteId, $language);
        $qa->match(
            array(
                'nodeId'  => $nodeId,
                'status.published' => true,
            )
        );
        $qa->sort(array('version' => -1));

        return $this->hydrateAggregateQuery($qa);
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
        $qa = $this->createAggregationQueryBuilderWithSiteId($siteId);
        $qa->match(array('nodeId' => $nodeId));

        return $this->hydrateAggregateQuery($qa);
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
        $qa = $this->createAggregationQueryBuilderWithSiteId($siteId);
        $qa->match(array('parentId' => $parentId));

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param string $nodeId
     * @param string $language
     * @param string $siteId
     *
     * @deprecated will be removed in 0.3.0, use findOneByNodeIdAndLanguageAndSiteIdInLastVersion instead
     *
     * @return mixed
     */
    public function findOneByNodeIdAndLanguageAndSiteIdAndLastVersion($nodeId, $language, $siteId)
    {
        return $this->findOneByNodeIdAndLanguageAndSiteIdInLastVersion($nodeId, $language, $siteId);
    }

    /**
     * @param string $nodeId
     * @param string $language
     * @param string $siteId
     *
     * @return mixed
     */
    public function findOneByNodeIdAndLanguageAndSiteIdInLastVersion($nodeId, $language, $siteId)
    {
        $qa = $this->createAggregationQueryBuilderWithSiteIdAndLanguage($siteId, $language);
        $qa->match(
            array(
                'nodeId'  => $nodeId,
                'deleted' => false,
            )
        );
        $qa->sort(array('version' => -1));

        return $this->singleHydrateAggregateQuery($qa);
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
     * @deprecated will be removed in 0.3.0, use findDeletedInLastVersionBySiteId instead
     *
     * @return array
     */
    public function findLastVersionByDeletedAndSiteId($siteId, $type = NodeInterface::TYPE_DEFAULT)
    {
        return $this->findDeletedInLastVersionBySiteId($siteId, $type);
    }

    /**
     * @param string $siteId
     * @param string $type
     *
     * @return array
     */
    public function findDeletedInLastVersionBySiteId($siteId, $type = NodeInterface::TYPE_DEFAULT)
    {
        return $this->prepareFindLastVersion($type, $siteId, true);
    }

    /**
     * @param string $path
     * @param string $siteId
     * @param string $language
     *
     * @deprecated will be removed in 0.3.0, use findChildByPathAndSiteIdAndLanguage instead
     *
     * @return mixed
     */
    public function findChildsByPathAndSiteIdAndLanguage($path, $siteId, $language)
    {
        return $this->findChildrenByPathAndSiteIdAndLanguage($path, $siteId, $language);
    }

    /**
     * @param string $path
     * @param string $siteId
     * @param string $language
     *
     * @return mixed
     */
    public function findChildrenByPathAndSiteIdAndLanguage($path, $siteId, $language)
    {
        $qa = $this->buildTreeRequest($language, $siteId);
        $qa->match(array('path' => new \MongoRegex('/'.preg_quote($path).'.+/')));

        return $this->hydrateAggregateQuery($qa);
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
            $qa = $this->buildTreeRequest($language, $siteId);
            $qa->match(array('parentId' => $parentId));

            $nodes = $this->hydrateAggregateQuery($qa);
            $result = $nodes;

            if (is_array($nodes)) {
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
    public function testUniquenessInContext($name)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(array('name' => $name));

        return $this->countDocumentAggregateQuery($qa) > 0;
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
     * @return Stage
     */
    protected function createAggregationQueryBuilderWithSiteId($siteId)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(array('siteId' => $siteId));

        return $qa;
    }

    /**
     * @param string $language
     * @param string $siteId
     *
     * @return Stage
     */
    protected function buildTreeRequest($language, $siteId)
    {
        $qa = $this->createAggregationQueryBuilderWithSiteIdAndLanguage($siteId, $language);
        $qa->match(
            array(
                'status.published' => true,
                'deleted' => false,
            )
        );

        return $qa;
    }

    /**
     * @param string $siteId
     * @param string $language
     *
     * @return Stage
     */
    protected function createAggregationQueryBuilderWithSiteIdAndLanguage($siteId, $language)
    {
        $qa = $this->createAggregationQueryBuilderWithSiteId($siteId);
        $qa->match(array('language' => $language));

        return $qa;
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
     * @will be removed in 0.3.0, use findByParentIdAndRoutePatternAndNodeIdAndSiteId instead
     *
     * @return array
     */
    public function findByParentIdAndRoutePatternAndNotNodeIdAndSiteId($parentId, $routePattern, $nodeId, $siteId)
    {
        return $this->findByParentIdAndRoutePatternAndNodeIdAndSiteId($parentId, $routePattern, $nodeId, $siteId);
    }

    /**
     * @param string $parentId
     * @param string $routePattern
     * @param string $nodeId
     * @param string $siteId
     *
     * @return array
     */
    public function findByParentIdAndRoutePatternAndNodeIdAndSiteId($parentId, $routePattern, $nodeId, $siteId)
    {
        $qa = $this->createAggregationQueryBuilderWithSiteId($siteId);
        $qa->match(
            array(
                'parentId'     => $parentId,
                'routePattern' => $routePattern,
                'nodeId'       => array('$ne' => $nodeId),
            )
        );

        return $this->hydrateAggregateQuery($qa);
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
