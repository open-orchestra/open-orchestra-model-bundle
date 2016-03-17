<?php

namespace OpenOrchestra\ModelBundle\Tests\Validator\Constraints;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\ModelBundle\Validator\Constraints\CheckRoutePattern;
use OpenOrchestra\ModelBundle\Validator\Constraints\CheckRoutePatternValidator;

/**
 * Class CheckRoutePatternValidatorTest
 */
class CheckRoutePatternValidatorTest extends AbstractBaseTestCase
{
    /**
     * @var CheckRoutePatternValidator
     */
    protected $validator;

    protected $node;
    protected $areas;
    protected $context;
    protected $constraint;
    protected $constraintViolationBuilder;
    protected $nodeRepository;
    protected $fakeNameNode = 'fakeNodeName';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');

        $this->constraint = new CheckRoutePattern();
        $this->context = Phake::mock('Symfony\Component\Validator\Context\ExecutionContextInterface');
        $this->constraintViolationBuilder = Phake::mock('Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface');

        Phake::when($this->context)->buildViolation(Phake::anyParameters())->thenReturn($this->constraintViolationBuilder);
        Phake::when($this->constraintViolationBuilder)->atPath(Phake::anyParameters())->thenReturn($this->constraintViolationBuilder);

        $this->areas = Phake::mock('Doctrine\Common\Collections\ArrayCollection');

        $this->node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');

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
     * @param array  $nodes
     * @param int    $violationTimes
     * @param string $message
     *
     * @dataProvider provideCountAndViolation
     */
    public function testAddViolationOrNot($nodes, $violationTimes, $message = null)
    {
        Phake::when($this->nodeRepository)->findByParentAndRoutePattern(Phake::anyParameters())->thenReturn($nodes);

        $this->validator->validate($this->node, $this->constraint);

        Phake::verify($this->context, Phake::times($violationTimes))->buildViolation(
            $message,
            array("%nodeName%" => $this->fakeNameNode)
        );
        Phake::verify($this->constraintViolationBuilder, Phake::times($violationTimes))->atPath('routePattern');
    }

    /**
     * @return array
     */
    public function provideCountAndViolation()
    {
        $nodeSameRoute = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($nodeSameRoute)->isDeleted()->thenReturn(false);
        Phake::when($nodeSameRoute)->getName()->thenReturn($this->fakeNameNode);

        $nodeSameRouteDeleted = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($nodeSameRouteDeleted)->isDeleted()->thenReturn(true);
        Phake::when($nodeSameRouteDeleted)->getName()->thenReturn($this->fakeNameNode);
        $message = 'open_orchestra_model_validators.document.node.check_route_pattern';
        $messageWitNodeDeleted = 'open_orchestra_model_validators.document.node.check_route_pattern_node_deleted';

        return array(
            array(array($nodeSameRoute), 1, $message),
            array(array($nodeSameRouteDeleted), 1, $messageWitNodeDeleted),
            array(array(), 0),
            array(null, 0),
        );
    }
}
