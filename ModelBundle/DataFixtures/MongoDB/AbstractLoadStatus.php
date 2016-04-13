<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 13/04/16
 * Time: 16:11
 */

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use OpenOrchestra\ModelBundle\Document\Status;
use OpenOrchestra\ModelBundle\Document\TranslatedValue;

/**
 * Class AbstractLoadStatus
 */
abstract class AbstractLoadStatus extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param string $enName
     * @param string $frName
     * @param string $name
     * @param string $color
     * @param bool   $published
     * @param bool   $initial
     *
     * @return Status
     */
    protected function loadStatus($enName, $frName, $name, $color, $published = false, $initial = false)
    {
        $value = new Status();
        $value->setName($name);
        $value->setPublished($published);
        $value->setInitial($initial);
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
        $translatedValue = new TranslatedValue();
        $translatedValue->setLanguage($language);
        $translatedValue->setValue($value);

        return $translatedValue;
    }
}
