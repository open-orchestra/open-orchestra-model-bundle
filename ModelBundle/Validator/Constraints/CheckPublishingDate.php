<?php

namespace OpenOrchestra\ModelBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class CheckPublishingDate
 */
class CheckPublishingDate extends Constraint
{
    public $message = 'open_orchestra_model_validators.document.autopublish.check_publishing_date';

    /**
     * @return string|void
     */
    public function validatedBy()
    {
        return 'check_publishing_date';
    }

    /**
     * @return array|string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
