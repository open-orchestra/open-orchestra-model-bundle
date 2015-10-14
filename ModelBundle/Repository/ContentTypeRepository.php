<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use OpenOrchestra\Pagination\Configuration\FinderConfiguration;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;
use OpenOrchestra\Pagination\MongoTrait\PaginationTrait;
use OpenOrchestra\Repository\AbstractAggregateRepository;
use Solution\MongoAggregation\Pipeline\Stage;

/**
 * Class ContentTypeRepository
 */
class ContentTypeRepository extends AbstractAggregateRepository implements ContentTypeRepositoryInterface
{
    use PaginationTrait;

    /**
     * @param $language
     *
     * @return array
     */
    public function findAllNotDeletedInLastVersion($language = null)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(
            array(
                'deleted' => false
            )
        );
        $elementName = 'contentType';
        $this->generateLastVersionFilter($qa, $elementName);

        if ($language) {
            $qa->sort(
                array(
                    $elementName . '.names.' . $language. '.value' => 1
                )
            );
        }

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

        $qa = $this->generateFilter($qa, $configuration);

        $elementName = 'contentType';
        $this->generateLastVersionFilter($qa, $elementName);

        $qa = $this->generateFilterSort(
            $qa,
            $configuration->getOrder(),
            $configuration->getDescriptionEntity()
        );

        $qa = $this->generateSkipFilter($qa, $configuration->getSkip());
        $qa = $this->generateLimitFilter($qa, $configuration->getLimit());

        return $this->hydrateAggregateQuery($qa, $elementName, 'getContentTypeId');
    }

    /**
     * @param array|null  $descriptionEntity
     * @param array|null  $columns
     * @param string|null $search
     *
     * @deprecated will be removed in 0.3.0, use countNotDeletedInLastVersionWithSearchFilter instead
     *
     * @return int
     */
    public function countByDeletedInLastVersionWithSearchFilter($descriptionEntity = null, $columns = null, $search = null)
    {
        $configuration = FinderConfiguration::generateFromVariable($descriptionEntity, $columns, $search);

        return $this->countNotDeletedInLastVersionWithSearchFilter($configuration);
    }

    /**
     * @param FinderConfiguration $configuration
     *
     * @return int
     */
    public function countNotDeletedInLastVersionWithSearchFilter(FinderConfiguration $configuration)
    {
        $qa = $this->createAggregateQueryNotDeletedInLastVersion();
        $qa = $this->generateFilter($qa, $configuration);

        $elementName = 'contentType';
        $this->generateLastVersionFilter($qa, $elementName);

        return $this->countDocumentAggregateQuery($qa, $elementName);
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
     * @param Stage  $qa
     * @param string $elementName
     */
    protected function generateLastVersionFilter(Stage $qa, $elementName)
    {
        $qa->sort(array('version' => 1));
        $qa->group(array(
            '_id' => array('contentTypeId' => '$contentTypeId'),
            $elementName => array('$last' => '$$ROOT')
        ));
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
