<?php
declare(strict_types=1);
/*
 * @package    agitation/intl-bundle
 * @link       http://github.com/agitation/intl-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\IntlBundle\Event;

use Agit\IntlBundle\Command\BundleCatalogCommand;
use Gettext\Translation;
use Symfony\Component\EventDispatcher\Event;

/**
 * This event can be used to add empty translations which are not caught
 * by the extractors.
 */
class BundleTranslationsEvent extends Event
{
    private $processor;

    private $bundleAlias;

    public function __construct(BundleCatalogCommand $processor, $bundleAlias)
    {
        $this->processor = $processor;
        $this->bundleAlias = $bundleAlias;
    }

    public function getBundleAlias()
    {
        return $this->bundleAlias;
    }

    public function addTranslation(Translation $translation)
    {
        return $this->processor->addTranslation($translation);
    }
}
