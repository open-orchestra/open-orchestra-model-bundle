<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelBundle\Document\Content;
use OpenOrchestra\ModelBundle\Document\ContentAttribute;
use OpenOrchestra\ModelBundle\Document\EmbedKeyword;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;

/**
 * Class LoadContentNewsData
 */
class LoadContentNewsData extends AbstractFixture implements OrderedFixtureInterface, OrchestraFunctionalFixturesInterface
{
    /**
     * @param ObjectManager $objectManager
     */
    public function load(ObjectManager $objectManager)
    {
        $objectManager->persist($this->generateFirstNews());
        $objectManager->persist($this->generateSecondNews());
        $objectManager->persist($this->generateBienvenueFrance());
        $objectManager->persist($this->generateLoremIpsum());

        $objectManager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 520;
    }

    /**
     * Generate a content attribute
     *
     * @param string $name
     * @param string $value
     * @param string $type
     * @return ContentAttribute
     */
    protected function generateContentAttribute($name, $value, $type = 'text')
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
     * @param string $type
     * @param int    $id
     * @param string $name
     * @param string $language
     *
     * @return Content
     */
    protected function generateContent($type, $id, $name, $language)
    {
        $content = new Content();

        $content->setContentId($id);
        $content->setContentType($type);
        $content->setContentTypeVersion(1);
        $content->setDeleted(false);
        $content->setName($name);
        $content->setLanguage($language);
        $content->setStatus($this->getReference('status-published'));
        $content->setVersion(1);
        $content->setSiteId('2');

        return $content;
    }

    /**
     * Fill news attributes
     *
     * @param Content          $news
     * @param ContentAttribute $title
     * @param ContentAttribute $start
     * @param ContentAttribute $end
     * @param ContentAttribute $image
     * @param ContentAttribute $intro
     * @param ContentAttribute $text
     *
     * @return Content
     */
    protected function addNewsAttributes($news, $title, $start, $end, $image, $intro, $text)
    {
        $news->addAttribute($title);
        $news->addAttribute($start);
        $news->addAttribute($end);
        $news->addAttribute($image);
        $news->addAttribute($intro);
        $news->addAttribute($text);

        return $news;
    }

