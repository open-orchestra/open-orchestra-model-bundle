<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use OpenOrchestra\ModelBundle\Document\Content;
use OpenOrchestra\ModelBundle\Document\ContentAttribute;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;

/**
 * Class LoadDeletedSiteData
 */
class LoadDeletedSiteContentData extends AbstractFixture implements OrderedFixtureInterface, OrchestraFunctionalFixturesInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $customerFrContent = $this->generateCustomerContentFr();
        $manager->persist($customerFrContent);

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 540;
    }

    /**
     * @return Content
     */
    protected function generateCustomerContentFr()
    {
        $content = new Content();
        $content->setContentId("jsmith");
        $content->setContentType("customer");
        $content->setDeleted(false);
        $content->setCreatedBy('admin');
        $content->setStatus($this->getReference('status-published'));
        $content->setName("John Smith");
        $content->setLanguage("fr");
        $content->setVersion(1);
        $content->setLinkedToSite(true);
        $content->setSiteId('3');

        $attribute1 = $this->generateContentAttribute('firstname', 'John');
        $attribute2 = $this->generateContentAttribute('lastname', 'Smith');
        $attribute3 = $this->generateContentAttribute('identifier', 4987, 'integer');

        $content->addAttribute($attribute1);
        $content->addAttribute($attribute2);
        $content->addAttribute($attribute3);

        return $content;
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
}
