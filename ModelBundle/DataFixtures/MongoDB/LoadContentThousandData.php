<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelBundle\Document\Content;
use OpenOrchestra\ModelBundle\Document\ContentAttribute;
use OpenOrchestra\ModelInterface\Model\ContentInterface;

/**
 * Class LoadContentThousandData
 */
class LoadContentThousandData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $objectManager
     */
    public function load(ObjectManager $objectManager)
    {
        for ($i = 0; $i < 250 ; $i++) {
            $objectManager->persist($this->generateNews($i, 1, 'fr'));
            // Versions
            for ($j = 2; $j < 5; $j++) {
                // Languages
                for ($k = 0; $k < 2; $k++) {
                    $language = 'fr';
                    if ($k === 1) {
                        $language = 'en';
                    }
                    $objectManager->persist($this->generateNews($i, $j, $language));
                }
            }
        }

        $objectManager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 530;
    }

    /**
     * @param string $name
     * @param string $value
     * @param string $type
     *
     * @return ContentAttribute
     */
    public function generateContentAttribute($name, $value, $type = 'text')
    {
        $attribute = new ContentAttribute();
        $attribute->setName($name);
        $attribute->setValue($value);
        $attribute->setStringValue($value);
        $attribute->setType($type);

        return $attribute;
    }

    /**
     * Generate a content
     *
     * @param string  $type
     * @param int     $id
     * @param string  $name
     * @param string  $language
     * @param int     $version
     * @param boolean $deleted
     *
     * @return Content
     */
    protected function generateContent($type, $id, $name, $language, $version, $deleted)
    {
        $content = new Content();

        $content->setContentId($id);
        $content->setContentType($type);
        $content->setDeleted($deleted);
        $content->setName($name);
        $content->setLanguage($language);
        $content->setVersion($version);
        $content->setSiteId('2');
        $date = new \DateTime("now");
        $content->setVersionName($content->getName().'_'. $content->getVersion(). '_'. $date->format("Y-m-d_H:i:s"));

        switch ($version) {
            case 2: $content->setStatus($this->getReference('status-pending'));
                break;
            case 4: $content->setStatus($this->getReference('status-draft'));
                break;
            default: $content->setStatus($this->getReference('status-published'));
                break;
        }

        return $content;
    }

    /**
     * Fill news attributes
     *
     * @param Content          $news
     * @param ContentAttribute $title
     * @param ContentAttribute $intro
     * @param ContentAttribute $text
     *
     * @return Content
     */
    protected function addNewsAttributes($news, $title, $intro, $text)
    {
        $news->addAttribute($title);
        $news->addAttribute($this->generateContentAttribute('publish_start', '2014-08-26', 'date'));
        $news->addAttribute($this->generateContentAttribute('publish_end', '2014-12-19', 'date'));
        $news->addAttribute($intro);
        $news->addAttribute($text);

        return $news;
    }

    /**
     * @param string  $index
     * @param int     $version
     * @param string  $language
     * @param boolean $deleted
     *
     * @return ContentInterface
     */
    public function generateNews($index, $version, $language, $deleted = false)
    {
        $title = $this->generateContentAttribute('title', 'New number' . $index . 'in language ' . $language);
        $intro = $this->generateContentAttribute('intro', 'This is the introduction for the news number ' . $index .'.');
        $text = $this->generateContentAttribute('text', 'Donec bibendum at nibh eget imperdiet. Mauris eget justo augue. Fusce fermentum iaculis erat, sollicitudin elementum enim sodales eu. Donec a ante tortor. Suspendisse a.', 'wysiwyg');
        $news = $this->generateContent('news', 'news-' . $index, 'News ' . $index, $language, $version, $deleted);

        return $this->addNewsAttributes($news, $title, $intro, $text);
    }
}
