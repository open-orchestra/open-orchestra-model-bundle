<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;

/**
 * Class LoadStatusDataFunctional
 */
class LoadStatusDataFunctional extends AbstractLoadStatus implements OrchestraFunctionalFixturesInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $manager->persist($this->loadStatus('Pending', 'En attente', 'pending', 'orange', false, false, true));

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 45;
    }
}
