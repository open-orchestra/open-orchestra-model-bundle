<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelBundle\Document\Role;
use OpenOrchestra\ModelBundle\Document\TranslatedValue;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraProductionFixturesInterface;

/**
 * Class LoadRoleDataProduction
 */
class LoadRoleDataProduction extends AbstractFixture implements OrderedFixtureInterface, OrchestraProductionFixturesInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $draftToPublished = new Role();
        $draftToPublished->setName('ROLE_FROM_DRAFT_TO_PUBLISHED');
        $draftToPublished->addDescription($this->generateTranslatedValue('en', 'Change status from draft to published'));
        $draftToPublished->addDescription($this->generateTranslatedValue('fr', 'Modifier le status de brouillon à publié'));
        $draftToPublished->setFromStatus($this->getReference('status-draft'));
        $draftToPublished->setToStatus($this->getReference('status-published'));
        $this->addReference('role-production-draft-to-published', $draftToPublished);
        $manager->persist($draftToPublished);

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 115;
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
