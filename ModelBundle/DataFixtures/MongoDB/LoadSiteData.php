<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelBundle\Document\Site;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;

/**
 * Class LoadSiteData
 */
class LoadSiteData extends AbstractLoadSiteData implements OrchestraFunctionalFixturesInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $site3 = $this->getSite3();
        $manager->persist($site3);
        $this->addReference('site3', $site3);

        $manager->flush();
    }

    /**
     * @return Site
     */
    protected function getSite3()
    {
        $site3 = new Site();
        $site3->setSiteId('3');
        $site3->setName('Empty site');
        $site3->setTemplateSet('default');
        $site3->setTemplateRoot('default');
        $this->addSitesAliases(
            array('empty.openorchestra.inte', 'empty.openorchestra.dev'),
            array("fr", "en"),
            $site3);
        $site3->setDeleted(true);
        $site3->setTheme($this->getReference('themePresentation'));

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
}
