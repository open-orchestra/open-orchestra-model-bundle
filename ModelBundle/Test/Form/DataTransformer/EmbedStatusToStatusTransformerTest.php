<?php

namespace OpenOrchestra\ModelBundle\Test\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Phake;
use OpenOrchestra\ModelBundle\Form\DataTransformer\EmbedStatusToStatusTransformer;

/**
 * Class EmbedStatusToStatusTransformerTest
 */
class EmbedStatusToStatusTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EmbedStatusToStatusTransformer
     */
    protected $transformer;

    protected $status;
    protected $statusId;
    protected $embedStatus;
    protected $statusRepository;
    protected $embedStatusClass;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->embedStatusClass = 'OpenOrchestra\ModelBundle\Document\EmbedStatus';
        $this->statusId = 'statusId';

        $this->status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($this->status)->getId()->thenReturn($this->statusId);
        Phake::when($this->status)->getToRoles()->thenReturn(new ArrayCollection());
        Phake::when($this->status)->getFromRoles()->thenReturn(new ArrayCollection());

        $this->embedStatus = Phake::mock('OpenOrchestra\ModelBundle\Document\EmbedStatus');
        Phake::when($this->embedStatus)->getId()->thenReturn($this->statusId);

        $this->statusRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface');
        Phake::when($this->statusRepository)->find(Phake::anyParameters())->thenReturn($this->status);

        $this->transformer = new EmbedStatusToStatusTransformer($this->statusRepository, $this->embedStatusClass);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Form\DataTransformerInterface', $this->transformer);
    }

    /**
     * Test transform
     */
    public function testTransform()
    {
        $status = $this->transformer->transform($this->embedStatus);

        $this->assertSame($this->status, $status);
    }

    /**
     * Test reverse transform
     */
    public function testReverseTransform()
    {
        $embedStatus = $this->transformer->reverseTransform($this->status);

        $this->assertInstanceOf('OpenOrchestra\ModelInterface\Model\EmbedStatusInterface', $embedStatus);
        $this->assertSame($this->statusId, $embedStatus->getId());
    }
}
