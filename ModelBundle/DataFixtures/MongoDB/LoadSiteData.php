<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelBundle\Document\Site;
use OpenOrchestra\ModelBundle\Document\SiteAlias;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraProductionFixturesInterface;
use OpenOrchestra\ModelInterface\Model\SchemeableInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;

/**
 * Class LoadSiteData
 */
class LoadSiteData extends AbstractFixture implements OrderedFixtureInterface, OrchestraProductionFixturesInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $site2 = $this->getSite2();
        $manager->persist($site2);
        $this->addReference('site2', $site2);

        $site3 = $this->getSite3();
        $manager->persist($site3);
        $this->addReference('site3', $site3);

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
            array('demo.open-orchestra.com', 'demo.openorchestra.inte', 'demo.openorchestra.dev'),
            array('fr', 'en'),
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
     * @return Site
     */
    protected function getSite3()
    {
        $site3 = new Site();
        $site3->setSiteId('3');
        $site3->setName('Empty site');
        $this->addSitesAliases(
            array('empty.openorchestra.inte', 'empty.openorchestra.dev'),
            array("fr", "en"),
            $site3);
        $site3->setDeleted(true);
        $site3->setTheme($this->getReference('themePresentation'));
        $site3->addBlock('sample');

        return $site3;
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 300;
    }

    /**
     * @param string $domainName
     * @param string $language
     * @param bool   $main
     * @param string $prefix
     *
     * @return SiteAlias
     */
    protected function generateSiteAlias($domainName, $language, $main = false, $prefix = null)
    {
        $siteAlias = new SiteAlias();
        $siteAlias->setDomain($domainName);
        $siteAlias->setLanguage($language);
        $siteAlias->setMain($main);
        $siteAlias->setPrefix($prefix);
        $siteAlias->setScheme(SchemeableInterface::SCHEME_HTTP);

        return $siteAlias;
    }

    /**
     * @param array         $sitesNames
     * @param array         $sitesLanguages
     * @param SiteInterface $site
     * @param array         $prefixes
     */
    protected function addSitesAliases(array $sitesNames, array $sitesLanguages, $site, array $prefixes = array())
    {
        $master = true;
        foreach ($sitesNames as $siteName ) {
            foreach ($sitesLanguages as $siteLanguage) {
                $prefix = (isset ($prefixes[$siteLanguage]))?$prefixes[$siteLanguage]:null;
                $site->addAlias($this->generateSiteAlias($siteName, $siteLanguage, $master, $prefix));
                $master = false;
            }
        }
    }
}
