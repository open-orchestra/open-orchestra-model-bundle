<?php

namespace OpenOrchestra\ModelBundle\EventListener;

use Doctrine\ODM\MongoDB\Event\PreUpdateEventArgs;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use OpenOrchestra\ModelInterface\Model\ReportableInterface;
use OpenOrchestra\ModelBundle\Document\Report;

/**
 * Class UpdateReportListener
 */
class UpdateReportListener implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param preFlushEventArgs $event
     */
    public function preUpdate(PreUpdateEventArgs $event)
    {
        $document = $event->getDocument();
        if ($document instanceof ReportableInterface) {
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $report = new Report();
            $report->setUpdatedAt(new \DateTime());
            var_dump($user);
            $report->setUser($user);
            $document->addReport($report);
        }
    }
}
