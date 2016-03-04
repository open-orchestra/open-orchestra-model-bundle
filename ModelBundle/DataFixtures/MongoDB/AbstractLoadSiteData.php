<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use OpenOrchestra\ModelBundle\Document\SiteAlias;
use OpenOrchestra\ModelInterface\Model\SchemeableInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;

/**
 * Class AbstractLoadSiteData
 */
abstract class AbstractLoadSiteData extends AbstractFixture implements OrderedFixtureInterface
{
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
