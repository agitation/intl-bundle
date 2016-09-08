<?php

/*
 * @package    agitation/base-bundle
 * @link       http://github.com/agitation/base-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\IntlBundle\Service;

use Agit\IntlBundle\Tool\Translate;
use Twig_Extension;
use Twig_Function_Method;

class TranslationExtension extends Twig_Extension
{
    private $localeService;

    public function __construct(LocaleService $localeService)
    {
        $this->localeService = $localeService;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            "t"                => new Twig_Function_Method($this, "t",  ["is_safe" => ["all"]]),
            "n"                => new Twig_Function_Method($this, "n",  ["is_safe" => ["all"]]),
            "x"                => new Twig_Function_Method($this, "x",  ["is_safe" => ["all"]]),
            "ts"               => new Twig_Function_Method($this, "ts", ["is_safe" => ["all"]]),
            "getActiveLocales" => new Twig_Function_Method($this, "getActiveLocales")

        ];
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return "translation";
    }

    public function t($string)
    {
        return Translate::t($string);
    }

    public function n($string1, $string2, $num)
    {
        return Translate::n($string1, $string2, $num);
    }

    public function x($ctxt, $string)
    {
        return Translate::x($ctxt, $string);
    }

    public function ts($string)
    {
        $args = array_slice(func_get_args(), 1);
        $translated = $this->t($string);

        return vsprintf($translated, $args);
    }

    public function getActiveLocales()
    {
        return $this->localeService->getActiveLocales();
    }
}
