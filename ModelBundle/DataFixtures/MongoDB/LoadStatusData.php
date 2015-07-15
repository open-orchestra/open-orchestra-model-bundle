<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelBundle\Document\Status;
use OpenOrchestra\ModelBundle\Document\TranslatedValue;

/**
 * Class LoadStatusData
 */
class LoadStatusData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $manager->persist($this->loadStatus('Draft', 'Brouillon', 'draft', 'green'));
        $manager->persist($this->loadStatus('Pending', 'En attente', 'pending', 'orange'));
        $manager->persist($this->loadStatus('Published', 'Publié', 'published', 'red'));

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

    /**
     * @param string $enName
     * @param string $frName
     * @param string $name
     * @param string $color
     * @param bool   $published
     *
     * @return Status
     */
    protected function loadStatus($enName, $frName, $name, $color, $published = false)
    {
        $value = new Status();
        $value->setName($name);
        $value->setPublished($published);
        $value->addLabel($this->generateTranslatedValue('en', $enName));
        $value->addLabel($this->generateTranslatedValue('fr', $frName));
        $value->setDisplayColor($color);

        $this->addReference('status-' . $name, $value);

        return $value;
    }

    /**
     * @param string $language
     * @param string $value
     *
     * @return TranslatedValue
     */
    protected function generateTranslatedValue($language, $value)
    {
        $draftEn = new TranslatedValue();
        $draftEn->setLanguage($language);
        $draftEn->setValue($value);

        return $draftEn;
    }
}
