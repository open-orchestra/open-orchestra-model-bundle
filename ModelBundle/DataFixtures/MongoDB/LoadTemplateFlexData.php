<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelBundle\Document\AreaFlex;
use OpenOrchestra\ModelBundle\Document\Template;
use OpenOrchestra\ModelBundle\Document\TemplateFlex;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;
use OpenOrchestra\ModelInterface\Model\AreaFlexInterface;

/**
 * Class LoadTemplateFlexData
 */
class LoadTemplateFlexData extends AbstractFixture implements OrderedFixtureInterface, OrchestraFunctionalFixturesInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $generic = $this->homepageTemplate();
        $manager->persist($generic);

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 220;
    }

    /**
     * @return Template
     */
    protected function homepageTemplate()
    {
        $root = new AreaFlex();
        $root->setAreaType(AreaFlexInterface::TYPE_ROOT);
        $root->setAreaId(AreaFlexInterface::ROOT_AREA_ID);
        $root->setLabel(AreaFlexInterface::ROOT_AREA_LABEL);

        $header = new AreaFlex();
        $header->setAreaType(AreaFlexInterface::TYPE_ROW);
        $header->setAreaId('root_row_1');

        $columnLogo = new AreaFlex();
        $columnLogo->setAreaType(AreaFlexInterface::TYPE_COLUMN);
        $columnLogo->setAreaId('root_row_1_column_1');
        $columnLogo->setLabel('Logo');
        $columnLogo->setWidth('1');

        $columnMenu = new AreaFlex();
        $columnMenu->setAreaType(AreaFlexInterface::TYPE_COLUMN);
        $columnMenu->setAreaId('root_row_1_column_2');
        $columnMenu->setLabel('Menu');
        $columnMenu->setWidth('3');

        $header->addArea($columnLogo);
        $header->addArea($columnMenu);

        $main = new AreaFlex();
        $main->setAreaType(AreaFlexInterface::TYPE_ROW);
        $main->setAreaId('root_row_2');

        $columnMain = new AreaFlex();
        $columnMain->setAreaType(AreaFlexInterface::TYPE_COLUMN);
        $columnMain->setAreaId('root_row_2_column_1');
        $columnMain->setLabel('Content');
        $columnMain->setWidth('1');

        $main->addArea($columnMain);

        $footer = new AreaFlex();
        $footer->setAreaType(AreaFlexInterface::TYPE_ROW);
        $footer->setAreaId('root_row_3');

        $footer1 = new AreaFlex();
        $footer1->setAreaType(AreaFlexInterface::TYPE_COLUMN);
        $footer1->setAreaId('root_row_3_column_1');
        $footer1->setLabel('Footer 1');
        $footer1->setWidth('1');

        $footer2 = new AreaFlex();
        $footer2->setAreaType(AreaFlexInterface::TYPE_COLUMN);
        $footer2->setAreaId('root_row_3_column_2');
        $footer2->setLabel('Footer 2');
        $footer2->setWidth('1');

        $footer3 = new AreaFlex();
        $footer3->setAreaType(AreaFlexInterface::TYPE_COLUMN);
        $footer3->setAreaId('root_row_3_column_3');
        $footer3->setLabel('Footer 3');
        $footer3->setWidth('1');

        $footer->addArea($footer1);
        $footer->addArea($footer2);
        $footer->addArea($footer3);

        $root->addArea($header);
        $root->addArea($main);
        $root->addArea($footer);

        $generic = new TemplateFlex();
        $generic->setTemplateId('template_home_flex');
        $generic->setSiteId('1');
        $generic->setName('Homepage Template');
        $generic->setDeleted(false);

        $generic->setArea($root);

        return $generic;
    }
}
