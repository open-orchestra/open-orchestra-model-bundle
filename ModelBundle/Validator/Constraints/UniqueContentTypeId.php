<?php

namespace OpenOrchestra\ModelBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class UniqueContentTypeId
 */
class UniqueContentTypeId extends Constraint
{
    public $message = 'open_orchestra_model_validators.document.content_type.unique_content_type_id';

    /**
     * @return string|void
     */
    public function validatedBy()
    {
        return 'unique_content_type_id';
    }

    /**
     * @return array|string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
