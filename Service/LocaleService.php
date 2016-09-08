<?php

/*
 * @package    agitation/base-bundle
 * @link       http://github.com/agitation/base-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\IntlBundle\Service;

use Agit\IntlBundle\Exception\InternalErrorException;
use Agit\IntlBundle\Tool\Translate;

class LocaleService
{
    private $defaultLocale = "en_GB";

    private $currentLocale;

    private $primaryLocale;

    private $availableLocales;

    private $activeLocales;

    private $translationsPath;

    public function __construct(LocaleConfigInterface $localeConfigService = null, $availableLocales, $translationsPath, $textdomain)
    {
        $this->availableLocales = $availableLocales;

        if (! $localeConfigService) {
            $this->primaryLocale = $this->defaultLocale;
            $this->activeLocales = $this->availableLocales;
        } else {
            $this->primaryLocale = $localeConfigService->getPrimaryLocale();
            $this->activeLocales = $localeConfigService->getActiveLocales();
        }

        bindtextdomain($textdomain, $translationsPath);
        textdomain($textdomain);

        $this->setLocale($this->primaryLocale);
        Translate::_setAppLocale($this->primaryLocale);
    }

    // default locale for this application
    public function getDefaultLocale()
    {
        return $this->defaultLocale;
    }

    // available locales for this application
    public function getAvailableLocales()
    {
        return $this->availableLocales;
    }

    // default locale for this installation
    public function getPrimaryLocale()
    {
        return $this->primaryLocale;
    }

    // available locales for this installation
    public function getActiveLocales()
    {
        return $this->activeLocales;
    }

    public function setLocale($locale)
    {
        if (! in_array($locale, $this->availableLocales)) {
            throw new InternalErrorException("The locale `$locale` is not available.");
        }

        Translate::_setLocale($locale);

        $this->currentLocale = $locale;
    }

    public function getLocale()
    {
        return $this->currentLocale;
    }

    public function getUserLocale()
    {
        $browserLocale = isset($_SERVER["HTTP_ACCEPT_LANGUAGE"]) ? \Locale::acceptFromHttp($_SERVER["HTTP_ACCEPT_LANGUAGE"]) : "";

        $userLocale = (in_array($browserLocale, $this->availableLocales))
            ? $browserLocale
            : "";

        // try locales with same language but different country
        if (! $userLocale) {
            foreach ($this->availableLocales as $locale) {
                if (strtolower(substr($locale, 0, 2)) === strtolower(substr($browserLocale, 0, 2))) {
                    $userLocale = $locale;
                    break;
                }
            }
        }

        return $userLocale ?: reset($this->availableLocales);
    }
}
