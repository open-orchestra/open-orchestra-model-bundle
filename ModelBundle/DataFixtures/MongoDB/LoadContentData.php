<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelBundle\Document\Content;
use OpenOrchestra\ModelBundle\Document\ContentAttribute;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;

/**
 * Class LoadContentData
 */
class LoadContentData extends AbstractFixture implements OrderedFixtureInterface, OrchestraFunctionalFixturesInterface
{
    protected $objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function load(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
        $languages = array('fr', "en");
        $this->persistMultiLanguageContent("generateCarR5", $languages);
        $this->persistMultiLanguageContent("generateCar206", $languages);
        $this->persistMultiLanguageContent("generateCarDs3", $languages);
        $this->persistMultiLanguageContent("generateCustomerConvenant", $languages);

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
     * @param string $methodName
     * @param array  $languages
     */
    protected  function persistMultiLanguageContent($methodName, array $languages)
    {
        foreach ($languages as $language) {
            $this->objectManager->persist($this->$methodName($language));
        }
    }

    /**
     * Generate a content attribute
     *
     * @param string $name
     * @param string $value
     * @param string $type
     *
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
     * @param string $language
     *
     * @return Content
     */
    public function generateCarR5($language)
    {
        $content = $this->addBaseContent("r5_3_portes", "car", 1);

        $attribute1 = $this->generateContentAttribute('car_name', 'R5');
        $attribute2 = $this->generateContentAttribute('description',
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean non feugiat sem.
             Aliquam a mauris tellus. In hac habitasse platea dictumst. Nunc eget interdum
             ante, id mollis diam. Suspendisse sed magna lectus. Aenean fringilla elementum
             lorem id suscipit. Phasellus feugiat tellus sapien, id tempus nisi ultrices ut.');

        $content->setName("R5 3 portes " . $language);
        $content->setLanguage($language);
        $content->setVersion(2);
        $content->addKeyword($this->getReference('keyword-lorem'));
        $content->setLinkedToSite(false);
        $content->setSiteId('2');

        $content->addAttribute($attribute1);
        $content->addAttribute($attribute2);

        return $content;
    }

    /**
     * @param string $language
     *
     * @return Content
     */
    public function generateCarDs3($language)
    {
        $content = $this->addBaseContent("ds_3", "car", 1);
        $content->setDeleted(false);
        $content->setName("DS 3 " . $language);
        $content->setLanguage($language);
        $content->setVersion(1);
        $content->addKeyword($this->getReference('keyword-lorem'));
        $content->setLinkedToSite(true);
        $content->setSiteId('2');

        $attribute1 = $this->generateContentAttribute('car_name', 'Ds3');
        $attribute2 = $this->generateContentAttribute('description',
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean non feugiat sem.
             Aliquam a mauris tellus. In hac habitasse platea dictumst. Nunc eget interdum
             ante, id mollis diam. Suspendisse sed magna lectus. Aenean fringilla elementum
             lorem id suscipit. Phasellus feugiat tellus sapien, id tempus nisi ultrices ut.');

        $content->addAttribute($attribute1);
        $content->addAttribute($attribute2);
        $this->setReference("ds_3_".$language, $content);

        return $content;
    }

    /**
     * @param string $language
     *
     * @return Content
     */
    public function generateCar206($language)
    {
        $content = $this->addBaseContent("206_3_portes", "car", 1);
        $content->setName("206 3 portes " . $language);
        $content->setLanguage($language);
        $content->setVersion(2);
        $content->addKeyword($this->getReference('keyword-lorem'));
        $content->addKeyword($this->getReference('keyword-sit'));
        $content->setLinkedToSite(false);
        $content->setSiteId('2');

        $attribute1 = $this->generateContentAttribute('car_name', '206');
        $attribute2 = $this->generateContentAttribute('description',
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean non feugiat sem.
            Aliquam a mauris tellus. In hac habitasse platea dictumst. Nunc eget interdum ante,
             id mollis diam. Suspendisse sed magna lectus. Aenean fringilla elementum lorem id
             suscipit. Phasellus feugiat tellus sapien, id tempus nisi ultrices ut.');

        $content->addAttribute($attribute1);
        $content->addAttribute($attribute2);

        return $content;
    }

    /**
     * @param string $language
     *
     * @return Content
     */
    public function generateCustomerConvenant($language)
    {
        $content = $this->addBaseContent("jean_paul", 'customer', 1);
        $content->setName("Jean-Paul");
        $content->setLanguage($language);
        $content->setVersion(2);
        $content->addKeyword($this->getReference('keyword-lorem'));
        $content->addKeyword($this->getReference('keyword-sit'));
        $content->setLinkedToSite(false);
        $content->setSiteId('2');

        $attribute1 = $this->generateContentAttribute('firstname', 'Jean-Claude');
        $attribute2 = $this->generateContentAttribute('lastname', 'Convenant');
        $attribute3 = $this->generateContentAttribute('identifier', 28, 'integer');

        $content->addAttribute($attribute1);
        $content->addAttribute($attribute2);
        $content->addAttribute($attribute3);

        return $content;
    }

    /**
     * @param string $id
     * @param string $type
     * @param int    $typeVersion
     *
     * @return Content
     */
    protected function addBaseContent($id, $type, $typeVersion)
    {
        $content = new Content();
        $content->setContentId($id);
        $content->setContentType($type);
        $content->setDeleted(false);
        $content->setCreatedBy('admin');
        $content->setStatus($this->getReference('status-published'));
        $content->setCurrentlyPublished(true);

        return $content;
    }
}
