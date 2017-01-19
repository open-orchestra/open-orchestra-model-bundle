<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelBundle\Document\ContentType;
use OpenOrchestra\ModelBundle\Document\FieldType;
use OpenOrchestra\ModelBundle\Document\FieldOption;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;

/**
 * Class LoadContentTypeData
 */
class LoadContentTypeData extends AbstractFixture implements OrderedFixtureInterface, OrchestraFunctionalFixturesInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $new = $this->generateContentTypeNews();
        $manager->persist($new);

        $car = $this->generateContentTypeCar();
        $manager->persist($car);

        $customer = $this->generateContentTypeCustomer();
        $manager->persist($customer);

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 500;
    }

    /**
     * @return array
     */
    protected function genereteDefaultListable()
    {
        return array(
            'name'           => true,
            'status_label'   => false,
            'linked_to_site' => true,
            'created_at'     => true,
            'created_by'     => true,
            'updated_at'     => false,
            'updated_by'     => false,
        );
    }

    /**
     * Generate a field type
     *
     * @param string $fieldType
     * @param string $fieldId
     * @param array $labels
     *
     * @return FieldType
     */
    protected function generateField($fieldType, $fieldId, array $labels)
    {
        $field = new FieldType();
        $field->setType($fieldType);
        $field->setFieldId($fieldId);
        $field->setDefaultValue(null);
        $field->setSearchable(true);
        $field->setLabels($labels);

        return $field;
    }

    /**
     * Generate a field option
     *
     * @param string $key
     * @param string|int|array $value
     *
     * @return FieldOption
     */
    protected function generateOption($key, $value)
    {
        $option = new FieldOption();
        $option->setKey($key);
        $option->setValue($value);

        return $option;
    }

    /**
     * @return ContentType
     */
    protected function generateContentTypeNews()
    {
        $maxLengthOption = $this->generateOption('max_length', 25);
        $required = $this->generateOption('required', true);
        $dateWidgetOption = $this->generateOption('widget', 'single_text');
        $dateInputOption = $this->generateOption('input', 'string');
        $formatOption = $this->generateOption('format', 'yyyy-MM-dd');

        /* TITLE */

        $newsTitle = $this->generateField('text', 'title', array('en' => 'Title', 'fr' => 'Titre'));
        $newsTitle->setFieldTypeSearchable('text');
        $newsTitle->addOption($maxLengthOption);
        $newsTitle->addOption($required);

        /* BEGINING DATE */

        $newBeginning = $this->generateField('date', 'publish_start', array('en' => 'Publicated from (yyyy-MM-dd)', 'fr' => 'Publié du (aaaa-MM-jj)'));
        $newBeginning->addOption($required);
        $newBeginning->addOption($dateWidgetOption);
        $newBeginning->addOption($dateInputOption);
        $newBeginning->addOption($formatOption);
        $newBeginning->setFieldTypeSearchable('date');

        /* ENDING DATE */

        $newEnding = $this->generateField('date', 'publish_end', array('en' => 'till (yyyy-MM-dd)', 'fr' => 'au (aaaa-MM-jj)'));
        $newEnding->addOption($required);
        $newEnding->addOption($dateWidgetOption);
        $newEnding->addOption($dateInputOption);
        $newEnding->addOption($formatOption);
        $newEnding->setFieldTypeSearchable('date');

        /* IMAGE */

        $newImage = $this->generateField('orchestra_media', 'image', array('en' => 'Image', 'fr' => 'Image'));
        $newImage->setFieldTypeSearchable('text');

        /* INTRODUCTION */

        $newsIntro = $this->generateField('text', 'intro', array('en' => 'Introduction', 'fr' => 'Introduction'));
        $newsIntro->addOption($maxLengthOption);
        $newsIntro->addOption($required);
        $newsIntro->setFieldTypeSearchable('text');

        /* TEXT */

        $newsText = $this->generateField('tinymce', 'text', array('en' => 'Text', 'fr' => 'Texte'));
        $newsText->setFieldTypeSearchable('text');

        /* CONTENT TYPE */

        $news = new ContentType();
        $news->setContentTypeId('news');
        $news->addName('en', 'News');
        $news->addName('fr', 'Actualité');
        $news->setDefiningStatusable(true);
        $news->setDefiningVersionable(true);
        $news->setDeleted(false);
        $news->setVersion(1);
        $news->setDefaultListable($this->genereteDefaultListable());

        $news->addFieldType($newsTitle);
        $news->addFieldType($newBeginning);
        $news->addFieldType($newEnding);
        $news->addFieldType($newImage);
        $news->addFieldType($newsIntro);
        $news->addFieldType($newsText);

        return $news;
    }

    /**
     * @return ContentType
     */
    protected function generateContentTypeCar()
    {
        $maxLengthOption = $this->generateOption('max_length', 25);

        $required = $this->generateOption('required', true);

        $carName = new FieldType();
        $carName->setFieldId('car_name');
        $carName->addLabel('en', 'Name');
        $carName->addLabel('fr', 'Nom');
        $carName->setDefaultValue('');
        $carName->setSearchable(true);
        $carName->setType('text');
        $carName->addOption($maxLengthOption);
        $carName->addOption($required);
        $carName->setFieldTypeSearchable('text');

        $carDescription = new FieldType();
        $carDescription->setFieldId('description');
        $carDescription->addLabel('en', 'Description');
        $carDescription->addLabel('fr', 'Description');
        $carDescription->setDefaultValue('');
        $carDescription->setSearchable(true);
        $carDescription->setType('text');
        $carDescription->addOption($maxLengthOption);
        $carDescription->addOption($required);
        $carDescription->setFieldTypeSearchable('text');

        $car = new ContentType();
        $car->setContentTypeId('car');
        $car->addName('en', 'Car');
        $car->addName('fr', 'Voiture');
        $car->setDefiningStatusable(true);
        $car->setDefiningVersionable(true);
        $car->setDeleted(false);
        $car->setVersion(2);
        $car->setDefaultListable($this->genereteDefaultListable());

        $car->addFieldType($carName);
        $car->addFieldType($carDescription);

        return $car;
    }

    /**
     * @return ContentType
     */
    protected function generateContentTypeCustomer()
    {
        $maxLengthOption = $this->generateOption('max_length', 25);
        $required = $this->generateOption('required', true);

        $customerFirstName = new FieldType();
        $customerFirstName->setFieldId('firstname');
        $customerFirstName->addLabel('en', 'Firstname');
        $customerFirstName->addLabel('fr', 'Prénom');
        $customerFirstName->setDefaultValue('');
        $customerFirstName->setSearchable(true);
        $customerFirstName->setType('text');
        $customerFirstName->addOption($maxLengthOption);
        $customerFirstName->addOption($required);
        $customerFirstName->setFieldTypeSearchable('text');

        $customerLastName = new FieldType();
        $customerLastName->setFieldId('lastname');
        $customerLastName->addLabel('en', 'Lastname');
        $customerLastName->addLabel('fr', 'Nom de famille');
        $customerLastName->setDefaultValue('');
        $customerLastName->setSearchable(true);
        $customerLastName->setType('text');
        $customerLastName->addOption($maxLengthOption);
        $customerLastName->addOption($required);
        $customerLastName->setFieldTypeSearchable('text');

        $customerIdentifier = new FieldType();
        $customerIdentifier->setFieldId('identifier');
        $customerIdentifier->addLabel('en', 'Identifier');
        $customerIdentifier->addLabel('fr', 'Identifiant');
        $customerIdentifier->setDefaultValue(0);
        $customerIdentifier->setSearchable(false);
        $customerIdentifier->setType('integer');
        $customerIdentifier->addOption($maxLengthOption);
        $customerIdentifier->addOption($required);
        $customerIdentifier->setFieldTypeSearchable('number');

        $customer = new ContentType();
        $customer->setContentTypeId('customer');
        $customer->addName('en', 'Customer');
        $customer->addName('fr', 'Client');
        $customer->setDefiningStatusable(true);
        $customer->setDefiningVersionable(true);
        $customer->setDeleted(false);
        $customer->setVersion(1);
        $customer->setDefaultListable($this->genereteDefaultListable());

        $customer->addFieldType($customerFirstName);
        $customer->addFieldType($customerLastName);
        $customer->addFieldType($customerIdentifier);

        return $customer;
    }
}
