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
        $site2->setMetaAuthor('Open Orchestra');
        $this->addSitesAliases(
            array('front.pddv-openorchestra-master.eolas-services.com', 'demo.openorchestra.2-0.dev'),
            array('fr', 'en', 'de'),
            $site2,
            array('en' => 'en', 'de' => 'de')
        );
        $site2->setSitemapPriority(0.5);
        $site2->setDeleted(false);
        $site2->setContentTypes(array('news', 'customer', 'car'));
        $site2->setTemplateSet('default');
        $site2->setTemplateNodeRoot('home');
        $site2->addBlock('tiny_mce_wysiwyg');
        $site2->addBlock('configurable_content');
        $site2->addBlock('content_list');
        $site2->addBlock('content');
        $site2->addBlock('video');

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
