<?php

namespace OpenOrchestra\ModelBundle\Tests\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\ModelBundle\Form\DataTransformer\EmbedStatusToStatusTransformer;

/**
 * Class EmbedStatusToStatusTransformerTest
 */
class EmbedStatusToStatusTransformerTest extends AbstractBaseTestCase
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
        Phake::when($this->status)->getLabels()->thenReturn(array());

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
     * Test transform with no status
     */
    public function testTransformWithNoStatus()
    {
        $noStatus = new \StdClass();

        $this->assertSame('', $this->transformer->transform($noStatus));
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

    /**
     * Test reverse transform
     */
    public function testReverseTransformWithNullElement()
    {
        $embedStatus = $this->transformer->reverseTransform(null);

        $this->assertNull($embedStatus);
    }
}
