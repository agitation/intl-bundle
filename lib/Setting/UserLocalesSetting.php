<?php

/*
 * @package    agitation/intl-bundle
 * @link       http://github.com/agitation/intl-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\IntlBundle\Setting;

use Agit\IntlBundle\Tool\Translate;
use Agit\SettingBundle\Exception\InvalidSettingValueException;

class UserLocalesSetting extends AbstractLocaleSetting
{
    public function getId()
    {
        return "agit.user_locales";
    }

    public function getName()
    {
        return Translate::t("User languages");
    }

    public function getDefaultValue()
    {
        return ["en_US"];
    }

    public function validate($value)
    {
        $this->validationService->validate("array", $value);

        if (! count($value)) {
            throw new InvalidSettingValueException(Translate::t("You must select at least one user language."));
        }

        $this->validationService->validate(
            "multiSelection",
            $value,
            $this->localeService->getAvailableLocales()
        );
    }
}
