<?php
declare(strict_types=1);

/*
 * @package    agitation/intl-bundle
 * @link       http://github.com/agitation/intl-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\IntlBundle\EventListener;

use Agit\CldrBundle\Adapter\TimeAdapter;
use Agit\IntlBundle\Event\TranslationsEvent;
use Agit\IntlBundle\Service\LocaleService;
use Gettext\Translation;

class TranslationCldrListener
{
    private $localeService;

    private $timeAdapter;

    public function __construct(LocaleService $localeService, TimeAdapter $timeAdapter)
    {
        $this->localeService = $localeService;
        $this->timeAdapter = $timeAdapter;
    }

    public function onRegistration(TranslationsEvent $event)
    {
        $defaultLocale = $this->localeService->getDefaultLocale();
        $availableLocales = $this->localeService->getAvailableLocales();

        $lists = [
            'month' => $this->timeAdapter->getMonths($defaultLocale, $availableLocales),
            'weekday' => $this->timeAdapter->getWeekdays($defaultLocale, $availableLocales)
        ];

        foreach ($availableLocales as $locale)
        {
            foreach ($lists as $type => $list)
            {
                foreach ($list as $id => $elem)
                {
                    $longTrans = new Translation('', $elem->getName($defaultLocale));
                    $longTrans->setTranslation($elem->getName($locale));
                    $longTrans->addReference("localedata:$type");
                    $event->addTranslation($locale, $longTrans);

                    $abbrTrans = new Translation('', $elem->getAbbr($defaultLocale));
                    $abbrTrans->setTranslation($elem->getAbbr($locale));
                    $abbrTrans->addReference("localedata:$type:$id");
                    $event->addTranslation($locale, $abbrTrans);
                }
            }
        }
    }
}
