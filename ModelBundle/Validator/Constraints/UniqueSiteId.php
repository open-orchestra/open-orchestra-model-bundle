<?php

namespace OpenOrchestra\ModelBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class UniqueSiteId
 * @Annotation
 */
class UniqueSiteId extends Constraint
{
    public $message = 'open_orchestra_model_validators.document.website.unique_site_id';

    /**
     * @return string|void
     */
    public function validatedBy()
    {
        return 'unique_site_id';
    }

    /**
     * @return array|string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
