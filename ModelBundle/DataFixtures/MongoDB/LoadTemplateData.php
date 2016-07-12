<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelBundle\Document\Area;
use OpenOrchestra\ModelBundle\Document\Template;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraProductionFixturesInterface;
use OpenOrchestra\ModelInterface\Model\AreaInterface;

/**
 * Class LoadTemplateData
 */
class LoadTemplateData extends AbstractFixture implements OrderedFixtureInterface, OrchestraFunctionalFixturesInterface, OrchestraProductionFixturesInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $generic = $this->homepageTemplate();
        $manager->persist($generic);

        $this->addReference("homepage-template", $generic);
        $full = $this->fullTemplate();
        $manager->persist($full);

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 210;
    }

    /**
     * @return Template
     */
    protected function homepageTemplate()
    {
        $root = new Area();
        $root->setAreaType(AreaInterface::TYPE_ROOT);
        $root->setAreaId(AreaInterface::ROOT_AREA_ID);
        $root->setLabel(AreaInterface::ROOT_AREA_LABEL);

        $header = new Area();
        $header->setAreaId('row_header');
        $header->setAreaType(AreaInterface::TYPE_ROW);
        $columnHeader = $this->createColumnArea('header', 'column-header');
        $header->addArea($columnHeader);

        $main = new Area();
        $main->setAreaId('row_main');
        $main->setAreaType(AreaInterface::TYPE_ROW);
        $columnMain = $this->createColumnArea('main', 'column-main');
        $main->addArea($columnMain);

        $footer = new Area();
        $footer->setAreaId('row_footer');
        $footer->setAreaType(AreaInterface::TYPE_ROW);
        $columnFooter = $this->createColumnArea('footer', 'column-footer');
        $footer->addArea($columnFooter);

        $root->addArea($header);
        $root->addArea($main);
        $root->addArea($footer);

        $generic = new Template();
        $generic->setTemplateId('template_home');
        $generic->setSiteId('1');
        $generic->setVersion(1);
        $generic->setName('Homepage Template');
        $generic->setDeleted(false);
        $generic->setArea($root);

        return $generic;
    }

    /**
     * @return Template
     */
    protected function fullTemplate()
    {
        $root = new Area();
        $root->setAreaType(AreaInterface::TYPE_ROOT);
        $root->setAreaId(AreaInterface::ROOT_AREA_ID);
        $root->setLabel(AreaInterface::ROOT_AREA_LABEL);

        $header = new Area();
        $header->setAreaId('row_header');
        $header->setAreaType(AreaInterface::TYPE_ROW);
        $columnHeader = $this->createColumnArea('header', 'column-header');
        $header->addArea($columnHeader);

        $main = new Area();
        $main->setAreaId('row_main');
        $main->setAreaType(AreaInterface::TYPE_ROW);
        $columnMain = $this->createColumnArea('main', 'column-main');
        $columnLeft = $this->createColumnArea('left menu', 'left_menu');
        $main->addArea($columnMain);
        $main->addArea($columnLeft);

        $footer = new Area();
        $footer->setAreaId('row_footer');
        $footer->setAreaType(AreaInterface::TYPE_ROW);
        $columnFooter = $this->createColumnArea('footer', 'column-footer');
        $footer->addArea($columnFooter);

        $root->addArea($header);
        $root->addArea($main);
        $root->addArea($footer);

        $full = new Template();
        $full->setTemplateId('template_full');
        $full->setSiteId('1');
        $full->setVersion(1);
        $full->setName('Full Template');
        $full->setDeleted(false);
        $full->setArea($root);

        return $full;
    }

    /**
     * @param string      $label
     * @param string      $areaId
     * @param string      $width
     * @param string|null $htmlClass
     *
     * @return AreaInterface
     */
    protected function createColumnArea($label, $areaId, $htmlClass = null, $width = '1')
    {
        $area = new Area();
        $area->setLabel($label);
        $area->setAreaId($areaId);
        $area->setWidth($width);
        $area->setAreaType(AreaInterface::TYPE_COLUMN);

        if ($htmlClass !== null) {
            $area->setHtmlClass($htmlClass);
        }

        return $area;
    }
}
