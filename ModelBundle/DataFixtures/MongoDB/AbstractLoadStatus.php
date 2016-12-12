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
     * @param bool   $publishedState
     * @param bool   $initialState
     * @param bool   $blockedEdition
     * @param bool   $outOfWorkflow
     * @param bool   $autoPublishFromState
     * @param bool   $autoUnpublishToState
     * @param bool   $translationState
     *
     * @return Status
     */
    protected function loadStatus(
        $enName,
        $frName,
        $name,
        $color,
        $publishedState = false,
        $initialState = false,
        $blockedEdition = false,
        $outOfWorkflow = false,
        $autoPublishFromState = false,
        $autoUnpublishToState = false,
        $translationState = false
    ) {
        $value = new Status();
        $value->setName($name);
        $value->setPublishedState($publishedState);
        $value->setInitialState($initialState);
        $value->setAutoPublishFromState($autoPublishFromState);
        $value->setAutoUnpublishToState($autoUnpublishToState);
        $value->addLabel('en', $enName);
        $value->addLabel('fr', $frName);
        $value->setDisplayColor($color);
        $value->setBlockedEdition($blockedEdition);
        $value->setOutOfWorkflow($outOfWorkflow);
        $value->setTranslationState($translationState);

        $this->addReference('status-' . $name, $value);

        return $value;
    }
}
