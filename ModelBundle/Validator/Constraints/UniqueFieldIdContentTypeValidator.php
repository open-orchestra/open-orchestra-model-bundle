<?php

namespace OpenOrchestra\ModelBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class UniqueFieldIdContentTypeValidator
 */
class UniqueFieldIdContentTypeValidator extends ConstraintValidator
{

    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $fields = $value->getFields();
        $fieldsId = array();

        foreach ($fields as $field) {
            $fieldId = $field->getFieldId();
            if (in_array($fieldId, $fieldsId)){
                $this->context->buildViolation($constraint->message)
                              ->addViolation();
            }
            $fieldsId[] = $fieldId;
        }
    }
}