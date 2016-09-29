<?php

namespace OpenOrchestra\ModelBundle\Repository;

<<<<<<< 051ee1ae50195acba886f42a3db3ffd2153691a4
=======
use OpenOrchestra\ModelBundle\Repository\RepositoryTrait\AreaFinderTrait;
use OpenOrchestra\ModelBundle\Repository\RepositoryTrait\UseTrackableTrait;
>>>>>>> add reference node
use OpenOrchestra\ModelInterface\Model\AreaInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\ReadNodeInterface;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\FieldAutoGenerableRepositoryInterface;
use Solution\MongoAggregation\Pipeline\Stage;
use OpenOrchestra\Repository\AbstractAggregateRepository;
use MongoRegex;

/**
 * Class NodeRepository
 */
class NodeRepository extends AbstractAggregateRepository implements FieldAutoGenerableRepositoryInterface, NodeRepositoryInterface
{
<<<<<<< 051ee1ae50195acba886f42a3db3ffd2153691a4
=======
    use AreaFinderTrait;
    use UseTrackableTrait;

>>>>>>> add reference node
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
        $node = $this->findOneCurrentlyPublished($nodeId, $language, $siteId);
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
    public function findOneCurrentlyPublished($nodeId, $language, $siteId)
    {
        $qa = $this->createAggregationQueryBuilderWithSiteIdAndLanguage($siteId, $language);
        $filter = array(
            'currentlyPublished' => true,
            'deleted' => false,
            'nodeId' => $nodeId,
        );
        $qa->match($filter);
        $qa->sort(array('version' => -1));

        return $this->singleHydrateAggregateQuery($qa);
    }

