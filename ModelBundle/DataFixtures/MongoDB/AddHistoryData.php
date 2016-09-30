<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;
use OpenOrchestra\ModelInterface\ContentEvents;
use OpenOrchestra\ModelInterface\NodeEvents;
use OpenOrchestra\ModelBundle\Document\History;

/**
 * Class AddHistoryData
 */
class AddHistoryData extends AbstractFixture implements OrderedFixtureInterface, OrchestraFunctionalFixturesInterface
{
    protected $objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function load(ObjectManager $objectManager)
    {
        if ($this->hasReference("user-admin")) {
            $this->objectManager = $objectManager;

            $objectManager->persist($this->addHistory("ds_3_fr", ContentEvents::CONTENT_CREATION));
            $objectManager->persist($this->addHistory("ds_3_en", ContentEvents::CONTENT_CREATION));
            $objectManager->persist($this->addHistory("node-fr", NodeEvents::NODE_CREATION));

            $objectManager->flush();
        }
    }

    /**
     * @param string $name
     * @param string $eventType
     */
    protected function addHistory($name, $eventType){
        $document = $this->getReference($name);

        $history = new History();
        $history->setUpdatedAt(new \DateTime());
        $history->setUser($this->getReference("user-admin"));
        $history->setEventType($eventType);
        $document->addHistory($history);

        return $document;
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 900;
    }
}
