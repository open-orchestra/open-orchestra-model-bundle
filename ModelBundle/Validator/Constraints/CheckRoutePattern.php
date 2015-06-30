<?php

namespace OpenOrchestra\ModelBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class CheckRoutePattern
 */
class CheckRoutePattern extends Constraint
{
    public $message = 'open_orchestra_model_validators.document.node.check_route_pattern';

    /**
     * @return string|void
     */
    public function validatedBy()
    {
        return 'check_route_pattern';
    }

    /**
     * @return array|string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
