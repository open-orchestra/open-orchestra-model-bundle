<?php

namespace OpenOrchestra\ModelBundle\Manager;


use OpenOrchestra\ModelInterface\Manager\VersionableSaverInterface;
use OpenOrchestra\ModelInterface\Model\VersionableInterface;
use Symfony\Component\Config\Definition\Exception\DuplicateKeyException;
use Symfony\Component\DependencyInjection\ContainerAware;

class VersionableSaver extends ContainerAware implements VersionableSaverInterface
{
    /**
     * Duplicate a node
     *
     * @param VersionableInterface   $versionable
     *
     * @return VersionableInterface
     */
    public function saveDuplicated(VersionableInterface $versionable)
    {
        $version = $versionable->getVersion();
        $documentManager = $this->container->get('doctrine.odm.mongodb.document_manager');
        $documentManager->persist($versionable);

        $count = 0;

        while ($count < 10) {
            try {
                $count ++;
                $documentManager->flush($versionable);
            } catch (DuplicateKeyException $e) {
                $versionable->setVersion($version + $count);
                continue;
            }
            break;
        }

        return $versionable;
    }
}
