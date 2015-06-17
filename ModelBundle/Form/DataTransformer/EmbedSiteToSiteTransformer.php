<?php

namespace OpenOrchestra\ModelBundle\Form\DataTransformer;

use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class EmbedSiteToSiteTransformer
 */
class EmbedSiteToSiteTransformer implements DataTransformerInterface
{
    protected $siteRepositoy;

    /**
     * @param SiteRepositoryInterface $siteRepository
     */
    public function __construct(SiteRepositoryInterface $siteRepository)
    {
        $this->siteRepositoy = $siteRepository;
    }

    /**
     * @param array $value
     *
     * @return SiteInterface
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function transform($value)
    {
        $sites = array();

        if (!empty($value)) {
            foreach ($value as $associatedSite) {
                $sites[] = $this->siteRepositoy->findOneBySiteId($associatedSite['siteId']);
            }
        }

        return $sites;
    }

    /**
     * @param SiteInterface $value
     *
     * @return string
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function reverseTransform($value)
    {
        $sites = array();

        foreach ($value as $site) {
            $sites[] = array('siteId' => $site->getSiteId());
        }

        return $sites;
    }
}