    /**
     * @return Content
     */
    public function generateFirstNews()
    {
        $title = $this->generateContentAttribute('title', 'Welcome');
        $image = $this->generateContentAttribute('image', '', 'orchestra_media');
        $intro = $this->generateContentAttribute('intro', 'Bienvenue sur le site d\'openorchestra');
        $text = $this->generateContentAttribute('text', 'A l’occasion de la sortie du projet, nous serons
         présents au Symfony live 2015. Venez nous voir sur notre stand dédié !', 'wysiwyg');
        $start = $this->generateContentAttribute('publish_start', '2014-08-26', 'date');
        $end = $this->generateContentAttribute('publish_end', '2014-12-19', 'date');
        $welcome = $this->generateContent('news', 'welcome', 'Welcome', 'fr');
        $welcome->addKeyword(EmbedKeyword::createFromKeyword($this->getReference('keyword-sit')));

        return $this->addNewsAttributes($welcome, $title, $start, $end, $image, $intro, $text);
    }

    /**
     * @return Content
     */
    public function generateSecondNews()
    {
        $title = $this->generateContentAttribute('title', 'Notre vision');
        $image = $this->generateContentAttribute('image', '', 'orchestra_media');
        $intro = $this->generateContentAttribute('intro',
            'Open orchestra propulse votre contenu web vers des hauteurs stratosphériques.');
        $text = $this->generateContentAttribute('text',
            'Essayez dès à présent Open orchestra pour votre nouveau projet, gagnez en productivité
            et en qualité.', 'tinymce');
        $start = $this->generateContentAttribute('publish_start', '2014-08-16', 'date');
        $end = $this->generateContentAttribute('publish_end', '2014-12-19', 'date');
        $vision = $this->generateContent('news', 'notre_vision', 'Notre vision', 'fr');
        $vision->addKeyword(EmbedKeyword::createFromKeyword($this->getReference('keyword-sit')));
        $vision->addKeyword(EmbedKeyword::createFromKeyword($this->getReference('keyword-lorem')));

        return $this->addNewsAttributes($vision, $title, $start, $end, $image, $intro, $text);
    }

    /**
     * @return Content
     */
    public function generateBienvenueFrance()
    {
        $title = $this->generateContentAttribute('title', 'Bien vivre en France');
        $image = $this->generateContentAttribute('image', '', 'orchestra_media');
        $intro = $this->generateContentAttribute('intro',
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit.
         -Aenean non feugiat sem. Aliquam a mauris tellus. In hac habitasse platea dictumst. Nunc eget interdum ante,
          id mollis diam. Suspendisse sed magna lectus. Aenean fringilla elementum lorem id suscipit. Phasellus feugiat
           tellus sapien, id tempus nisi ultrices ut.');
        $text = $this->generateContentAttribute('text',
            'Cras non dui id neque mattis molestie. Quisque feugiat metus in
             est aliquet, nec convallis ante blandit. Suspendisse tincidunt tortor et tellus eleifend bibendum. Fusce
             fringilla mauris dolor, quis tempus diam tempus eu. Morbi enim orci, aliquam at sapien eu, dignissim commodo
             enim. Nulla ultricies erat non facilisis feugiat. Quisque fringilla ante lacus, vitae viverra magna aliquam
             non. Pellentesque quis diam suscipit, tincidunt felis eget, mollis mauris. Nulla facilisi.<br /><br />Nunc
             tincidunt pellentesque suscipit. Donec tristique massa at turpis fringilla, non aliquam ante luctus. Nam in
             felis tristique, scelerisque magna eget, sagittis purus. Maecenas malesuada placerat rutrum. Vestibulum sem
             urna, pharetra et fermentum a, iaculis quis augue. Ut ac neque mauris. In vel risus dui. Fusce lacinia a velit
             vitae condimentum.',
            'wysiwyg');
        $start = $this->generateContentAttribute('publish_start', '2014-07-25', 'date');
        $end = $this->generateContentAttribute('publish_end', '2014-10-19', 'date');
        $bienvenueFrance = $this->generateContent('news', 'bien_vivre_en_france', 'Bien vivre en France', 'fr');

        return $this->addNewsAttributes($bienvenueFrance, $title, $start, $end, $image, $intro, $text);
    }

    /**
     * @return Content
     */
    public function generateLoremIpsum()
    {
        $title = $this->generateContentAttribute('title', 'lorem Ipsum');
        $image = $this->generateContentAttribute('image', '', 'orchestra_media');
        $intro = $this->generateContentAttribute('intro', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Aenean non feugiat sem. Aliquam a mauris tellus. In hac habitasse platea dictumst. Nunc eget interdum ante, id
        mollis diam. Suspendisse sed magna lectus. Aenean fringilla elementum lorem id suscipit. Phasellus feugiat
        tellus sapien, id tempus nisi ultrices ut.', 'wysiwyg');
        $text = $this->generateContentAttribute('text', 'Cras non dui id neque mattis molestie. Quisque feugiat metus
        in est aliquet, nec convallis ante blandit. Suspendisse tincidunt tortor et tellus eleifend bibendum. Fusce
        fringilla mauris dolor, quis tempus diam tempus eu. Morbi enim orci, aliquam at sapien eu, dignissim commodo
        enim. Nulla ultricies erat non facilisis feugiat. Quisque fringilla ante lacus, vitae viverra magna aliquam non.
        Pellentesque quis diam suscipit, tincidunt felis eget, mollis mauris. Nulla facilisi.<br /><br />Nunc tincidunt
        pellentesque suscipit. Donec tristique massa at turpis fringilla, non aliquam ante luctus. Nam in felis tristique,
        scelerisque magna eget, sagittis purus. Maecenas malesuada placerat rutrum. Vestibulum sem urna, pharetra et
        fermentum a, iaculis quis augue. Ut ac neque mauris. In vel risus dui. Fusce lacinia a velit vitae condimentum.',
            'wysiwyg');
        $start = $this->generateContentAttribute('publish_start', '2014-08-25', 'date');
        $end = $this->generateContentAttribute('publish_end', '2014-11-19', 'date');
        $loremIpsum = $this->generateContent('news', 'lorem_ipsum', 'Lorem ipsum', 'fr');

        return $this->addNewsAttributes($loremIpsum, $title, $start, $end, $image, $intro, $text);
    }
}
