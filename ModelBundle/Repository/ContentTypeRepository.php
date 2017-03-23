<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;
use OpenOrchestra\Repository\AbstractAggregateRepository;
use Solution\MongoAggregation\Pipeline\Stage;

/**
 * Class ContentTypeRepository
 */
class ContentTypeRepository extends AbstractAggregateRepository implements ContentTypeRepositoryInterface
{
    /**
     * @param array $contentTypes
     *
     * @return array
     */
    public function findAllNotDeletedInLastVersion(array $contentTypes = array())
    {
        $qa = $this->createAggregationQuery();
        $qa->match(
            array(
                'deleted' => false
            )
        );
        if (!empty($contentTypes)) {
            $qa->match(
                array('contentTypeId' => array('$in' => $contentTypes))
            );
        }
        $elementName = 'contentType';
        $this->generateLastVersionFilter($qa, $elementName);

        return $this->hydrateAggregateQuery($qa, $elementName, 'getContentTypeId');
    }

    /**
     * @param PaginateFinderConfiguration $configuration
     *
     * @return array
     */
    public function findAllNotDeletedInLastVersionForPaginate(PaginateFinderConfiguration $configuration)
    {
        $qa = $this->createAggregateQueryNotDeletedInLastVersion();
        $filters = $this->getFilterSearch($configuration);
        if (!empty($filters)) {
            $qa->match($filters);
        }
        $elementName = 'contentType';
        $group = array(
            'names' => array('$last' => '$names'),
            'contentTypeId' => array('$last' => '$contentTypeId')
        );
        $this->generateLastVersionFilter($qa, $elementName, $group);

        $order = $configuration->getOrder();
        if (!empty($order)) {
            $qa->sort($order);
        }

        $qa->skip($configuration->getSkip());
        $qa->limit($configuration->getLimit());

        return $this->hydrateAggregateQuery($qa, $elementName, 'getContentTypeId');
    }

    /**
     * @param PaginateFinderConfiguration $configuration
     *
     * @return int
     */
    public function countNotDeletedInLastVersionWithSearchFilter(PaginateFinderConfiguration $configuration)
    {
        $qa = $this->createAggregateQueryNotDeletedInLastVersion();
        $filters = $this->getFilterSearch($configuration);
        if (!empty($filters)) {
            $qa->match($filters);
        }
        $elementName = 'contentType';
        $this->generateLastVersionFilter($qa, $elementName);

        return $this->countDocumentAggregateQuery($qa);
    }

    /**
     * @return int
     */
    public function countByContentTypeInLastVersion()
    {
        $qa = $this->createAggregateQueryNotDeletedInLastVersion();
        $elementName = 'content';
        $this->generateLastVersionFilter($qa, $elementName);

        return $this->countDocumentAggregateQuery($qa);
    }

    /**
     * @param string   $contentType
     *
     * @return ContentTypeInterface
     */
    public function findOneByContentTypeIdInLastVersion($contentType)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(array('contentTypeId' => $contentType));
        $qa->sort(array('version' => -1));

        return $this->singleHydrateAggregateQuery($qa);
    }

    /**
     * @param array $contentTypeIds
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function removeByContentTypeId(array $contentTypeIds)
    {
        $qb = $this->createQueryBuilder();
        $qb->updateMany()
            ->field('contentTypeId')->in($contentTypeIds)
            ->field('deleted')->set(true)
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
        $language = $configuration->getSearchIndex('language');
        if (null !== $name && $name !== '' && null !== $language && $language !== '' ) {
            $filter['names.' . $language] = new \MongoRegex('/.*'.$name.'.*/i');
        }

        $linkedToSite = $configuration->getSearchIndex('linkedToSite');
        if (null !== $linkedToSite && $linkedToSite !== '') {
            $filter['linkedToSite'] = (boolean) $linkedToSite;
        }

        $contentTypeId = $configuration->getSearchIndex('contentTypeId');
        if (null !== $contentTypeId && $contentTypeId !== '') {
            $filter['contentTypeId'] =new \MongoRegex('/.*'.$contentTypeId.'.*/i');
        }

        return $filter;
    }

    /**
     * @param Stage  $qa
     * @param string $elementName
     * @param string $elementName
     * @param array  $group
     */
    protected function generateLastVersionFilter(Stage $qa, $elementName, $group = array())
    {
        $group = array_merge($group, array(
                '_id' => array('contentTypeId' => '$contentTypeId'),
                $elementName => array('$last' => '$$ROOT')
        ));

        $qa->sort(array('version' => 1));
        $qa->group($group);
    }

    /**
     * @return \Solution\MongoAggregation\Pipeline\Stage
     */
    protected function createAggregateQueryNotDeletedInLastVersion()
    {
        $qa = $this->createAggregationQuery();
        $qa->match(array('deleted' => false));
        $qa->sort(array('contentTypeId' => -1));

        return $qa;
    }
}
