<?php

namespace OpenOrchestra\ModelBundle\Helper;

use Doctrine\Common\Util\Inflector;

/**
 * Class GenerateIdHelper
 */
class GenerateIdHelper
{
    /**
     * @param string $input
     *
     * @return string
     */
    public function generate($input)
    {
        $element = trim($input);
        $element = Inflector::tableize($element);
        $element = str_replace(' ', '_', $element);
        $element = htmlentities($element, ENT_NOQUOTES, 'UTF-8');
        $accents = '/&([A-Za-z]{1,2})(grave|acute|circ|cedil|uml|lig|tilde);/';
        $element = preg_replace($accents, '$1', $element);
        $element = preg_replace('/[^-a-z_A-Z0-9-]+/', '', $element);

        return strtolower($element);
    }
}
