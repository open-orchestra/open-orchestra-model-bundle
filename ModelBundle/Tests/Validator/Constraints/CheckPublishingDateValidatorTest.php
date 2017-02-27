<?php

namespace OpenOrchestra\ModelBundle\Tests\Validator\Constraints;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\Model\AutoPublishableInterface;
use Phake;
use OpenOrchestra\ModelBundle\Validator\Constraints\CheckPublishingDate;
use OpenOrchestra\ModelBundle\Validator\Constraints\CheckPublishingDateValidator;

/**
 * Class CheckPublishingDateValidatorTest
 */
class CheckPublishingDateValidatorTest extends AbstractBaseTestCase
{
    /**
     * @var CheckPublishingDateValidator
     */
    protected $validator;

    protected $context;
    protected $constraint;
    protected $constraintViolationBuilder;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new CheckPublishingDate();
        $this->context = Phake::mock('Symfony\Component\Validator\Context\ExecutionContextInterface');
        $this->constraintViolationBuilder = Phake::mock('Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface');

        Phake::when($this->context)->buildViolation(Phake::anyParameters())->thenReturn($this->constraintViolationBuilder);
        Phake::when($this->constraintViolationBuilder)->atPath(Phake::anyParameters())->thenReturn($this->constraintViolationBuilder);

        $this->validator = new CheckPublishingDateValidator();
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
     * @param AutoPublishableInterface $document
     * @param int                      $violationTimes
     * @param string                   $message
     *
     * @dataProvider provideCountAndViolation
     */
    public function testAddViolationOrNot(AutoPublishableInterface $document, $violationTimes)
    {
        $this->validator->validate($document, $this->constraint);

        Phake::verify($this->context, Phake::times($violationTimes))->buildViolation('open_orchestra_model_validators.document.autopublish.check_publishing_date');
        Phake::verify($this->constraintViolationBuilder, Phake::times($violationTimes))->atPath('unpublishDate');
    }

    /**
     * @return array
     */
    public function provideCountAndViolation()
    {
        $withViolation = new PhakeTest();
        $withViolation->setPublishDate(new \DateTime('2017-02-27T15:03:01.012345Z'));
        $withViolation->setUnpublishDate(new \DateTime('2017-02-26T15:03:01.012345Z'));

        $withoutViolation = new PhakeTest();
        $withoutViolation->setPublishDate(new \DateTime('2017-02-26T15:03:01.012345Z'));
        $withoutViolation->setUnpublishDate(new \DateTime('2017-02-27T15:03:01.012345Z'));

        return array(
            array($withViolation, 1),
            array($withoutViolation, 0),
        );
    }
}


class PhakeTest implements AutoPublishableInterface
{
    public $publishDate;
    public $unPublishDate;

    public function getPublishDate()
    {
        return $this->publishDate;
    }
    /**
     * @return \DateTime $date
     */
    public function getUnpublishDate()
    {
        return $this->unPublishDate;
    }

    /**
     * @param \DateTime $date
     */
    public function setPublishDate($date)
    {
        $this->publishDate = $date;
    }
    /**
     * @param \DateTime $date
     */
    public function setUnpublishDate($date)
    {
        $this->unPublishDate = $date;
    }
}