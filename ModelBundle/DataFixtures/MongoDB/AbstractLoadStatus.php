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
     *
     * @return Status
     */
    protected function loadStatus($enName, $frName, $name, $color, $published = false, $initial = false)
    {
        $value = new Status();
        $value->setName($name);
        $value->setPublished($published);
        $value->setInitial($initial);
        $value->addLabel('en', $enName);
        $value->addLabel('fr', $frName);
        $value->setDisplayColor($color);

        $this->addReference('status-' . $name, $value);

        return $value;
    }
}
