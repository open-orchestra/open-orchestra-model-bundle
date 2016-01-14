<?php

namespace OpenOrchestra\ModelBundle\Tests\EventListener;

use Phake;
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

    protected $lifecycleEventArgs;
    protected $postFlushEventArgs;
    protected $nodeRepository;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelBundle\Repository\NodeRepository');
        $this->lifecycleEventArgs = Phake::mock('Doctrine\ODM\MongoDB\Event\LifecycleEventArgs');
        $this->postFlushEventArgs = Phake::mock('Doctrine\ODM\MongoDB\Event\PostFlushEventArgs');

        $this->listener = new UpdateSiteNodesThemeListener('Node');
    }

    /**
     * Test if method is present
     */
    public function testCallable()
    {
        $this->assertTrue(is_callable(array($this->listener, 'preUpdate')));
        $this->assertTrue(is_callable(array($this->listener, 'postFlush')));
    }
}
