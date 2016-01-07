<?php

namespace OpenOrchestra\ModelBundle\Tests\Form\DataTransformer;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelBundle\Form\DataTransformer\EmbedSiteToSiteTransformer;
use Phake;

/**
 * Test EmbedSiteToSiteTransformerTest
 */
class EmbedSiteToSiteTransformerTest extends AbstractBaseTestCase
{
    /**
     * @var EmbedSiteToSiteTransformer
     */
    protected $transformer;

    protected $site;
    protected $siteRepository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        $this->siteRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface');

        $this->transformer = new EmbedSiteToSiteTransformer($this->siteRepository);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Form\DataTransformerInterface', $this->transformer);
    }

    /**
     * @param array $input
     * @param array $output
     *
     * @dataProvider provideTransformData
     */
    public function testTransform($input, $output)
    {
        $this->assertSame($output, $this->transformer->transform($input));
    }

    /**
     * @return array
     */
    public function provideTransformData()
    {
        return array(
            array(array(), array()),
            array(array(array('siteId' => 'foo')), array($this->site)),
            array(array(array('siteId' => 'foo'), array('siteId' => 'bar')), array($this->site, $this->site)),
        );
    }

    /**
     * Test reverse transform
     */
    public function testReverseTransform()
    {
        $siteId = 'foo';
        Phake::when($this->site)->getSiteId()->thenReturn($siteId);
        $input = array($this->site);

        $this->assertSame(array(array('siteId' => $siteId)), $this->transformer->reverseTransform($input));
    }
}
