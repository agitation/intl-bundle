<?php

/*
 * @package    agitation/intl-bundle
 * @link       http://github.com/agitation/intl-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\IntlBundle\Event;

use Agit\IntlBundle\EventListener\CatalogCacheEventListener;
use Gettext\Translation;
use Symfony\Component\EventDispatcher\Event;

/**
 * This event is triggered before the files for generating a global catalog are
 * collected and merged with the global catalog. Through this event, listeners
 * can add translation entries directly to the global catalog.
 */
class TranslationsEvent extends Event
{
    private $processor;

    public function __construct(CatalogCacheEventListener $processor)
    {
        $this->processor = $processor;
    }

    public function addTranslation($locale, Translation $translation)
    {
        return $this->processor->addTranslation($locale, $translation);
    }
}
