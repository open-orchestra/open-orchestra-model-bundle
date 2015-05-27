<?php

namespace OpenOrchestra\ModelBundle\Tests\Validator\Constraints;

use Phake;
use OpenOrchestra\ModelBundle\Validator\Constraints\CheckRoutePattern;
use OpenOrchestra\ModelBundle\Validator\Constraints\CheckRoutePatternValidator;

/**
 * Class CheckRoutePatternValidatorTest
 */
class CheckRoutePatternValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CheckRoutePatternValidator
     */
    protected $validator;

    protected $node;
    protected $areas;
    protected $context;
    protected $constraint;
    protected $nodeRepository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');

        $this->constraint = new CheckRoutePattern();
        $this->context = Phake::mock('Symfony\Component\Validator\Context\ExecutionContext');
        $this->areas = Phake::mock('Doctrine\Common\Collections\ArrayCollection');

        $this->node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($this->node)->getAreas()->thenReturn($this->areas);

        $this->validator = new CheckRoutePatternValidator($this->nodeRepository);
        $this->validator->initialize($this->context);
    }

    /**
     * Test instance
     */
    public function testClass()
    {
        $this->assertInstanceOf('Symfony\Component\Validator\ConstraintValidator', $this->validator);
    }

    /**
     * @param array $nodes
     * @param int   $violationTimes
     *
     * @dataProvider provideCountAndViolation
     */
    public function testAddViolationOrNot($nodes, $violationTimes)
    {
        Phake::when($this->nodeRepository)->findByParentIdAndRoutePatternAndNotNodeIdAndSiteId(Phake::anyParameters())->thenReturn($nodes);

        $this->validator->validate($this->node, $this->constraint);

        Phake::verify($this->context, Phake::times($violationTimes))->addViolationAt('routePattern', $this->constraint->message);
    }

    /**
     * @return array
     */
    public function provideCountAndViolation()
    {
        return array(
            array(array('node'), 1),
            array(array(), 0),
            array(null, 0),
        );
    }
}
