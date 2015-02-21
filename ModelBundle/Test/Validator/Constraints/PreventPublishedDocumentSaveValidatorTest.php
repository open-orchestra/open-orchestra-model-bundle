<?php

namespace OpenOrchestra\ModelBundle\Test\Validator\Constraints;

use Phake;
use OpenOrchestra\ModelBundle\Validator\Constraints\PreventPublishedDocumentSave;
use OpenOrchestra\ModelBundle\Validator\Constraints\PreventPublishedDocumentSaveValidator;

/**
 * Class PreventPublishedDocumentSaveValidatorTest
 */
class PreventPublishedDocumentSaveValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PreventPublishedDocumentSaveValidator
     */
    protected $validator;

    protected $message = 'message';
    protected $documentManager;
    protected $unitOfWork;
    protected $translator;
    protected $constraint;
    protected $context;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        Phake::when($this->translator)->trans(Phake::anyParameters())->thenReturn($this->message);
        $this->context = Phake::mock('Symfony\Component\Validator\Context\ExecutionContext');
        $this->constraint = new PreventPublishedDocumentSave();

        $this->unitOfWork = Phake::mock('Doctrine\ODM\MongoDB\UnitOfWork');
        $this->documentManager = Phake::mock('Doctrine\ODM\MongoDB\DocumentManager');
        Phake::when($this->documentManager)->getUnitOfWork()->thenReturn($this->unitOfWork);

        $this->validator = new PreventPublishedDocumentSaveValidator($this->translator, $this->documentManager);
        $this->validator->initialize($this->context);
    }

    /**
     * Test validate
     *
     * @param Document $document
     * @param int      $numberOfViolation
     *
     * @dataProvider provideDocument
     */
    public function testValidate($document, $numberOfViolation)
    {
        $this->validator->validate($document, $this->constraint);
        Phake::verify($this->translator, Phake::times($numberOfViolation))->trans($this->constraint->message);
        Phake::verify($this->context, Phake::times($numberOfViolation))->addViolation($this->message);
    }

    /**
     * @return array
     */
    public function provideDocument()
    {

        $status0 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        $statusableInterface0 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusableInterface');
        Phake::when($status0)->isPublished()->thenReturn(true);
        Phake::when($statusableInterface0)->getStatus()->thenReturn($status0);

        $status1 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        $statusableInterface1 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusableInterface');
        Phake::when($status1)->isPublished()->thenReturn(false);
        Phake::when($statusableInterface1)->getStatus()->thenReturn($status1);

        $statusableInterface2 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusableInterface');
        Phake::when($statusableInterface2)->getStatus()->thenReturn(null);

        $notStatusableInterface = Phake::mock('\stdClass');

        return array(
            array($statusableInterface0, 1),
            array($statusableInterface1, 0),
            array($statusableInterface2, 0),
            array($notStatusableInterface, 0),
        );
    }

    /**
     * Test if old node not yet published
     */
    public function testWithDocumentNotYetPublished()
    {
        $oldStatus = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($oldStatus)->isPublished()->thenReturn(false);

        $status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        $statusable = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusableInterface');
        Phake::when($status)->isPublished()->thenReturn(true);
        Phake::when($statusable)->getStatus()->thenReturn($status);

        Phake::when($this->unitOfWork)->getOriginalDocumentData(Phake::anyParameters())->thenReturn(array(
            'status' => $oldStatus
        ));

        $this->validator->validate($statusable, $this->constraint);

        Phake::verify($this->context, Phake::never())->addViolation($this->message);
    }
}
