<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelBundle\Document\Role;
use OpenOrchestra\ModelBundle\Document\TranslatedValue;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraProductionFixturesInterface;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;

/**
 * Class LoadRoleData
 */
class LoadRoleData extends AbstractFixture implements OrderedFixtureInterface, OrchestraProductionFixturesInterface, OrchestraFunctionalFixturesInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $draft = new Role();
        $draft->setName('ROLE_FROM_DRAFT_TO_PENDING');
        $draft->addDescription($this->generateTranslatedValue('en', 'Change status from draft to pending'));
        $draft->addDescription($this->generateTranslatedValue('fr', 'Modifier le status de brouillon à en attente'));
        $draft->setFromStatus($this->getReference('status-draft'));
        $draft->setToStatus($this->getReference('status-pending'));
        $this->addReference('role-draft', $draft);
        $manager->persist($draft);

        $pending = new Role();
        $pending->setName('ROLE_FROM_PENDING_TO_PUBLISHED');
        $pending->addDescription($this->generateTranslatedValue('en', 'Change status from pending to published'));
        $pending->addDescription($this->generateTranslatedValue('fr', 'Modifier le status de en attente à publié'));
        $pending->setFromStatus($this->getReference('status-pending'));
        $pending->setToStatus($this->getReference('status-published'));
        $this->addReference('role-pending', $pending);
        $manager->persist($pending);

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
