<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;
use OpenOrchestra\ModelBundle\Document\Report;

/**
 * Class AddReportData
 */
class AddReportData extends AbstractFixture implements OrderedFixtureInterface, OrchestraFunctionalFixturesInterface
{
    protected $objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function load(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;

        $objectManager->persist($this->addReport("ds_3_fr"));
        $objectManager->persist($this->addReport("ds_3_en"));
        $objectManager->persist($this->addReport("node-fr"));

        $objectManager->flush();
    }

    /**
     * @param string $name
     */
    protected function addReport($name){
        $document = $this->getReference($name);

        $report = new Report();
        $report->setUpdatedAt(new \DateTime());
        $report->setUser($this->getReference("user-admin"));
        $document->addReport($report);

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
