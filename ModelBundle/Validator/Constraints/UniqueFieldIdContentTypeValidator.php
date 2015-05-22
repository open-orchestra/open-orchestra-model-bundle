<?php

namespace OpenOrchestra\ModelBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;

/**
 * Class UniqueFieldIdContentTypeValidator
 */
class UniqueFieldIdContentTypeValidator extends ConstraintValidator
{
    /**
     * @param ContentTypeInterface                $value
     * @param UniqueFieldIdContentType|Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $fields = $value->getFields();
        $fieldsId = array();

        foreach ($fields as $field) {
            $fieldId = $field->getFieldId();
            if (in_array($fieldId, $fieldsId)){
                $this->context->buildViolation($constraint->message)
                              ->atPath("fields")
                              ->addViolation();
            }
            $fieldsId[] = $fieldId;
        }
    }
}
