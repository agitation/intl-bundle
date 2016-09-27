<?php

/*
 * @package    agitation/intl-bundle
 * @link       http://github.com/agitation/intl-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\IntlBundle\Setting;

use Agit\SettingBundle\Service\AbstractSetting;
use Agit\ValidationBundle\ValidationService;
use Agit\IntlBundle\Service\LocaleService;

abstract class AbstractLocaleSetting extends AbstractSetting
{
    protected $localeService;

    protected $validationService;

    public function __construct(LocaleService $localeService, ValidationService $validationService)
    {
        $this->validationService = $validationService;
        $this->localeService = $localeService;
    }
}
