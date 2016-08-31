<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelBundle\Document\Role;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;

/**
 * Class LoadRoleDataFunctional
 */
class LoadRoleDataFunctional extends AbstractFixture implements OrderedFixtureInterface, OrchestraFunctionalFixturesInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $draftToPending = new Role();
        $draftToPending->setName('ROLE_FROM_DRAFT_TO_PENDING');
        $draftToPending->addDescription('en', 'Change status from draft to pending');
        $draftToPending->addDescription('fr', 'Modifier le status de brouillon à en attente');
        $draftToPending->setFromStatus($this->getReference('status-draft'));
        $draftToPending->setToStatus($this->getReference('status-pending'));
        $this->addReference('role-functional-draft-to-pending', $draftToPending);
        $manager->persist($draftToPending);
 
        $pendintToPusblished = new Role();
        $pendintToPusblished->setName('ROLE_FROM_PENDING_TO_PUBLISHED');
        $pendintToPusblished->addDescription('en', 'Change status from pending to draft');
        $pendintToPusblished->addDescription('fr', 'Modifier le status de en attente à brouillon');
        $pendintToPusblished->setFromStatus($this->getReference('status-pending'));
        $pendintToPusblished->setToStatus($this->getReference('status-published'));
        $this->addReference('role-functional-pending-to-published', $pendintToPusblished);
        $manager->persist($pendintToPusblished);

        $pendingToDraft = new Role();
        $pendingToDraft->setName('ROLE_FROM_PUBLISHED_TO_DRAFT');
        $pendingToDraft->addDescription('en', 'Change status from pending to draft');
        $pendingToDraft->addDescription('fr', 'Modifier le status de en attente à brouillon');
        $pendingToDraft->setFromStatus($this->getReference('status-published'));
        $pendingToDraft->setToStatus($this->getReference('status-draft'));
        $this->addReference('role-functional-published-to-draft', $pendingToDraft);
        $manager->persist($pendingToDraft);

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 110;
    }

}
