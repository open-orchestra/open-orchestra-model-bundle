<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use OpenOrchestra\ModelBundle\Document\Status;

/**
 * Class AbstractLoadStatus
 */
abstract class AbstractLoadStatus extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param string $enName
     * @param string $frName
     * @param string $name
     * @param string $color
     * @param bool   $published
     * @param bool   $initial
     * @param bool   $blockedEdition
     * @param bool   $autoPublishFrom
     * @param bool   $autoUnpublishTo
     *
     * @return Status
     */
    protected function loadStatus(
        $enName,
        $frName,
        $name,
        $color,
        $published = false,
        $initial = false,
        $blockedEdition = false,
        $outOfWorkflow = false,
        $autoPublishFrom = false,
        $autoUnpublishTo = false
    ) {
        $value = new Status();
        $value->setName($name);
        $value->setPublished($published);
        $value->setInitial($initial);
        $value->setAutoPublishFrom($autoPublishFrom);
        $value->setAutoUnpublishTo($autoUnpublishTo);
        $value->addLabel('en', $enName);
        $value->addLabel('fr', $frName);
        $value->setDisplayColor($color);
        $value->setBlockedEdition($blockedEdition);
        $value->setOutOfWorkflow($outOfWorkflow);

        $this->addReference('status-' . $name, $value);

        return $value;
    }
}
