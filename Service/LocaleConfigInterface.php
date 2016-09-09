<?php

/*
 * @package    agitation/intl-bundle
 * @link       http://github.com/agitation/intl-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\IntlBundle\Service;

/**
 * May be implemented by a bundle to provide a UI-configured services (e.g.
 * through the agitation/settings bundle). The service must have the tag
 * `agit.intl.localeconfig` and implement this interface.
 *
 * NB: The *active* locales are not the *available* locales, and the *primary* locale
 * is not the *default* locale – i.e. we cannot use the Intl/LocaleService here.
 */
interface LocaleConfigInterface
{
    public function getActiveLocales();

    public function getPrimaryLocale();
}
