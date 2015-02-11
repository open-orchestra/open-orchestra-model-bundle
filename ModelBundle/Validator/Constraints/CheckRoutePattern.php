<?php

namespace PHPOrchestra\ModelBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class CheckRoutePattern
 */
class CheckRoutePattern extends Constraint
{
    public $message = 'php_orchestra_model.node.check_route_pattern';

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