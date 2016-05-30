<?php

namespace OpenOrchestra\ModelBundle\Tests\Document;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelBundle\Document\Node;

/**
 * Test NodeTest
 */
class NodeTest extends AbstractBaseTestCase
{
    /**
     * @var Node
     */
    protected $node;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->node = new Node();
    }
}
