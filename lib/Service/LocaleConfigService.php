<?php

/*
 * @package    agitation/intl-bundle
 * @link       http://github.com/agitation/intl-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\IntlBundle\Service;

use Agit\IntlBundle\Tool\Translate;
use Agit\SettingBundle\Event\SettingsLoadedEvent;
use Agit\SettingBundle\Service\SettingService;

class LocaleConfigService
{
    private $localeService;

    private $settingService;

    public function __construct(LocaleService $localeService, SettingService $settingService = null)
    {
        $this->localeService = $localeService;
        $this->settingService = $settingService;
    }

    public function getInternalLocale()
    {
        return ($this->settingService)
            ? $this->settingService->getValueOf("agit.internal_locale")
            : $this->localeService->getDefaultLocale();
    }

    public function getActiveLocales()
    {
        return ($this->settingService)
            ? $this->settingService->getValueOf("agit.user_locales")
            : $this->localeService->getAvailableLocales();
    }

    public function settingsLoaded(SettingsLoadedEvent $event)
    {
        $internalLocale = $event->getSettingService()->getValueOf("agit.internal_locale");
        Translate::_setAppLocale($internalLocale);
    }
}
