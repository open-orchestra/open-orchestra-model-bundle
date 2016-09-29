<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;
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
        $this->objectManager = $objectManager;

        $objectManager->persist($this->addHistory("ds_3_fr"));
        $objectManager->persist($this->addHistory("ds_3_en"));
        $objectManager->persist($this->addHistory("node-fr"));

        $objectManager->flush();
    }

    /**
     * @param string $name
     */
    protected function addHistory($name){
        $document = $this->getReference($name);

        $history = new History();
        $history->setUpdatedAt(new \DateTime());
        $history->setUser($this->getReference("user-admin"));
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
