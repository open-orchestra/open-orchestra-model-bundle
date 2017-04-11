<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraProductionFixturesInterface;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;

/**
 * Class LoadStatusData
 */
class LoadStatusData extends AbstractLoadStatus implements OrchestraProductionFixturesInterface, OrchestraFunctionalFixturesInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $manager->persist($this->loadStatus('Out of validation workflow', 'Non soumis au workflow de validation', 'outOfWorkflow', 'grayDark', true, false, false, true, false, true));
        $manager->persist($this->loadStatus('Draft', 'Brouillon', 'draft', 'green', false, true));
        $manager->persist($this->loadStatus('Published', 'Publié', 'published', 'red', true, false, true));
        $manager->persist($this->loadStatus('To translate', 'A traduire', 'toTranslate', 'blue', false, false, false, false, false, false, true));
        $manager->persist($this->loadStatus('Offline', 'Hors ligne', 'offline', 'dark-grey', false, false, false, false, false, true));

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 40;
    }
}
