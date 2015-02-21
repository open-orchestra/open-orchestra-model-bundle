<?php

namespace OpenOrchestra\ModelBundle\Repository;

/**
 * Interface FieldAutoGenerableRepositoryInterface
 */
interface FieldAutoGenerableRepositoryInterface
{
    /**
     * @param string $name
     */
    public function testUnicityInContext($name);

}
