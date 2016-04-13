<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelBundle\Document\Role;
use OpenOrchestra\ModelBundle\Document\TranslatedValue;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;

/**
 * Class LoadRoleData
 */
class LoadRoleDataFunctional extends AbstractFixture implements OrderedFixtureInterface, OrchestraFunctionalFixturesInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $user = new Role();
        $user->setName('ROLE_USER');
        $user->addDescription($this->generateTranslatedValue('en', 'Authenticated user'));
        $user->addDescription($this->generateTranslatedValue('fr', 'Utilisateur authentifié'));
        $this->addReference('role-user-functional', $user);
        $manager->persist($user);

        $draftToPending = new Role();
        $draftToPending->setName('ROLE_FROM_DRAFT_TO_PENDING');
        $draftToPending->addDescription($this->generateTranslatedValue('en', 'Change status from draft to pending'));
        $draftToPending->addDescription($this->generateTranslatedValue('fr', 'Modifier le status de brouillon à en attente'));
        $draftToPending->setFromStatus($this->getReference('status-draft'));
        $draftToPending->setToStatus($this->getReference('status-pending'));
        $this->addReference('role-functional-draft-to-pending', $draftToPending);
        $manager->persist($draftToPending);
 
        $pendintToPusblished = new Role();
        $pendintToPusblished->setName('ROLE_FROM_PENDING_TO_PUBLISHED');
        $pendintToPusblished->addDescription($this->generateTranslatedValue('en', 'Change status from pending to draft'));
        $pendintToPusblished->addDescription($this->generateTranslatedValue('fr', 'Modifier le status de en attente à brouillon'));
        $pendintToPusblished->setFromStatus($this->getReference('status-pending'));
        $pendintToPusblished->setToStatus($this->getReference('status-published'));
        $this->addReference('role-functional-pending-to-published', $pendintToPusblished);
        $manager->persist($pendintToPusblished);

        $pendingToDraft = new Role();
        $pendingToDraft->setName('ROLE_FROM_PUBLISHED_TO_DRAFT');
        $pendingToDraft->addDescription($this->generateTranslatedValue('en', 'Change status from pending to draft'));
        $pendingToDraft->addDescription($this->generateTranslatedValue('fr', 'Modifier le status de en attente à brouillon'));
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

    /**
     * Generate a translatedValue
     *
     * @param string $language
     * @param string $value
     *
     * @return TranslatedValue
     */
    protected function generateTranslatedValue($language, $value)
    {
        $label = new TranslatedValue();
        $label->setLanguage($language);
        $label->setValue($value);

        return $label;
    }
}
