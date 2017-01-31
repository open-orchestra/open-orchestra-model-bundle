<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\ModelInterface\Model\AreaInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\ReadNodeInterface;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\FieldAutoGenerableRepositoryInterface;
use OpenOrchestra\ModelBundle\Repository\RepositoryTrait\AutoPublishableTrait;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use Solution\MongoAggregation\Pipeline\Stage;
use OpenOrchestra\Repository\AbstractAggregateRepository;
use MongoRegex;

/**
 * Class NodeRepository
 */
class NodeRepository extends AbstractAggregateRepository implements FieldAutoGenerableRepositoryInterface, NodeRepositoryInterface
{
    use AutoPublishableTrait;

    /**
     * @param $node  NodeInterface
     * @param string $areaId
     *
     * @return null|AreaInterface
     */
    public function findAreaInNodeByAreaId(NodeInterface $node, $areaId)
    {
        foreach ($node->getAreas() as $key => $area) {
            if ($areaId === $key) {
                return $area;
            }
        }

        return null;
    }

    /**
     * @param string $entityId
     *
     * @return NodeInterface
     */
    public function findVersionByDocumentId($entityId)
    {
        return $this->find(new \MongoId($entityId));
    }

    /**
     * @param string $language
     * @param string $siteId
     *
     * @return array
     */
    public function getFooterTree($language, $siteId)
    {
        return $this->getTreeByLanguageAndFieldAndSiteId($language, 'inFooter', $siteId);
    }

    /**
     * @param string $language
     * @param string $siteId
     *
     * @return array
     */
    public function getMenuTree($language, $siteId)
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
    public function getSubMenu($nodeId, $nbLevel, $language, $siteId)
    {
        $node = $this->findOnePublished($nodeId, $language, $siteId);
        $list = array();

        if ($node instanceof ReadNodeInterface) {
            $list[] = $node;
            $list = array_merge($list, $this->getTreeParentIdLevelAndLanguage($node->getNodeId(), $nbLevel, $language, $siteId));
        }

        return $list;
    }

    /**
     * @param string $nodeId
     * @param string $language
     * @param string $siteId
     *
     * @return mixed
     */
    public function findOnePublished($nodeId, $language, $siteId)
    {
        $qa = $this->createAggregationQueryBuilderWithSiteIdAndLanguage($siteId, $language);
        $filter = array(
            'status.publishedState' => true,
            'deleted' => false,
            'nodeId' => $nodeId,
        );
        $qa->match($filter);

        return $this->singleHydrateAggregateQuery($qa);
    }

    /**
     * @param string   $nodeId
     * @param string   $language
     * @param string   $siteId
     * @param int|null $version
     *
     * @return mixed
     */
    public function findVersion($nodeId, $language, $siteId, $version = null)
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

