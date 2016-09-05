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

class NotNullValidator extends AbstractValidator
{
    public function validate($value)
    {
        if (is_null($value)) {
            throw new InvalidValueException(Translate::t("The value must not be NULL."));
        }
    }
}