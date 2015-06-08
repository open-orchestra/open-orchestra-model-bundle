<?php

namespace OpenOrchestra\ModelBundle\Helper;

use OpenOrchestra\ModelInterface\Helper\SuppressSpecialCharacterHelperInterface;

/**
 * Class SuppressSpecialCharacterHelper
 */
class SuppressSpecialCharacterHelper implements SuppressSpecialCharacterHelperInterface
{
    /**
     * @param string $input
     * @param array  $authorizeSpecial
     *
     * @return string
     */
    public function transform($input, $authorizeSpecial = array())
    {
        $element = trim($input);
        $element = str_replace(' ', '_', $element);
        $element = htmlentities($element, ENT_NOQUOTES, 'UTF-8');
        $accents = '/&([A-Za-z]{1,2})(grave|acute|circ|cedil|uml|lig|tilde);/';
        $element = preg_replace($accents, '$1', $element);
        $authorizeSpecial = join('',$authorizeSpecial);
        $element = preg_replace('/[^-a-z_A-Z0-9'.$authorizeSpecial.']+/', '', $element);

        return strtolower($element);
    }
}
