<?php

namespace OpenOrchestra\ModelBundle\Tests\Validator\Constraints;

use Phake;
use OpenOrchestra\ModelBundle\Validator\Constraints\UniqueSiteId;
use OpenOrchestra\ModelBundle\Validator\Constraints\UniqueSiteIdValidator;
use OpenOrchestra\ModelInterface\Model\SiteInterface;

/**
 * Class UniqueSiteIdValidatorTest
 */
class UniqueSiteIdValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UniqueSiteIdValidator
     */
    protected $validator;

    protected $site;
    protected $site2;
    protected $context;
    protected $id = 'id';
    protected $constraint;
    protected $constraintViolationBuilder;
    protected $repository;
    protected $siteId = 'siteid';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new UniqueSiteId();
        $this->context = Phake::mock('Symfony\Component\Validator\Context\ExecutionContextInterface');
        $this->constraintViolationBuilder = Phake::mock('Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface');

        Phake::when($this->context)->buildViolation(Phake::anyParameters())->thenReturn($this->constraintViolationBuilder);
        Phake::when($this->constraintViolationBuilder)->atPath(Phake::anyParameters())->thenReturn($this->constraintViolationBuilder);

        $this->site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        $this->site2 = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($this->site)->getSiteId()->thenReturn($this->siteId);
        Phake::when($this->site)->getId()->thenReturn($this->id);

        $this->repository = Phake::mock('OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface');

        $this->validator = new UniqueSiteIdValidator($this->repository);
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
     * Test validate
     */
    public function testValidateWithViolation()
    {
        Phake::when($this->site2)->getId()->thenReturn('id2');
        Phake::when($this->repository)->findOneBySiteId($this->siteId)->thenReturn($this->site2);

        $this->validator->validate($this->site, $this->constraint);

        Phake::verify($this->context)->buildViolation($this->constraint->message);
        Phake::verify($this->constraintViolationBuilder)->atPath('siteId');
    }

    /**
     * Test Validate
     *
     * @param string        $id
     * @param SiteInterface $site2
     *
     * @dataProvider generateNoViolation
     */
    public function testValidationNoViolation($id, $site2)
    {
        Phake::when($this->site2)->getId()->thenReturn($id);
        Phake::when($this->repository)->findOneBySiteId($this->siteId)->thenReturn($site2);

        $this->validator->validate($this->site, $this->constraint);

        Phake::verify($this->context, Phake::never())->buildViolation($this->constraint->message);
        Phake::verify($this->constraintViolationBuilder, Phake::never())->atPath('siteId');
    }

    /**
     * @return array
     */
    public function generateNoViolation()
    {
        return array(
            array(null, null),
            array($this->id, $this->site2)
        );
    }
}
