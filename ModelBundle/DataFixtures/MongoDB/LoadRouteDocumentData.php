<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelBundle\Document\RouteDocument;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;

/**
 * Class LoadRouteDocumentData
 */
class LoadRouteDocumentData implements FixtureInterface, OrchestraFunctionalFixturesInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $patterns = array(
            'foo',
            'foo/{bar}',
            'foo/bar',
            'baz/bar',
            'foo/{bar}/baz'
        );

        foreach ($patterns as $pattern) {
            $route = new RouteDocument();
            $route->setName($pattern);
            $route->setPattern($pattern);

            $manager->persist($route);
        }

        $manager->flush();
    }
}
