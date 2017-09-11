<?php
declare(strict_types=1);
/*
 * @package    agitation/intl-bundle
 * @link       http://github.com/agitation/intl-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\IntlBundle\Tool;

class Translate
{
    // used internally to store the currently set locale
    private static $locale = 'en_US';

    private static $appLocale = 'en_US';

    public static function getLocale()
    {
        return self::$locale;
    }

    public static function getAppLocale()
    {
        return self::$appLocale;
    }

    public static function t($string)
    {
        return gettext($string);
    }

    public static function n($string1, $string2, $num)
    {
        return ngettext($string1, $string2, $num);
    }

    public static function x($context, $string)
    {
        // based on http://www.php.net/manual/de/book.gettext.php#89975
        $contextString = "{$context}\004{$string}";
        $translation = self::t($contextString);

        return ($translation === $contextString) ? $string : $translation;
    }

    // like t(), only in the appLocale. Useful for logging etc.
    public static function tl($string)
    {
        $locale = self::$locale;
        self::_setLocale(self::$appLocale);
        $translation = self::t($string);
        self::_setLocale($locale);

        return $translation;
    }

    // like n(), only in the appLocale. Useful for logging etc.
    public static function nl($string1, $string2, $num)
    {
        $locale = self::$locale;
        self::_setLocale(self::$appLocale);
        $translation = self::n($string1, $string2, $num);
        self::_setLocale($locale);

        return $translation;
    }

    // like x(), only in the appLocale. Useful for logging etc.
    public static function xl($context, $string)
    {
        $locale = self::$locale;
        self::_setLocale(self::$appLocale);
        $translation = self::x($context, $string);
        self::_setLocale($locale);

        return $translation;
    }

    /**
     * This method is just a helper to ensure that strings are caught by xgettext.
     * The string itself will usually be translated in a different context.
     * @param mixed $string
     */
    public static function noop($string)
    {
        return $string;
    }

    /**
     * Same as noop(), only for strings with plural forms.
     * @param mixed $string1
     * @param mixed $string2
     * @param mixed $num
     */
    public static function noopN($string1, $string2, $num)
    {
        return $string1;
    }

    /**
     * Same as noop(), only for strings with context.
     * @param mixed $context
     * @param mixed $string
     */
    public static function noopX($context, $string)
    {
        return $string;
    }

    /**
     * DO NOT CALL THIS METHOD; use LocaleService->setLocale instead.
     * This method is only public because both Translate and LocaleService
     * need its functionality and we want to avoid duplicate code.
     * @param mixed $locale
     */
    public static function _setLocale($locale)
    {
        putenv("LANGUAGE=$locale.UTF-8"); // for CLI
        setlocale(LC_ALL, "$locale.utf8");
        setlocale(LC_NUMERIC, 'en_US.utf8'); // avoid strange results with floats in sprintf
        self::$locale = $locale;
    }

    /**
     * DO NOT CALL THIS METHOD; it is only public for LocaleService.
     * @param mixed $locale
     */
    public static function _setAppLocale($locale)
    {
        self::$appLocale = $locale;
    }
}
