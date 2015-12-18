<?php

namespace OpenOrchestra\ModelBundle\Repository;

use Doctrine\ODM\MongoDB\Mapping;
use OpenOrchestra\ModelBundle\Repository\RepositoryTrait\AreaFinderTrait;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\ReadNodeInterface;
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
    use AreaFinderTrait;

    /**
     * @param string $entityId
     *
     * @deprecated will be removed in 1.2.0, use findVersionByDocumentId instead
     *
     * @return NodeInterface
     */
    public function findOneById($entityId)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.1.0 and will be removed in 1.2.0. Use the '.__CLASS__.'::findVersionByDocumentId method instead.', E_USER_DEPRECATED);

        return $this->findVersionByDocumentId($entityId);
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
     * @deprecated will be removed in 1.2.0, use getFooterTree instead
     *
     * @return array
     */
    public function getFooterTreeByLanguageAndSiteId($language, $siteId)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.1.0 and will be removed in 1.2.0. Use the '.__CLASS__.'::getFooteTree method instead.', E_USER_DEPRECATED);

        return $this->getFooterTree($language, $siteId);
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
     * @deprecated will be removed in 1.2.0, use getMenuTree instead
     *
     * @return array
     */
    public function getMenuTreeByLanguageAndSiteId($language, $siteId)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.1.0 and will be removed in 1.2.0. Use the '.__CLASS__.'::getMenuTree method instead.', E_USER_DEPRECATED);

        return $this->getMenuTree($language, $siteId);
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
     * @deprecated will be removed in 1.2.0, use getSubMenu instead
     *
     * @return array
     */
    public function getSubMenuByNodeIdAndNbLevelAndLanguageAndSiteId($nodeId, $nbLevel, $language, $siteId)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.1.0 and will be removed in 1.2.0. Use the '.__CLASS__.'::getSubMenu method instead.', E_USER_DEPRECATED);

        return $this->getSubMenu($nodeId, $nbLevel, $language, $siteId);
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
        $node = $this->findPublishedInLastVersion($nodeId, $language, $siteId);
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
     * @deprecated will be removed in 1.2.0, use findPublishedInLastVersion instead
     *
     * @return mixed
     */
    public function findOnePublishedByNodeIdAndLanguageAndSiteIdInLastVersion($nodeId, $language, $siteId)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.1.0 and will be removed in 1.2.0. Use the '.__CLASS__.'::findPpublishedInLastVersion method instead.', E_USER_DEPRECATED);

        return $this->findPublishedInLastVersion($nodeId, $language, $siteId);
    }

    /**
     * @param string $nodeId
     * @param string $language
     * @param string $siteId
     *
     * @return mixed
     */
    public function findPublishedInLastVersion($nodeId, $language, $siteId)
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
     * @deprecated will be removed in 1.2.0, use findVersion instead
     *
     * @return mixed
     */
    public function findOneByNodeIdAndLanguageAndSiteIdAndVersion($nodeId, $language, $siteId, $version = null)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.1.0 and will be removed in 1.2.0. Use the '.__CLASS__.'::findVersion method instead.', E_USER_DEPRECATED);

        return $this->findVersion($nodeId, $language, $siteId, $version);
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
     * @deprecated will be removed in 1.2.0, use findByNodeAndLanguageAndSite instead
     *
     * @return mixed
     */
    public function findByNodeIdAndLanguageAndSiteId($nodeId, $language, $siteId)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.1.0 and will be removed in 1.2.0. Use the '.__CLASS__.'::findByNodeAndLanguageAndSite method instead.', E_USER_DEPRECATED);

        return $this->findByNodeAndLanguageAndSite($nodeId, $language, $siteId);
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

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param string $nodeId
     * @param string $language
     * @param string $siteId
     *
     * @throws \Exception
     *
     * @deprecated will be removed in 1.2.0, use findPublishedSortedByVersion instead
     *
     * @return mixed
     */
    public function findByNodeIdAndLanguageAndSiteIdAndPublishedOrderedByVersion($nodeId, $language, $siteId)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.1.0 and will be removed in 1.2.0. Use the '.__CLASS__.'::findPublishedSortedByVersion method instead.', E_USER_DEPRECATED);

        return $this->findPublishedSortedByVersion($nodeId, $language, $siteId);
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
     * @deprecated will be removed in 1.2.0, use findByNodeAndSite instead
     *
     * @return mixed
     */
    public function findByNodeIdAndSiteId($nodeId, $siteId)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.1.0 and will be removed in 1.2.0. Use the '.__CLASS__.'::findByNodeAndSite method instead.', E_USER_DEPRECATED);

        return $this->findByNodeAndSite($nodeId, $siteId);
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
     * @param string $parentId
     * @param string $siteId
     *
     * @throws \Exception
     *
     * @deprecated will be removed in 1.2.0, use findByParent instead
     *
     * @return mixed
     */
    public function findByParentIdAndSiteId($parentId, $siteId)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.1.0 and will be removed in 1.2.0. Use the '.__CLASS__.'::findByParent method instead.', E_USER_DEPRECATED);

        return $this->findByParent($$parentId, $siteId);
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
        $qa->match(array('path' => new MongoRegex('/'.$path.'.*/i')));

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param string $nodeId
     * @param string $language
     * @param string $siteId
     *
     * @deprecated will be removed in 1.2.0, use findInLastVersion instead
     *
     * @return mixed
     */
    public function findOneByNodeIdAndLanguageAndSiteIdInLastVersion($nodeId, $language, $siteId)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.1.0 and will be removed in 1.2.0. Use the '.__CLASS__.'::findInLastVersion method instead.', E_USER_DEPRECATED);

        return $this->findInLastVersion($nodeId, $language, $siteId);
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
     * @deprecated will be removed in 1.2.0, use findLastVersionByType instead
     *
     * @return array
     */
    public function findLastVersionBySiteId($siteId, $type = NodeInterface::TYPE_DEFAULT)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.1.0 and will be removed in 1.2.0. Use the '.__CLASS__.'::findLastVersionByType method instead.', E_USER_DEPRECATED);

        return $this->findLastVersionByType($siteId, $type);
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
     * @param string $path
     * @param string $siteId
     * @param string $language
     *
     * @deprecated will be removed in 1.2.0, use findSubTreeByPath instead
     *
     * @return mixed
     */
    public function findChildrenByPathAndSiteIdAndLanguage($path, $siteId, $language)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.1.0 and will be removed in 1.2.0. Use the '.__CLASS__.'::findSubTreeByPath method instead.', E_USER_DEPRECATED);

        return $this->findSubTreeByPath($path, $siteId, $language);
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
     * @deprecated used only in the fixtures, will be removed in 1.2.0
     *
     * @return array
     */
    public function findByNodeType($type = NodeInterface::TYPE_DEFAULT)
    {
        return parent::findBy(array('nodeType' => $type));
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
     * @param string $language
     * @param string $siteId
     *
     * @deprecated will be removed in 1.2.0, use findLastPublishedVersion instead
     *
     * @return ReadNodeInterface
     */
    public function findLastPublishedVersionByLanguageAndSiteId($language, $siteId)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.1.0 and will be removed in 1.2.0. Use the '.__CLASS__.'::findLastPublishedVersion method instead.', E_USER_DEPRECATED);

        return $this->findLastPublishedVersion($language, $siteId);
    }

    /**
     * @param string $language
     * @param string $siteId
     *
     * @return ReadNodeInterface
     */
    public function findLastPublishedVersion($language, $siteId)
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
     * @deprecated will be removed in 1.2.0, use findByParentAndRoutePattern instead
     *
     * @return array
     */
    public function findByParentIdAndRoutePatternAndNodeIdAndSiteId($parentId, $routePattern, $nodeId, $siteId)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.1.0 and will be removed in 1.2.0. Use the '.__CLASS__.'::findByParentAndRoutePattern method instead.', E_USER_DEPRECATED);

        return $this->findByParentAndRoutePattern($parentId, $routePattern, $nodeId, $siteId);
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
     * @param string       $author
     * @param boolean|null $published
     * @param int|null     $limit
     *
     * @return array
     *
     * @deprecated will be removed in 1.2.0
     */
    public function findByAuthor($author, $published = null, $limit = null)
    {
        $qa = $this->createAggregationQuery();
        $filter = array(
            'nodeType' => NodeInterface::TYPE_DEFAULT,
            'createdBy' => $author,
            'deleted' => false
        );
        if (null !== $published) {
            $filter['status.published'] = $published;
        }
        $qa->match($filter);

        if (null !== $limit) {
            $qa->limit($limit);
        }

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param string       $author
     * @param string       $siteId
     * @param boolean|null $published
     * @param int|null     $limit
     * @param array        $sort
     *
     * @return array
     */
    public function findByAuthorAndSiteId($author, $siteId, $published = null, $limit = null, $sort = null)
    {
        $qa = $this->createAggregationQuery();
        $filter = array(
            'nodeType' => NodeInterface::TYPE_DEFAULT,
            'createdBy' => $author,
            'siteId' => $siteId,
            'deleted' => false
        );
        if (null !== $published) {
            $filter['status.published'] = $published;
        }
        $qa->match($filter);

        if (null !== $limit) {
            $qa->limit($limit);
        }

        if (null !== $sort) {
            $qa->sort($sort);
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
        $node = $this->findOneBy(array('status' => $status));

        return $node instanceof NodeInterface;
    }
}
