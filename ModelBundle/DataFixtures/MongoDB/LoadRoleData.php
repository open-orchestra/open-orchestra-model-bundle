<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelBundle\Document\Role;
use OpenOrchestra\ModelBundle\Document\TranslatedValue;

/**
 * Class LoadRoleData
 */
class LoadRoleData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $admin = new Role();
        $admin->setName('ROLE_ADMIN');
        $admin->addDescription($this->generateTranslatedValue('en', 'Role for administrator'));
        $admin->addDescription($this->generateTranslatedValue('fr', 'Role pour les administrateurs'));
        $admin->addDescription($this->generateTranslatedValue('de', 'Rollen für Administratoren'));
        $admin->addDescription($this->generateTranslatedValue('es', 'Funciones para administradores'));
        $manager->persist($admin);

        $user = new Role();
        $user->setName('ROLE_USER');
        $user->addDescription($this->generateTranslatedValue('en', 'Role for users'));
        $user->addDescription($this->generateTranslatedValue('fr', 'Role pour tous les utilisateurs'));
        $user->addDescription($this->generateTranslatedValue('de', 'Rollen für Leute'));
        $user->addDescription($this->generateTranslatedValue('es', 'Papeles para todos los usuarios'));
        $manager->persist($user);

        $draft = new Role();
        $draft->setName('ROLE_FROM_DRAFT_TO_PENDING');
        $draft->addDescription($this->generateTranslatedValue('en', 'Change status from draft to pending'));
        $draft->addDescription($this->generateTranslatedValue('fr', 'Modifier le status de brouillon à en attente'));
        $draft->addDescription($this->generateTranslatedValue('de', 'Ändern Sie den Status zu anstehenden Entwurf'));
        $draft->addDescription($this->generateTranslatedValue('es', 'Cambiar el estado de proyecto pendiente'));
        $draft->setFromStatus($this->getReference('status-draft'));
        $draft->setToStatus($this->getReference('status-pending'));
        $manager->persist($draft);

        $pending = new Role();
        $pending->setName('ROLE_FROM_PENDING_TO_PUBLISHED');
        $pending->addDescription($this->generateTranslatedValue('en', 'Change status from pending to published'));
        $pending->addDescription($this->generateTranslatedValue('fr', 'Modifier le status de en attente à publié'));
        $pending->addDescription($this->generateTranslatedValue('de', 'Ändern Sie den Status der ausstehenden veröffentlicht'));
        $pending->addDescription($this->generateTranslatedValue('es', 'Cambiar el estado de pendiente de publicación'));
        $pending->setFromStatus($this->getReference('status-pending'));
        $pending->setToStatus($this->getReference('status-published'));
        $manager->persist($pending);

        $published = new Role();
        $published->setName('ROLE_FROM_PUBLISHED_TO_DRAFT');
        $published->addDescription($this->generateTranslatedValue('en', 'Change status from published to draft'));
        $published->addDescription($this->generateTranslatedValue('fr', 'Modifier le status de publié à brouillon'));
        $published->addDescription($this->generateTranslatedValue('de', 'Ändern Sie den Status der veröffentlichten Entwurf'));
        $published->addDescription($this->generateTranslatedValue('es', 'Cambiar el estado de borrador publicado'));
        $published->setFromStatus($this->getReference('status-published'));
        $published->setToStatus($this->getReference('status-draft'));
        $manager->persist($published);

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
