<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelBundle\Document\Site;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraProductionFixturesInterface;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;

/**
 * Class LoadDemoSiteData
 */
class LoadDemoSiteData extends AbstractLoadSiteData implements OrchestraProductionFixturesInterface, OrchestraFunctionalFixturesInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $site2 = $this->getSite2();
        $manager->persist($site2);
        $this->addReference('site2', $site2);

        $manager->flush();
    }

    /**
     * @return Site
     */
    protected function getSite2()
    {
        $site2 = new Site();
        $site2->setSiteId('2');
        $site2->setName('Demo site');
        $this->addSitesAliases(
            array('demo.openorchestra.1-2.inte', 'demo.openorchestra.1-2.dev'),
            array('fr', 'en', 'de'),
            $site2);
        $site2->setSitemapPriority(0.5);
        $site2->setDeleted(false);
        $site2->setTheme($this->getReference('themePresentation'));
        $site2->addBlock('slideshow');
        $site2->addBlock('gallery');
        $site2->addBlock('footer');
        $site2->addBlock('menu');
        $site2->addBlock('sub_menu');
        $site2->addBlock('language_list');
        $site2->addBlock('tiny_mce_wysiwyg');
        $site2->addBlock('configurable_content');
        $site2->addBlock('content_list');
        $site2->addBlock('content');
        $site2->addBlock('media_list_by_keyword');
        $site2->addBlock('video');
        $site2->addBlock('gmap');
        $site2->addBlock('add_this');
        $site2->addBlock('audience_analysis');

        return $site2;
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 310;
    }
}
