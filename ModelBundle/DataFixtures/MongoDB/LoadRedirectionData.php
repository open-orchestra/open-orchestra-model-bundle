<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelBundle\Document\Redirection;
use OpenOrchestra\ModelInterface\Model\NodeInterface;


/**
 * Class LoadRedirectionData
 */
class LoadRedirectionData implements FixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $manager->persist($this->generateMainRedirection());
        $manager->persist($this->generateNewsRedirection());
        $manager->persist($this->generateGoogleRedirection());

        $manager->flush();
    }

    /**
     * @return Redirection
     */
    protected function generateNewsRedirection()
    {
        $redirection = new Redirection();
        $redirection->setSiteId('3');
        $redirection->setSiteName('Echonext site');
        $redirection->setRoutePattern('/news/{newsId}');
        $redirection->setLocale('fr');
        $redirection->setNodeId('news');
        $redirection->setPermanent(false);

        return $redirection;
    }

    /**
     * @return Redirection
     */
    protected function generateMainRedirection()
    {
        $redirection = new Redirection();
        $redirection->setSiteId('3');
        $redirection->setSiteName('Echonext site');
        $redirection->setRoutePattern('/');
        $redirection->setLocale('fr');
        $redirection->setNodeId(NodeInterface::ROOT_NODE_ID);
        $redirection->setPermanent(true);

        return $redirection;
    }

    /**
     * @return Redirection
     */
    protected function generateGoogleRedirection()
    {
        $redirection = new Redirection();
        $redirection->setSiteId('3');
        $redirection->setSiteName('Echonext site');
        $redirection->setRoutePattern('/google');
        $redirection->setLocale('fr');
        $redirection->setUrl('http://google.fr');
        $redirection->setPermanent(true);

        return $redirection;
    }

}
