<?php

/*
 * @package    agitation/intl-bundle
 * @link       http://github.com/agitation/intl-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\IntlBundle\Service;

use Agit\IntlBundle\Exception\InternalErrorException;
use Agit\IntlBundle\Tool\Translate;
use Locale;

class LocaleService
{
    private $defaultLocale = "en_US";

    private $currentLocale;

    private $availableLocales;

    private $translationsPath;

    public function __construct($availableLocales, $translationsPath, $textdomain)
    {
        $this->availableLocales = $availableLocales;

        bindtextdomain($textdomain, $translationsPath);
        textdomain($textdomain);

        $this->setLocale($this->defaultLocale);
        Translate::_setAppLocale($this->defaultLocale);
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
        $browserLocale = isset($_SERVER["HTTP_ACCEPT_LANGUAGE"]) ? Locale::acceptFromHttp($_SERVER["HTTP_ACCEPT_LANGUAGE"]) : "";

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