        return $this->findInLastVersion($nodeId, $language, $siteId);
    }

    /**
     * @param string $nodeId
     * @param string $language
     * @param string $siteId
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function findByNodeAndLanguageAndSite($nodeId, $language, $siteId)
    {
        $qa = $this->createAggregationQueryBuilderWithSiteIdAndLanguage($siteId, $language);
        $qa->match(array('nodeId' => $nodeId));
        $qa->sort(array('version' => -1));

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param string $nodeId
     * @param string $language
     * @param string $siteId
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function findPublishedSortedByVersion($nodeId, $language, $siteId)
    {
        $qa = $this->createAggregationQueryBuilderWithSiteIdAndLanguage($siteId, $language);
        $qa->match(
            array(
                'nodeId'  => $nodeId,
                'status.publishedState' => true,
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
    public function findByNodeAndSite($nodeId, $siteId)
    {
        $qa = $this->createAggregationQueryBuilderWithSiteId($siteId);
        $qa->match(array('nodeId' => $nodeId));

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param string $nodeId
     * @param string $siteId
     * @param string $areaId
     *
     * @return array
     */
    public function findByNodeIdAndSiteIdWithBlocksInArea($nodeId, $siteId, $areaId)
    {
        $qa = $this->createAggregationQueryBuilderWithSiteId($siteId);
        $qa->match(array(
                'nodeId' => $nodeId,
                'areas.'.$areaId.'.blocks.0' => array('$exists' => true),
        ));

        return $this->findLastVersionInLanguage($qa);
    }

    /**
     * @param string $nodeId
     * @param string $siteId
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     *
     * @return mixed
     */
    public function findOneByNodeAndSite($nodeId, $siteId)
    {
        $qa = $this->createAggregationQueryBuilderWithSiteId($siteId);
        $qa->match(array('nodeId' => $nodeId));

        return $this->singleHydrateAggregateQuery($qa);
    }

    /**
     * @param string $nodeId
     * @param string $siteId
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     *
     * @return mixed
     */
    public function findByNodeAndSiteSortedByVersion($nodeId, $siteId)
    {
        $qa = $this->createAggregationQueryBuilderWithSiteId($siteId);
        $qa->match(array('nodeId' => $nodeId));

        $qa->sort(array('version' => -1));

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param string $parentId
     * @param string $siteId
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function findByParent($parentId, $siteId)
    {
        $qa = $this->createAggregationQueryBuilderWithSiteId($siteId);
        $qa->match(array('parentId' => $parentId));

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param string $parentId
     * @param string $siteId
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function findOneByParentWithGreatestOrder($parentId, $siteId)
    {
        $qa = $this->createAggregationQueryBuilderWithSiteId($siteId);
        $qa->match(array('parentId' => $parentId));

        $qa->sort(array('order' => -1));

        return $this->singleHydrateAggregateQuery($qa);
    }

    /**
     * @param string $path
     * @param string $siteId
     * @param string $language
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     *
     * @return array
     */
    public function findByIncludedPathSiteIdAndLanguage($path, $siteId, $language)
    {
        $qa = $this->createAggregationQueryBuilderWithSiteId($siteId);
        $qa->match(array('language' => $language));
        $qa->match(array('path' => new MongoRegex('/^'.$path.'(\/.*)?$/')));

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param string $path
     * @param string $siteId
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     *
     * @return mixed
     */
    public function findByIncludedPathAndSiteId($path, $siteId)
    {
        $qa = $this->createAggregationQueryBuilderWithSiteId($siteId);
        $qa->match(array('path' => new MongoRegex('/^'.$path.'(\/.*)?$/')));

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param string $nodeId
     * @param string $language
     * @param string $siteId
     *
     * @return mixed
     */
    public function findInLastVersion($nodeId, $language, $siteId)
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
    public function findLastVersionByType($siteId, $type = NodeInterface::TYPE_DEFAULT)
    {
        return $this->prepareFindLastVersion($type, $siteId, false);
    }

    /**
     * @param string $siteId
     * @param string $language
     * @param string $parentId
     *
     * @return array
     */
    public function findTreeNode($siteId, $language, $parentId)
    {
        $qa = $this->createAggregationQueryBuilderWithSiteIdAndLanguage($siteId, $language);
        if (NodeInterface::ROOT_PARENT_ID !== $parentId ) {
            $qa->match(array('path' => new MongoRegex('/'.$parentId.'(\/.*)?$/')));
        }
        $qa->match(array('deleted' => false));

        $qa->sort(array('version' => 1));
        $elementName = 'node';
        $qa->group(array(
            '_id' => array('nodesId' => '$nodeId'),
            'version' => array('$last' => '$version'),
            'order' => array('$last' => '$order'),
            'nodeId' => array('$last' => '$nodeId'),
            $elementName => array('$last' => '$$ROOT')
        ));
        $qa->sort(array('order' => 1));
        $nodes = $qa->getQuery()->aggregate()->toArray();

        return $this->generateTree($nodes, $parentId);
    }

    /**
     * @param string $siteId
     * @param string $type
     *
     * @return array
     */
    public function findPublishedByType($siteId, $type = NodeInterface::TYPE_DEFAULT)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(
            array(
                'siteId' => $siteId,
                'deleted' => false,
                'nodeType' => $type,
                'status.publishedState' => true,
            )
        );

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param string $path
     * @param string $siteId
     * @param string $language
     *
     * @return array
     */
    public function findPublishedByPathAndLanguage($path, $siteId, $language)
    {
        $qa = $this->createAggregationQueryBuilderWithSiteId($siteId);
        $qa->match(
            array(
                'path' => new MongoRegex('/^'.$path.'(\/.*)?$/'),
                'status.publishedState' => true,
                'deleted' => false,
                'language' => $language,
            )
        );

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param string $path
     * @param string $siteId
     * @param string $language
     *
     * @return mixed
     */
    public function findSubTreeByPath($path, $siteId, $language)
    {
        $qa = $this->buildTreeRequest($language, $siteId);
        $qa->match(array('path' => new \MongoRegex('/'.preg_quote($path).'.+/')));

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param PaginateFinderConfiguration $configuration
     * @param string                      $siteId
     * @param string                      $language
     *
     * @return array
     */
    public function findForPaginate(PaginateFinderConfiguration $configuration, $siteId, $language)
    {
        $elementName = 'node';
        $order = $configuration->getOrder();
        $qa = $this->createQueryWithFilterAndLastVersion($configuration, $siteId, $language, $elementName, $order);

        $qa->skip($configuration->getSkip());
        $qa->limit($configuration->getLimit());

        return $this->hydrateAggregateQuery($qa, $elementName);
    }

    /**
     * @param PaginateFinderConfiguration $configuration
     * @param string                      $siteId
     * @param string                      $language
     * @param string                      $blockId
     *
     * @return array
     */
    public function findWithBlockUsedForPaginate(PaginateFinderConfiguration $configuration, $siteId, $language, $blockId)
    {
        $qb = $this->createQueryBuilder();
        $qb->field('language')->equals($language);
        $qb->field('siteId')->equals($siteId);

        $functionFilter = $this->generateFunctionFilterBlock($blockId);
        $qb->where($functionFilter);

        $order = $configuration->getOrder();
        if (!empty($order)) {
            $qb->sort($order);
        }

        $qb->skip($configuration->getSkip());
        $qb->limit($configuration->getLimit());

        return $qb->getQuery()->execute();
    }

    /**
     * @param string  $siteId
     * @param string  $language
     *
     * @return int
     */
    public function count($siteId, $language)
    {
        $qa = $this->createAggregationQueryBuilderWithSiteIdAndLanguage($siteId, $language);
        $qa->match(array('deleted' => false));
        $elementName = 'node';
        $qa->sort(array('version' => 1));
        $qa->group(array(
            '_id' => array('nodeId' => '$nodeId'),
            $elementName => array('$last' => '$$ROOT')
        ));

        return $this->countDocumentAggregateQuery($qa);
    }

    /**
     * @param string  $siteId
     * @param string  $language
     * @param string  $blockId
     *
     * @return int
     */
    public function countWithBlockUsed($siteId, $language, $blockId)
    {
        $qb = $this->createQueryBuilder();
        $qb->field('language')->equals($language);
        $qb->field('siteId')->equals($siteId);

        $functionFilter = $this->generateFunctionFilterBlock($blockId);
        $qb->where($functionFilter);

        return $qb->getQuery()->execute()->count();
    }

    /**
     * @param PaginateFinderConfiguration $configuration
     * @param string                      $siteId
     * @param string                      $language
     *
     * @return int
     */
    public function countWithFilter(PaginateFinderConfiguration $configuration, $siteId, $language)
    {
        $elementName = 'node';
        $qa = $this->createQueryWithFilterAndLastVersion($configuration, $siteId, $language, $elementName);

        return $this->countDocumentAggregateQuery($qa);
    }

    /**
     * @param string $siteId
     * @param string $nodeId
     * @param int    $order
     * @param string $parentId
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function updateOrderOfBrothers($siteId, $nodeId, $order, $parentId)
    {
        $this->createQueryBuilder()
            ->updateMany()
            ->field('nodeId')->notEqual($nodeId)
            ->field('siteId')->equals($siteId)
            ->field('parentId')->equals($parentId)
            ->field('order')->gte($order)
            ->field('order')->inc(1)
            ->getQuery()
            ->execute();
    }

    /**
     * @param string $blockId
     *
     * @return \MongoCode
     */
    protected function generateFunctionFilterBlock($blockId)
    {
        return new \MongoCode(
            'function() {
                for (var areaIndex in this.areas)
                    for (var key in this.areas[areaIndex].blocks)
                        if (this.areas[areaIndex].blocks[key].$id == "'.$blockId.'")
                            return this;
            }'
        );
    }

    /**
     * @param PaginateFinderConfiguration $configuration
     * @param string                      $siteId
     * @param string                      $language
     * @param string                      $elementName
     * @param array                       $order
     *
     * @return Stage
     */
    protected function createQueryWithFilterAndLastVersion(
        PaginateFinderConfiguration $configuration,
        $siteId,
        $language,
        $elementName,
        $order = array()
    ){
        $qa = $this->createAggregationQueryBuilderWithSiteIdAndLanguage($siteId, $language);
        $qa->match(array('deleted' => false));
        $filters = $this->getFilterSearch($configuration);
        if (!empty($filters)) {
            $qa->match($filters);
        }

        $qa->sort(array('version' => 1));

        $group = array(
            '_id' => array('nodeId' => '$nodeId'),
            $elementName => array('$last' => '$$ROOT')
        );
        $groupOrder = array();
        foreach ($order as $name => $dir) {
            $nameOrder = str_replace('.', '_', $name);
            $groupOrder[$nameOrder] = $dir;
            $group[$nameOrder] = array('$last' => '$'.$name);
        }
        $qa->group($group);

        if (!empty($groupOrder)) {
            $qa->sort($groupOrder);
        }

        return $qa;
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
        $qa->sort(array('version' => 1));
        $qa->group(array(
            '_id' => array('nodeId' => '$nodeId'),
            $elementName => array('$last' => '$$ROOT')
        ));

        return $this->hydrateAggregateQuery($qa, $elementName, 'getNodeId');
    }

    /**
     * @param Stage $qa
     *
     * @return array
     */
    protected function findLastVersionInLanguage(Stage $qa)
    {
        $elementName = 'node';
        $qa->sort(array('version' => 1));
        $qa->group(array(
            '_id' => array('nodeId' => '$nodeId', 'language' => '$language'),
            $elementName => array('$last' => '$$ROOT')
        ));

        return $this->hydrateAggregateQuery($qa, $elementName);
    }

    /**
     * @param string $nodeId
     *
     * @return boolean
     */
    public function testUniquenessInContext($nodeId)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(array('nodeId' => $nodeId));

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
     * @param string $siteId
     *
     * @throws \Exception
     *
     * @return array
     */
    public function findByNodeTypeAndSite($type, $siteId)
    {
        return parent::findBy(array('nodeType' => $type, 'siteId' => $siteId));
    }

    /**
     * @param string $nodeId
     * @param string $type
     * @param string $siteId
     *
     * @throws \Exception
     *
     * @return array
     */
    public function findByNodeIdAndNodeTypeAndSite($nodeId, $type, $siteId)
    {
        return parent::findBy(array('nodeId' => $nodeId, 'nodeType' => $type, 'siteId' => $siteId));
    }

    /**
     * @param string $language
     * @param string $siteId
     *
     * @return ReadNodeInterface
     */
    public function findOnePublishedByLanguageAndSiteId($language, $siteId)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(
            array(
                'siteId'=> $siteId,
                'language'=> $language,
                'status.publishedState' => true,
                'nodeType' => NodeInterface::TYPE_DEFAULT
            )
        );

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param StatusableInterface $element
     *
     * @return array
     */
    public function findPublished(StatusableInterface $element)
    {
        $qa = $this->createAggregationQueryBuilderWithSiteIdAndLanguage($element->getSiteId(), $element->getLanguage());
        $filter = array(
            'status.publishedState' => true,
            'deleted' => false,
            'nodeId' => $element->getNodeId(),
        );
        $qa->match($filter);

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param string $parentId
     * @param string $routePattern
     * @param string $nodeId
     * @param string $siteId
     *
     * @return array
     */
    public function findByParentAndRoutePattern($parentId, $routePattern, $nodeId, $siteId)
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
     * @param string $parentId
     * @param int    $order
     * @param string $nodeId
     * @param string $siteId
     *
     * @return bool
     */
    public function hasOtherNodeWithSameParentAndOrder($parentId, $order, $nodeId, $siteId)
    {
        $qa = $this->createAggregationQueryBuilderWithSiteId($siteId);
        $qa->match(
            array(
                'parentId' => $parentId,
                'order'    => $order,
                'nodeId'   => array('$ne' => $nodeId),
                'deleted'  => false,
                'nodeType' => NodeInterface::TYPE_DEFAULT
            )
        );
        $node = $this->singleHydrateAggregateQuery($qa);

        return $node instanceof NodeInterface;
    }

    /**
     * @param string $parentId
     * @param int    $order
     * @param string $siteId
     *
     * @return bool
     */
    public function hasNodeWithSameParentAndOrder($parentId, $order, $siteId)
    {
        $qa = $this->createAggregationQueryBuilderWithSiteId($siteId);
        $qa->match(
            array(
                'parentId' => $parentId,
                'order'    => $order,
                'deleted'  => false,
                'nodeType' => NodeInterface::TYPE_DEFAULT
            )
        );
        $node = $this->singleHydrateAggregateQuery($qa);

        return $node instanceof NodeInterface;
    }

    /**
     * @return ReadNodeInterface
     */
    public function findLastPublished()
    {
        $qb = $this->createQueryBuilder();
        $qb->field('status.publishedState')->equals(true);
        $qb->field('deleted')->equals(false);
        $qb->sort('updatedAt', 'desc');

        return $qb->getQuery()->getSingleResult();
    }

    /**
     * @param string $nodeType
     * @param int    $skip
     * @param int    $limit
     *
     * @return array
     */
    public function findAllPublishedByTypeWithSkipAndLimit($nodeType, $skip, $limit)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(
            array(
                'nodeType' => $nodeType,
                'status.publishedState' => true,
                'deleted' => false
            )
        );
        $qa->sort(array('createdAt' => 1));
        $qa->skip($skip);
        $qa->limit($limit);

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param string $nodeType
     *
     * @return int
     */
    public function countAllPublishedByType($nodeType)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(
            array(
                'nodeType' => $nodeType,
                'status.publishedState' => true,
                'deleted' => false
            )
        );

        return $this->countDocumentAggregateQuery($qa);
    }

    /**
     * @param string       $id
     * @param string       $siteId
     * @param array|null   $eventTypes
     * @param boolean|null $published
     * @param int|null     $limit
     * @param array|null   $sort
     *
     * @return array
     */
    public function findByHistoryAndSiteId($id, $siteId, array $eventTypes = null, $published = null, $limit = null, array $sort = null)
    {
        $qa = $this->createAggregationQuery();
        $filter = array(
            'nodeType' => NodeInterface::TYPE_DEFAULT,
            'histories.user.$id' => new \MongoId($id),
            'siteId' => $siteId,
            'deleted' => false
        );
        if (null !== $eventTypes) {
            $filter['histories.eventType'] = array('$in' => $eventTypes);
        }
        if (null !== $published) {
            $filter['status.publishedState'] = $published;
        }
        $qa->match($filter);

        if (null !== $sort) {
            $qa->sort($sort);
        }

        if (null !== $limit) {
            $qa->limit($limit);
        }

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param StatusInterface $status
     *
     * @return bool
     */
    public function hasStatusedElement(StatusInterface $status)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(array('status._id' => new \MongoId($status->getId())));
        $node = $this->singleHydrateAggregateQuery($qa);

        return $node instanceof NodeInterface;
    }

    /**
     * @param string $siteId
     * @param bool   $themeSiteDefault
     *
     * @return array
     */
    public function findBySiteIdAndDefaultTheme($siteId, $themeSiteDefault = true)
    {
        $qa = $this->createAggregationQuery();
        $filter = array(
            'siteId' => $siteId,
            'themeSiteDefault' => $themeSiteDefault
        );
        $qa->match($filter);

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param string $theme
     *
     * @return array
     */
    public function findByTheme($theme)
    {
        $qa = $this->createAggregationQuery();
        $filter = array(
            'theme' => $theme
        );
        $qa->match($filter);

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param string $blockId
     *
     * @return boolean
     */
    public function countBlockUsed($blockId)
    {
        $qb = $this->createQueryBuilder();
        $function = new \MongoCode(
            'function() {
                for (var areaIndex in this.areas)
                    for (var key in this.areas[areaIndex].blocks)
                        if (this.areas[areaIndex].blocks[key].$id == "'.$blockId.'")
                            return this;
            }'
        );
        $qb->where($function);

        return $qb->getQuery()->execute()->count();
    }

    /**
     * @param string  $blockId
     * @param string  $areaName
     * @param string  $nodeId
     * @param string  $siteId
     * @param string  $language
     * @param integer $version
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function removeBlockInArea($blockId, $areaName, $nodeId, $siteId, $language, $version)
    {
        $qb = $this->createQueryBuilder();
        $qb->updateMany()
            ->field('nodeId')->equals($nodeId)
            ->field('siteId')->equals($siteId)
            ->field('language')->equals($language)
            ->field('version')->equals($version)
            ->field('areas.'.$areaName.'.blocks.$id')->equals(new \MongoId($blockId))
            ->field('areas.'.$areaName.'.blocks')->pull(array('$id' => new \MongoId($blockId)))
            ->getQuery()
            ->execute();
    }

    /**
     * @param PaginateFinderConfiguration $configuration
     *
     * @return array
     */
    protected function getFilterSearch(PaginateFinderConfiguration $configuration) {
        $filter = array();
        $name = $configuration->getSearchIndex('name');
        if (null !== $name && $name !== '') {
            $filter['name'] = new MongoRegex('/.*'.$name.'.*/i');
        }

        $inMenu = $configuration->getSearchIndex('inMenu');
        if (null !== $inMenu && $inMenu !== '') {
            $filter['inMenu'] = (boolean) $inMenu;
        }

        $status = $configuration->getSearchIndex('status');
        if (null !== $status && $status !== '') {
            $filter['status.name'] = $status;
        }

        return $filter;
    }

    /**
     * @param array  $nodes
     * @param string $parentId
     *
     * @return array
     */
    protected function generateTree(array $nodes, $parentId)
    {
        if (empty($nodes)) {
            return array();
        }

        $nodesRoot = array_filter($nodes, function($node, $key) use ($parentId) {
            $property = 'nodeId';
            if (NodeInterface::ROOT_PARENT_ID === $parentId) {
                $property = 'parentId';
            }
            if ($parentId !== $node['node'][$property]) {
                return false;
            }
            return true;
        }, ARRAY_FILTER_USE_BOTH);

        $tree = array();
        foreach ($nodesRoot as $nodeRoot) {
            $tree[] = array('node' => $nodeRoot['node'], 'child' => $this->getChildren($nodeRoot['nodeId'], $nodes));
        }
        uasort($tree, array($this, 'sortTree'));
        $tree = array_values($tree);

        return $tree;
    }

    /**
     * @param string $parentId
     * @param array  $nodes
     *
     * @return array
     */
    protected function getChildren($parentId, array $nodes)
    {
        $children = array();
        foreach ($nodes as $position => $node) {
            if ($parentId === $node['node']['parentId']) {
                unset($nodes[$position]);
                $children[] = array('node' => $node['node'], 'child' => $this->getChildren($node['nodeId'], $nodes));
            }
        }
        uasort($children, array($this, 'sortTree'));
        $children = array_values($children);

        return $children;
    }

    /**
     * @param ReadNodeInterface $node1
     * @param ReadNodeInterface $node2
     *
     * @return int
     */
    protected function sortTree($node1, $node2)
    {
        $order1 = $node1['node']['order'];
        $order2 = $node2['node']['order'];

        if ($order1 == $order2 || $order1 == -1 || $order2 == -1) {
            return 0;
        }

        return ($order1 < $order2) ? -1 : 1;
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
                'status.publishedState' => true,
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
                'status.publishedState' => true,
                'deleted' => false,
                $field => true
            )
        );

        return $this->hydrateAggregateQuery($qa);
    }
}
