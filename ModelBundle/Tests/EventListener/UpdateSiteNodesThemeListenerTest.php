<?php

namespace OpenOrchestra\ModelBundle\Tests\EventListener;

use Phake;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\ModelBundle\EventListener\UpdateSiteNodesThemeListener;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;

/**
 * Class UpdateSiteNodesThemeListenerTest
 */
class UpdateSiteNodesThemeListenerTest extends AbstractBaseTestCase
{
    /**
     * @var UpdateSiteNodesThemeListener
     */
    protected $listener;

    protected $preUpdateEventArgs;
    protected $nodeRepository;
    protected $container;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelBundle\Repository\NodeRepository');
        $this->container = Phake::mock('Symfony\Component\DependencyInjection\Container');
        $this->preUpdateEventArgs = Phake::mock('Doctrine\ODM\MongoDB\Event\PreUpdateEventArgs');
        Phake::when($this->container)->get(Phake::anyParameters())->thenReturn($this->nodeRepository);

        $this->listener = new UpdateSiteNodesThemeListener();
        $this->listener->setContainer($this->container);
    }

    /**
     * Test if method is present
     */
    public function testCallable()
    {
        $this->assertTrue(is_callable(array($this->listener, 'preUpdate')));
    }

    /**
     * @param SiteInterface $site
     * @param array         $documents
     *
     * @dataProvider provideSiteAndNodes
     */
    public function testPreUpdate(SiteInterface $site, array $documents)
    {
        Phake::when($this->preUpdateEventArgs)->getDocument()->thenReturn($site);
        Phake::when($this->preUpdateEventArgs)->hasChangedField(Phake::anyParameters())->thenReturn(true);
        Phake::when($this->nodeRepository)->findBySiteIdAndDefaultTheme(Phake::anyParameters())->thenReturn($documents);

        $this->listener->preUpdate($this->preUpdateEventArgs);

        foreach ($documents as $document) {
            Phake::verify($document)->setTheme("fakeTheme");
        }
    }

    /**
     * @return array
     */
    public function provideSiteAndNodes()
    {
        $site = Phake::mock('OpenOrchestra\ModelBundle\Document\Site');
        $theme = Phake::mock('OpenOrchestra\ModelBundle\Document\Theme');
        Phake::when($site)->getTheme()->thenReturn($theme);
        Phake::when($theme)->getName()->thenReturn("fakeTheme");

        $document0 = Phake::mock('OpenOrchestra\ModelBundle\Document\Node');
        $document1 = Phake::mock('OpenOrchestra\ModelBundle\Document\Node');
        $document2 = Phake::mock('OpenOrchestra\ModelBundle\Document\Node');

        return array(
            array($site, array($document0)),
            array($site, array($document1, $document2)),
            array($site, array()),
        );
    }
}
