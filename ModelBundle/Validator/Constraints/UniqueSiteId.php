<?php

namespace OpenOrchestra\ModelBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class UniqueSiteId
 */
class UniqueSiteId extends Constraint
{
    public $message = 'open_orchestra_model.website.unique_site_id';

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
