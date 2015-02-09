<?php

namespace PHPOrchestra\ModelBundle\Test\Validator\Constraints;

use Phake;
use PHPOrchestra\ModelBundle\Validator\Constraints\CheckRoutePattern;
use PHPOrchestra\ModelBundle\Validator\Constraints\CheckRoutePatternValidator;

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
    protected $translator;
    protected $nodeRepository;
    protected $message = 'message';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->nodeRepository = Phake::mock('PHPOrchestra\ModelInterface\Repository\NodeRepositoryInterface');
        $this->translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        Phake::when($this->translator)->trans(Phake::anyParameters())->thenReturn($this->message);

        $this->constraint = new CheckRoutePattern();
        $this->context = Phake::mock('Symfony\Component\Validator\Context\ExecutionContext');
        $this->areas = Phake::mock('Doctrine\Common\Collections\ArrayCollection');

        $this->node = Phake::mock('PHPOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($this->node)->getAreas()->thenReturn($this->areas);

        $this->validator = new CheckRoutePatternValidator($this->translator, $this->nodeRepository);
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
        Phake::when($this->nodeRepository)->findByParentIdAndRoutePatternAndNotNodeId(Phake::anyParameters())->thenReturn($nodes);

        $this->validator->validate($this->node, $this->constraint);

        Phake::verify($this->context, Phake::times($violationTimes))->addViolationAt('routePattern', $this->message);
        Phake::verify($this->translator, Phake::times($violationTimes))->trans($this->constraint->message);
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