    /**
     * @param StatusableInterface $element
     *
     * @return StatusableInterface
     */
    public function findOneCurrentlyPublishedByElement(StatusableInterface $element)
    {
        return $this->findOneCurrentlyPublished($element->getNodeId(), $element->getLanguage(), $element->getSiteId());
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
    public function findByNodeAndSite($nodeId, $siteId)
    {
        $qa = $this->createAggregationQueryBuilderWithSiteId($siteId);
        $qa->match(array('nodeId' => $nodeId));

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param string $referenceNodeId
     * @param string $nodeId
     * @param string $siteId
     * @param string $entityType
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function updateUseReference($referenceNodeId, $nodeId, $siteId, $entityType)
    {
        $qb = $this->createQueryBuilder()
             ->update()
             ->multiple(true)
             ->field('nodeId')->equals($nodeId)
             ->field('siteId')->equals($siteId);

        $this->addUpdateUseReferenceField($qb, $entityType, $referenceNodeId)
             ->getQuery()
             ->execute();
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
     * @param string $type
     *
     * @return array
     */
    public function findLastVersionByTypeCurrentlyPublished($siteId, $type = NodeInterface::TYPE_DEFAULT)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(
            array(
                'siteId' => $siteId,
                'deleted' => false,
                'nodeType' => $type,
                'currentlyPublished' => true,
            )
        );

        return $this->findLastVersionInLanguage($qa);
    }

    /**
     * @param string $path
     * @param string $siteId
     * @param string $language
     *
     * @return array
     */
    public function findByPathCurrentlyPublishedAndLanguage($path, $siteId, $language)
    {
        $qa = $this->createAggregationQueryBuilderWithSiteId($siteId);
        $qa->match(
            array(
                'path' => new MongoRegex('/^'.$path.'(\/.*)?$/'),
                'currentlyPublished' => true,
                'deleted' => false,
                'language' => $language,
            )
        );

        return $this->findLastVersion($qa);
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
    public function findCurrentlyPublishedVersion($language, $siteId)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(
            array(
                'siteId'=> $siteId,
                'language'=> $language,
                'currentlyPublished' => true,
                'nodeType' => NodeInterface::TYPE_DEFAULT
            )
        );

        return $this->findLastVersion($qa);
    }

    /**
     * @param NodeInterface $element
     *
     * @return NodeInterface
     */
    public function findPublishedInLastVersionWithoutFlag(StatusableInterface $element)
    {
        $qa = $this->createAggregationQueryBuilderWithSiteIdAndLanguage($element->getSiteId(), $element->getLanguage());
        $filter = array(
            'status.published' => true,
            'currentlyPublished' => false,
            'deleted' => false,
            'nodeId' => $element->getNodeId(),
        );
        $qa->match($filter);
        $qa->sort(array('version' => -1));

        return $this->singleHydrateAggregateQuery($qa);
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
                'currentlyPublished' => true,
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

    /**
     * @param string $nodeType
     * @param int    $skip
     * @param int    $limit
     *
     * @return array
     */
    public function findAllCurrentlyPublishedByTypeWithSkipAndLimit($nodeType, $skip, $limit)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(
            array(
                'nodeType' => $nodeType,
                'currentlyPublished' => true,
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
    public function countAllCurrentlyPublishedByType($nodeType)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(
            array(
                'nodeType' => $nodeType,
                'currentlyPublished' => true,
                'deleted' => false
            )
        );

        return $this->countDocumentAggregateQuery($qa);
    }

    /**
     * @param string $nodeType
     * @param string $siteId
     *
     * @return array
     */
    public function findAllNodesOfTypeInLastPublishedVersionForSite($nodeType, $siteId)
    {
        $qa = $this->createAggregationQueryBuilderWithSiteId($siteId);
        $qa->match(
            array(
                'nodeType' => $nodeType,
                'status.published' => true,
                'deleted' => false
            )
        );

        $qa->sort(array('version' => 1));

        return $this->hydrateAggregateQuery($qa);
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
            $filter['status.published'] = $published;
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
     * @param NodeInterface $element
     *
     * @return array
     */
    public function findAllCurrentlyPublishedByElementId(StatusableInterface $element)
    {
        return $this->findBy(array(
            'nodeId' => $element->getNodeId(),
            'language' => $element->getLanguage(),
            'siteId' => $element->getSiteId(),
            'currentlyPublished' => true
        ));
    }

    /**
     * Find all nodes (in all versions and all langauges) ready to be auto-published
     *
     * @param string $siteId
     * @param array  $fromStatus
     *
     * @return array
     */
    public function findNodeToAutoPublish($siteId, array $fromStatus)
    {
        $date = new \Mongodate(strtotime(date('d F Y')));

        $statusIds = array();
        foreach($fromStatus as $status) {
            $statusIds[] = new \MongoId($status->getId());
        }

        $qa = $this->createAggregationQuery();

        $filter = array(
            'siteId' => $siteId,
            'deleted' => false,
            'status._id' => array('$in' => $statusIds),
            'publishDate' => array('$lte' => $date),
            '$or' => array(
                array('unpublishDate' => array('$exists' => false)),
                array('unpublishDate' => array('$gte' => $date))
            )
        );

        $qa->match($filter);
        $qa->sort(array('version' => 1));

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * Find all nodes (in all versions and all langauges) ready to be auto-unpublished
     *
     * @param string          $siteId
     * @param StatusInterface $publishedStatus
     *
     * @return array
     */
    public function findNodeToAutoUnpublish($siteId, StatusInterface $publishedStatus)
    {
        $date = new \Mongodate(strtotime(date('d F Y')));
        $statusId = new \MongoId($publishedStatus->getId());

        $qa = $this->createAggregationQuery();

        $filter = array(
            'siteId' => $siteId,
            'deleted' => false,
            'status._id' => $statusId,
            'unpublishDate' => array('$lte' => $date)
        );

        $qa->match($filter);
        $qa->sort(array('version' => 1));

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * indicates if node collection contains an usage of a particular block
     *
     * @param string $blockId
     *
     * @return boolean
     */
    public function isBlockUsed($blockId)
    {
        $qa = $this->createAggregationQuery();

        $filter = array(
            'areas.blocks.$id' => new \MongoId($blockId),
        );

        $qa->match($filter);

        return $this->countDocumentAggregateQuery($qa) > 0;
    }
}
