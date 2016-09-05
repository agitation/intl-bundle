<?php

/*
 * @package    agitation/base-bundle
 * @link       http://github.com/agitation/base-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\BaseBundle\Validation;

use Agit\BaseBundle\Exception\InvalidValueException;
use Agit\BaseBundle\Tool\Translate;

class StringValidator extends AbstractValidator
{
    public function validate($value, $minLength = null, $maxLength = null, $allowLinebreaks = false)
    {
        if (! is_string($value)) {
            throw new InvalidValueException(Translate::t("The value must be a string."));
        }

        if (is_int($minLength) && strlen($value) < $minLength) {
            throw new InvalidValueException(sprintf(Translate::t("The value is too short, it must have at least %s characters."), $minLength));
        }

        if (is_int($maxLength) && strlen($value) > $maxLength) {
            throw new InvalidValueException(sprintf(Translate::t("The value is too long, it must have at most %s characters."), $maxLength));
        }

        // filtering for control characters, but maybe allow \n, \r and \t
        $allowedCntrlChars = $allowLinebreaks ? [9, 10, 13] : [];

        if (preg_match_all('/[[:cntrl:]]/', $value, $matches)) {
            foreach ($matches[0] as $match) {
                if (! in_array(ord($match), $allowedCntrlChars)) {
                    throw new InvalidValueException(Translate::t("The value contains invalid characters."));
                }
            }
        }
    }
}