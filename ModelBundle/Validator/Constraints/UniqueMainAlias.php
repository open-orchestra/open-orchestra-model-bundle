<?php

namespace PHPOrchestra\ModelBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class UniqueMainAlias
 */
class UniqueMainAlias extends Constraint
{
    public $message = 'php_orchestra_model.website.unique_main_alias';

    /**
     * @return string|void
     */
    public function validatedBy()
    {
        return 'unique_main_alias';
    }

    /**
     * @return array|string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
