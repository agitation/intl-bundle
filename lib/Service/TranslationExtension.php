<?php
declare(strict_types=1);
/*
 * @package    agitation/intl-bundle
 * @link       http://github.com/agitation/intl-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\IntlBundle\Service;

use Agit\IntlBundle\Tool\Translate;
use Twig_Extension;
use Twig_SimpleFunction;

class TranslationExtension extends Twig_Extension
{
    private $localeConfigService;

    public function __construct(LocaleConfigService $localeConfigService)
    {
        $this->localeConfigService = $localeConfigService;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('t', [$this, 't'], ['is_safe' => ['all']]),
            new Twig_SimpleFunction('n', [$this, 'n'], ['is_safe' => ['all']]),
            new Twig_SimpleFunction('x', [$this, 'x'], ['is_safe' => ['all']]),
            new Twig_SimpleFunction('ts', [$this, 'ts'], ['is_safe' => ['all']]),
            new Twig_SimpleFunction('getActiveLocales', [$this, 'getActiveLocales'])

        ];
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'translation';
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
        return $this->localeConfigService->getActiveLocales();
    }
}
