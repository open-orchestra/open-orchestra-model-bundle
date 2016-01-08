<?php

namespace OpenOrchestra\ModelBundle\Tests\Validator\Constraints;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\ModelBundle\Validator\Constraints\UniqueContentTypeId;
use OpenOrchestra\ModelBundle\Validator\Constraints\UniqueContentTypeIdValidator;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;

/**
 * Class UniqueContentTypeIdValidatorTest
 */
class UniqueContentTypeIdValidatorTest extends AbstractBaseTestCase
{
    /**
     * @var UniqueContentTypeId
     */
    protected $validator;

    protected $contentType;
    protected $contentType2;
    protected $context;
    protected $constraint;
    protected $repository;
    protected $contentTypeId = 'contentTypeId';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = Phake::mock('Symfony\Component\Validator\Constraint');
        $this->context = Phake::mock('Symfony\Component\Validator\Context\ExecutionContextInterface');
        $constraintViolationBuilder = Phake::mock('Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface');

        Phake::when($this->context)->buildViolation(Phake::anyParameters())->thenReturn($constraintViolationBuilder);
        Phake::when($constraintViolationBuilder)->atPath(Phake::anyParameters())->thenReturn($constraintViolationBuilder);

        $this->contentType = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');
        $this->contentType2 = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');

        Phake::when($this->contentType)->getContentTypeId()->thenReturn($this->contentTypeId);

        $this->repository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface');

        $this->validator = new UniqueContentTypeIdValidator($this->repository);
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
        Phake::when($this->contentType2)->getContentTypeId()->thenReturn($this->contentTypeId);
        Phake::when($this->contentType2)->getVersion()->thenReturn('1');

        Phake::when($this->repository)->findOneByContentTypeIdInLastVersion($this->contentTypeId)->thenReturn($this->contentType);

        $this->validator->validate($this->contentType2, $this->constraint);

        Phake::verify($this->context)->buildViolation(Phake::anyParameters());
    }

    /**
     * @param string               $contentTypeId
     * @param string               $version
     * @param ContentTypeInterface $contentType
     *
     * @dataProvider provideContentTypeIdAndContentTypeWithNoViolation
     */
    public function testValidationNoViolation($contentTypeId, $version, $contentType)
    {
        Phake::when($this->contentType2)->getId()->thenReturn($contentTypeId);
        Phake::when($this->contentType2)->getVersion()->thenReturn($version);
        Phake::when($this->repository)->findOneByContentTypeIdInLastVersion(Phake::anyParameters())->thenReturn($contentType);

        $this->validator->validate($this->contentType2, $this->constraint);

        Phake::verify($this->context, Phake::never())->buildViolation(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideContentTypeIdAndContentTypeWithNoViolation()
    {
        return array(
            array('newFakeId', 1, null),
            array('contentTypeId', 2, Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface')),
        );
    }
}
