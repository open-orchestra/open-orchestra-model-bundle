<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelBundle\Document\Redirection;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;


/**
 * Class LoadRedirectionData
 */
class LoadRedirectionData implements FixtureInterface, OrchestraFunctionalFixturesInterface
{
    /**
     * @param ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $manager->persist($this->generateGoogleRedirection());
        $manager->persist($this->generateNodeRedirection());

        $manager->flush();
    }

    /**
     * @return Redirection
     */
    protected function generateGoogleRedirection()
    {
        $redirection = new Redirection();
        $redirection->setSiteId('2');
        $redirection->setSiteName('Demo site');
        $redirection->setRoutePattern('/google');
        $redirection->setLocale('fr');
        $redirection->setUrl('http://google.fr');
        $redirection->setPermanent(true);

        return $redirection;
    }

    /**
     * @return Redirection
     */
    protected function generateNodeRedirection()
    {
        $redirection = new Redirection();
        $redirection->setSiteId('2');
        $redirection->setSiteName('Demo site');
        $redirection->setRoutePattern('/test-root-redirection');
        $redirection->setLocale('fr');
        $redirection->setPermanent(false);
        $redirection->setNodeId('root');

        return $redirection;
    }
}
