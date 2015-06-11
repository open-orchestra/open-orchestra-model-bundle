<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelBundle\Document\Content;
use OpenOrchestra\ModelBundle\Document\ContentAttribute;
use OpenOrchestra\ModelBundle\Document\EmbedKeyword;

/**
 * Class LoadContentData
 */
class LoadContentData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $objectManager
     */
    public function load(ObjectManager $objectManager)
    {
        $objectManager->persist($this->generateCarR5('fr'));
        $objectManager->persist($this->generateCarR5('en'));
        $objectManager->persist($this->generateCar206('fr'));
        $objectManager->persist($this->generateCar206('en'));
        $objectManager->persist($this->generateCarDs3('fr'));
        $objectManager->persist($this->generateCarDs3('en'));

        $objectManager->persist($this->generateCustomerConvenant('fr'));
        $objectManager->persist($this->generateCustomerConvenant('en'));

        $objectManager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 510;
    }

    /**
     * Generate a content attribute
     *
     * @param string $name
     * @param string $value
     *
     * @return ContentAttribute
     */
    protected function generateContentAttribute($name, $value)
    {
        $attribute = new ContentAttribute();
        $attribute->setName($name);
        $attribute->setValue($value);

        return $attribute;
    }

    /**
     * @return Content
     */
    public function generateCarR5($language)
    {
        $content = new Content();

        $attribute1 = $this->generateContentAttribute('car_name', 'R5');
        $attribute2 = $this->generateContentAttribute('image', 'r5.png');
        $attribute3 = $this->generateContentAttribute('description', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean non feugiat sem. Aliquam a mauris tellus. In hac habitasse platea dictumst. Nunc eget interdum ante, id mollis diam. Suspendisse sed magna lectus. Aenean fringilla elementum lorem id suscipit. Phasellus feugiat tellus sapien, id tempus nisi ultrices ut.');

        $content->setContentId("r5_3_portes");
        $content->setContentType("car");
        $content->setContentTypeVersion(1);
        $content->setDeleted(false);
        $content->setName("R5 3 portes " . $language);
        $content->setLanguage($language);
        $content->setStatus($this->getReference('status-published'));
        $content->setVersion(2);
        $content->addKeyword(EmbedKeyword::createFromKeyword($this->getReference('keyword-lorem')));
        $content->setSiteLinked(false);
        $content->setSiteId('2');

        $content->addAttribute($attribute1);
        $content->addAttribute($attribute2);
        $content->addAttribute($attribute3);

        return $content;
    }

    /**
     * @return Content
     */
    public function generateCarDs3($language)
    {
        $content = new Content();

        $attribute1 = $this->generateContentAttribute('car_name', 'Ds3');
        $attribute2 = $this->generateContentAttribute('image', 'ds3.png');
        $attribute3 = $this->generateContentAttribute('description', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean non feugiat sem. Aliquam a mauris tellus. In hac habitasse platea dictumst. Nunc eget interdum ante, id mollis diam. Suspendisse sed magna lectus. Aenean fringilla elementum lorem id suscipit. Phasellus feugiat tellus sapien, id tempus nisi ultrices ut.');

        $content->setContentId("ds_3");
        $content->setContentType("car");
        $content->setContentTypeVersion(1);
        $content->setDeleted(false);
        $content->setName("DS 3 " . $language);
        $content->setLanguage($language);
        $content->setStatus($this->getReference('status-published'));
        $content->setVersion(1);
        $content->addKeyword(EmbedKeyword::createFromKeyword($this->getReference('keyword-lorem')));
        $content->setSiteLinked(true);
        $content->setSiteId('2');

        $content->addAttribute($attribute1);
        $content->addAttribute($attribute2);
        $content->addAttribute($attribute3);

        return $content;
    }

    /**
     * @return Content
     */
    public function generateCar206($language)
    {
        $content = new Content();

        $attribute1 = $this->generateContentAttribute('car_name', '206');
        $attribute2 = $this->generateContentAttribute('image', '206.png');
        $attribute3 = $this->generateContentAttribute('description', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean non feugiat sem. Aliquam a mauris tellus. In hac habitasse platea dictumst. Nunc eget interdum ante, id mollis diam. Suspendisse sed magna lectus. Aenean fringilla elementum lorem id suscipit. Phasellus feugiat tellus sapien, id tempus nisi ultrices ut.');

        $content->setContentId("206_3_portes");
        $content->setContentType("car");
        $content->setContentTypeVersion(1);
        $content->setDeleted(false);
        $content->setName("206 3 portes " . $language);
        $content->setLanguage($language);
        $content->setStatus($this->getReference('status-published'));
        $content->setVersion(2);
        $content->addKeyword(EmbedKeyword::createFromKeyword($this->getReference('keyword-lorem')));
        $content->addKeyword(EmbedKeyword::createFromKeyword($this->getReference('keyword-sit')));
        $content->setSiteLinked(false);
        $content->setSiteId('1');

        $content->addAttribute($attribute1);
        $content->addAttribute($attribute2);
        $content->addAttribute($attribute3);

        return $content;
    }

    /**
     * @return Content
     */
    public function generateCustomerConvenant($language)
    {
        $content = new Content();

        $attribute1 = $this->generateContentAttribute('firstname', 'Jean-Claude');
        $attribute2 = $this->generateContentAttribute('lastname', 'Convenant');
        $attribute3 = $this->generateContentAttribute('identifier', 28);

        $content->setContentId("jean_paul");
        $content->setContentType("customer");
        $content->setContentTypeVersion(1);
        $content->setDeleted(false);
        $content->setName("Jean-Paul");
        $content->setLanguage($language);
        $content->setStatus($this->getReference('status-published'));
        $content->setVersion(2);
        $content->addKeyword(EmbedKeyword::createFromKeyword($this->getReference('keyword-lorem')));
        $content->setSiteLinked(false);
        $content->setSiteId('2');

        $content->addAttribute($attribute1);
        $content->addAttribute($attribute2);
        $content->addAttribute($attribute3);

        return $content;
    }
}
