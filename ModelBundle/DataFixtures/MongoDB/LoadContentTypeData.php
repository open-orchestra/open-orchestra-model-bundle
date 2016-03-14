<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelBundle\Document\ContentType;
use OpenOrchestra\ModelBundle\Document\FieldType;
use OpenOrchestra\ModelBundle\Document\FieldOption;
use OpenOrchestra\ModelBundle\Document\TranslatedValue;
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
            'version'        => false,
            'language'       => false,
            'linked_to_site' => true,
            'created_at'     => true,
            'created_by'     => true,
            'updated_at'     => false,
            'updated_by'     => false,
        );
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

    /**
     * Generate a field type
     *
     * @param string $fieldType
     * @param string $fieldId
     * @param array $labels
     *
     * @return FieldType
     */
    protected function generateField($fieldType, $fieldId, $labels)
    {
        $field = new FieldType();
        $field->setType($fieldType);
        $field->setFieldId($fieldId);
        $field->setDefaultValue(null);
        $field->setSearchable(true);
        foreach ($labels as $label) {
            $field->addLabel($label);
        }

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

        $enLabel = $this->generateTranslatedValue('en', 'Title');
        $frLabel = $this->generateTranslatedValue('fr', 'Titre');

        $newsTitle = $this->generateField('text', 'title', array($enLabel, $frLabel));
        $newsTitle->setFieldTypeSearchable('text');
        $newsTitle->addOption($maxLengthOption);
        $newsTitle->addOption($required);

        /* BEGINING DATE */

        $enLabel = $this->generateTranslatedValue('en', 'Publicated from (yyyy-MM-dd)');
        $frLabel = $this->generateTranslatedValue('fr', 'Publié du (aaaa-MM-jj)');

        $newBeginning = $this->generateField('date', 'publish_start', array($enLabel, $frLabel));
        $newBeginning->addOption($required);
        $newBeginning->addOption($dateWidgetOption);
        $newBeginning->addOption($dateInputOption);
        $newBeginning->addOption($formatOption);
        $newBeginning->setFieldTypeSearchable('date');

        /* ENDING DATE */

        $enLabel = $this->generateTranslatedValue('en', 'till (yyyy-MM-dd)');
        $frLabel = $this->generateTranslatedValue('fr', 'au (aaaa-MM-jj)');

        $newEnding = $this->generateField('date', 'publish_end', array($enLabel, $frLabel));
        $newEnding->addOption($required);
        $newEnding->addOption($dateWidgetOption);
        $newEnding->addOption($dateInputOption);
        $newEnding->addOption($formatOption);
        $newEnding->setFieldTypeSearchable('date');

        /* IMAGE */

        $enLabel = $this->generateTranslatedValue('en', 'Image');
        $frLabel = $this->generateTranslatedValue('fr', 'Image');

        $newImage = $this->generateField('orchestra_media', 'image', array($enLabel, $frLabel));
        $newImage->setFieldTypeSearchable('text');

        /* INTRODUCTION */

        $enLabel = $this->generateTranslatedValue('en', 'Introduction');
        $frLabel = $this->generateTranslatedValue('fr', 'Introduction');

        $newsIntro = $this->generateField('text', 'intro', array($enLabel, $frLabel));
        $newsIntro->addOption($maxLengthOption);
        $newsIntro->addOption($required);
        $newsIntro->setFieldTypeSearchable('text');

        /* TEXT */

        $enLabel = $this->generateTranslatedValue('en', 'Text');
        $frLabel = $this->generateTranslatedValue('fr', 'Texte');

        $newsText = $this->generateField('tinymce', 'text', array($enLabel, $frLabel));
        $newsText->setFieldTypeSearchable('text');

        /* CONTENT TYPE */

        $enLabel = $this->generateTranslatedValue('en', 'News');
        $frLabel = $this->generateTranslatedValue('fr', 'Actualité');

        $news = new ContentType();
        $news->setContentTypeId('news');
        $news->addName($enLabel);
        $news->addName($frLabel);
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

        $enLabel = new TranslatedValue();
        $enLabel->setLanguage('en');
        $enLabel->setValue('Name');

        $frLabel = new TranslatedValue();
        $frLabel->setLanguage('fr');
        $frLabel->setValue('Nom');

        $carName = new FieldType();
        $carName->setFieldId('car_name');
        $carName->addLabel($enLabel);
        $carName->addLabel($frLabel);
        $carName->setDefaultValue('');
        $carName->setSearchable(true);
        $carName->setType('text');
        $carName->addOption($maxLengthOption);
        $carName->addOption($required);
        $carName->setFieldTypeSearchable('text');

        $enLabel = new TranslatedValue();
        $enLabel->setLanguage('en');
        $frLabel = new TranslatedValue();
        $frLabel->setLanguage('fr');
        $enLabel->setValue('Description');
        $frLabel->setValue('Description');

        $carDescription = new FieldType();
        $carDescription->setFieldId('description');
        $carDescription->addLabel($enLabel);
        $carDescription->addLabel($frLabel);
        $carDescription->setDefaultValue('');
        $carDescription->setSearchable(true);
        $carDescription->setType('text');
        $carDescription->addOption($maxLengthOption);
        $carDescription->addOption($required);
        $carDescription->setFieldTypeSearchable('text');

        $en = new TranslatedValue();
        $en->setLanguage('en');
        $en->setValue('Car');

        $fr = new TranslatedValue();
        $fr->setLanguage('fr');
        $fr->setValue('Voiture');

        $car = new ContentType();
        $car->setContentTypeId('car');
        $car->addName($en);
        $car->addName($fr);
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

        $enLabel = new TranslatedValue();
        $enLabel->setLanguage('en');
        $frLabel = new TranslatedValue();
        $frLabel->setLanguage('fr');
        $enLabel->setValue('Firstname');
        $frLabel->setValue('Prénom');

        $customerFirstName = new FieldType();
        $customerFirstName->setFieldId('firstname');
        $customerFirstName->addLabel($enLabel);
        $customerFirstName->addLabel($frLabel);
        $customerFirstName->setDefaultValue('');
        $customerFirstName->setSearchable(true);
        $customerFirstName->setType('text');
        $customerFirstName->addOption($maxLengthOption);
        $customerFirstName->addOption($required);
        $customerFirstName->setFieldTypeSearchable('text');

        $enLabel = new TranslatedValue();
        $enLabel->setLanguage('en');
        $frLabel = new TranslatedValue();
        $frLabel->setLanguage('fr');
        $enLabel->setValue('Lastname');
        $frLabel->setValue('Nom de famille');

        $customerLastName = new FieldType();
        $customerLastName->setFieldId('lastname');
        $customerLastName->addLabel($enLabel);
        $customerLastName->addLabel($frLabel);
        $customerLastName->setDefaultValue('');
        $customerLastName->setSearchable(true);
        $customerLastName->setType('text');
        $customerLastName->addOption($maxLengthOption);
        $customerLastName->addOption($required);
        $customerLastName->setFieldTypeSearchable('text');

        $enLabel = new TranslatedValue();
        $enLabel->setLanguage('en');
        $frLabel = new TranslatedValue();
        $frLabel->setLanguage('fr');
        $enLabel->setValue('Identifier');
        $frLabel->setValue('Identifiant');

        $customerIdentifier = new FieldType();
        $customerIdentifier->setFieldId('identifier');
        $customerIdentifier->addLabel($enLabel);
        $customerIdentifier->addLabel($frLabel);
        $customerIdentifier->setDefaultValue(0);
        $customerIdentifier->setSearchable(false);
        $customerIdentifier->setType('integer');
        $customerIdentifier->addOption($maxLengthOption);
        $customerIdentifier->addOption($required);
        $customerIdentifier->setFieldTypeSearchable('number');

        $en = new TranslatedValue();
        $en->setLanguage('en');
        $en->setValue('Customer');

        $fr = new TranslatedValue();
        $fr->setLanguage('fr');
        $fr->setValue('Client');

        $customer = new ContentType();
        $customer->setContentTypeId('customer');
        $customer->addName($en);
        $customer->addName($fr);
        $customer->setDeleted(false);
        $customer->setVersion(1);
        $customer->setDefaultListable($this->genereteDefaultListable());

        $customer->addFieldType($customerFirstName);
        $customer->addFieldType($customerLastName);
        $customer->addFieldType($customerIdentifier);

        return $customer;
    }
}
