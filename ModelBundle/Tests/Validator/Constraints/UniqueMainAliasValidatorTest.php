<?php

namespace OpenOrchestra\ModelBundle\Tests\Validator\Constraints;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\ModelBundle\Validator\Constraints\UniqueMainAlias;
use OpenOrchestra\ModelBundle\Validator\Constraints\UniqueMainAliasValidator;

/**
 * Test UniqueMainAliasValidatorTest
 */
class UniqueMainAliasValidatorTest extends AbstractBaseTestCase
{
    /**
     * @var UniqueMainAliasValidator
     */
    protected $validator;

    protected $site;
    protected $context;
    protected $constraint;
    protected $siteAliases;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new UniqueMainAlias();
        $this->context = Phake::mock('Symfony\Component\Validator\Context\ExecutionContext');
        $this->areas = Phake::mock('Doctrine\Common\Collections\ArrayCollection');

        $this->siteAliases = Phake::mock('Doctrine\Common\Collections\Collection');
        Phake::when($this->siteAliases)->filter(Phake::anyParameters())->thenReturn($this->siteAliases);

        $this->site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($this->site)->getAliases()->thenReturn($this->siteAliases);

        $this->validator = new UniqueMainAliasValidator();
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
     * @param int $count
     * @param int $violationTimes
     *
     * @dataProvider provideCountAndViolation
     */
    public function testValidate($count, $violationTimes)
    {
        Phake::when($this->siteAliases)->count()->thenReturn($count);

        $this->validator->validate($this->site, $this->constraint);

        Phake::verify($this->context, Phake::times($violationTimes))->addViolation($this->constraint->message);
    }

    /**
     * @return array
     */
    public function provideCountAndViolation()
    {
        return array(
            array(0, 0),
            array(1, 0),
            array(2, 1),
            array(3, 1),
        );
    }
}
