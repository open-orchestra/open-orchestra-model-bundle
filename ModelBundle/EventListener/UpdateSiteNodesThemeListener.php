<?php

namespace OpenOrchestra\ModelBundle\EventListener;

use Doctrine\ODM\MongoDB\Event\PreUpdateEventArgs;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class UpdateSiteNodesThemeListener
 */
class UpdateSiteNodesThemeListener implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(PreUpdateEventArgs $event)
    {
        $document = $event->getDocument();
        if ($document instanceof SiteInterface && $event->hasChangedField("theme")) {
            $siteTheme = $document->getTheme()->getName();
            $nodesToUpdate = $this->container->get('open_orchestra_model.repository.node')->findBySiteIdAndDefaultTheme($document->getSiteId());
            /* @var $node NodeInterface */
            foreach ($nodesToUpdate as $node) {
                $node->setTheme($siteTheme);
            }
        }
    }
}
