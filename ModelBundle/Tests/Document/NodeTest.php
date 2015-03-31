<?php

namespace OpenOrchestra\ModelBundle\Tests\Document;

use OpenOrchestra\ModelBundle\Document\Node;
use OpenOrchestra\ModelBundle\Document\Status;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Phake;

/**
 * Test NodeTest
 */
class NodeTest extends \PHPUnit_Framework_TestCase
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

    /**
     * @param string      $nodeId
     * @param Status|null $status
     * @param bool        $result
     *
     * @dataProvider provideNodeIdAndStatus
     */
    public function testIsEditable($nodeId, $status = null, $result)
    {
        $this->node->setNodeId($nodeId);
        $this->node->setStatus($status);

        $this->assertSame($result, $this->node->isEditable());
    }

    /**
     * @return array
     */
    public function provideNodeIdAndStatus()
    {
        $statusPublished = new Status();
        $statusPublished->setPublished(true);

        $statusNotPublished = new Status();
        $statusNotPublished->setPublished(false);

        return array(
            array(NodeInterface::ROOT_NODE_ID, $statusPublished, false),
            array(NodeInterface::ROOT_NODE_ID, $statusNotPublished, true),
            array(NodeInterface::ROOT_NODE_ID, null, true),
            array('test', $statusPublished, false),
            array('test', $statusNotPublished, true),
            array('test', null, true),
            array(NodeInterface::TRANSVERSE_NODE_ID, $statusPublished, true),
            array(NodeInterface::TRANSVERSE_NODE_ID, $statusNotPublished, true),
            array(NodeInterface::TRANSVERSE_NODE_ID, null, true),
        );
    }
}
