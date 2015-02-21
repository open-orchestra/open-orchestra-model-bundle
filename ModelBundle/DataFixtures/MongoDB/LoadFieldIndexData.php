<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelBundle\Document\FieldIndex;

/**
 * Class loadFieldIndexData
 */
class LoadFieldIndexData implements FixtureInterface
{
    /**
     * Load data fixture
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $index1 = $this->generateFieldIndex1();
        $manager->persist($index1);

        $index2 = $this->generateFieldIndex2();
        $manager->persist($index2);

        $index3 = $this->generateFieldIndex3();
        $manager->persist($index3);

        $index4 = $this->generateFieldIndex4();
        $manager->persist($index4);

        $index5 = $this->generateFieldIndex5();
        $manager->persist($index5);

        $index6 = $this->generateFieldIndex6();
        $manager->persist($index6);

        $index7 = $this->generateFieldIndex7();
        $manager->persist($index7);

        $index8 = $this->generateFieldIndex8();
        $manager->persist($index8);

        $manager->flush();
    }

    /**
     * @return FieldIndex
     */
    protected function generateFieldIndex1()
    {
        $fieldIndex = new FieldIndex();

        $fieldIndex->setFieldName('title');
        $fieldIndex->setFieldType('s');
        $fieldIndex->setLink(true);

        return $fieldIndex;
    }

    /**
     * @return FieldIndex
     */
    protected function generateFieldIndex2()
    {
        $fieldIndex = new FieldIndex();

        $fieldIndex->setFieldName('news');
        $fieldIndex->setFieldType('t');
        $fieldIndex->setLink(false);

        return $fieldIndex;
    }

    /**
     * @return FieldIndex
     */
    protected function generateFieldIndex3()
    {
        $fieldIndex = new FieldIndex();

        $fieldIndex->setFieldName('author');
        $fieldIndex->setFieldType('ss');
        $fieldIndex->setLink(false);

        return $fieldIndex;
    }

    /**
     * @return FieldIndex
     */
    protected function generateFieldIndex4()
    {
        $fieldIndex = new FieldIndex();

        $fieldIndex->setFieldName('title');
        $fieldIndex->setFieldType('txt');
        $fieldIndex->setLink(false);

        return $fieldIndex;
    }

    /**
     * @return FieldIndex
     */
    protected function generateFieldIndex5()
    {
        $fieldIndex = new FieldIndex();

        $fieldIndex->setFieldName('image');
        $fieldIndex->setFieldType('img');
        $fieldIndex->setLink(false);

        return $fieldIndex;
    }

    /**
     * @return FieldIndex
     */
    protected function generateFieldIndex6()
    {
        $fieldIndex = new FieldIndex();

        $fieldIndex->setFieldName('intro');
        $fieldIndex->setFieldType('t');
        $fieldIndex->setLink(false);

        return $fieldIndex;
    }

    /**
     * @return FieldIndex
     */
    protected function generateFieldIndex7()
    {
        $fieldIndex = new FieldIndex();

        $fieldIndex->setFieldName('text');
        $fieldIndex->setFieldType('t');
        $fieldIndex->setLink(false);

        return $fieldIndex;
    }

    /**
     * @return FieldIndex
     */
    protected function generateFieldIndex8()
    {
        $fieldIndex = new FieldIndex();

        $fieldIndex->setFieldName('description');
        $fieldIndex->setFieldType('t');
        $fieldIndex->setLink(false);

        return $fieldIndex;
    }
}
