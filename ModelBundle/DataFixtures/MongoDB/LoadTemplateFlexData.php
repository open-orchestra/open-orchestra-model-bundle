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
        $header->setAreaId('header');

        $columnLogo = new AreaFlex();
        $columnLogo->setAreaType(AreaFlexInterface::TYPE_COLUMN);
        $columnLogo->setAreaId('logo');
        $columnLogo->setWidth('1');

        $columnMenu = new AreaFlex();
        $columnMenu->setAreaType(AreaFlexInterface::TYPE_COLUMN);
        $columnMenu->setAreaId('logo');
        $columnMenu->setWidth('3');

        $header->addArea($columnLogo);
        $header->addArea($columnMenu);

        $main = new AreaFlex();
        $main->setAreaType(AreaFlexInterface::TYPE_ROW);
        $main->setAreaId('main');

        $columnMain = new AreaFlex();
        $columnMain->setAreaType(AreaFlexInterface::TYPE_COLUMN);
        $columnMain->setAreaId('content');
        $columnMain->setWidth('1');

        $main->addArea($columnMain);

        $footer = new AreaFlex();
        $footer->setAreaType(AreaFlexInterface::TYPE_ROW);
        $footer->setAreaId('footer');

        $footer1 = new AreaFlex();
        $footer1->setAreaType(AreaFlexInterface::TYPE_COLUMN);
        $footer1->setAreaId('footer_1');
        $footer1->setWidth('1');

        $footer2 = new AreaFlex();
        $footer2->setAreaType(AreaFlexInterface::TYPE_COLUMN);
        $footer2->setAreaId('footer_2');
        $footer2->setWidth('1');

        $footer3 = new AreaFlex();
        $footer3->setAreaType(AreaFlexInterface::TYPE_COLUMN);
        $footer3->setAreaId('footer_3');
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
