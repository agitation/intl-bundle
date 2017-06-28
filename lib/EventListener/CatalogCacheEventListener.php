<?php

/*
 * @package    agitation/intl-bundle
 * @link       http://github.com/agitation/intl-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\IntlBundle\EventListener;

use Agit\IntlBundle\Event\TranslationsEvent;
use Gettext\Merge;
use Gettext\Translation;
use Gettext\Translations;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class CatalogCacheEventListener implements CacheWarmerInterface
{
    const BUNDLE_CATALOG_DIR = "Resources/translations";

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    private $catalogDir;

    private $textdomain;

    private $bundles;

    private $locales;

    private $extraTranslations = [];

    public function __construct(KernelInterface $kernel, EventDispatcherInterface $eventDispatcher, array $bundles, array $locales, $catalogDir, $textdomain)
    {
        $this->kernel = $kernel;
        $this->eventDispatcher = $eventDispatcher;
        $this->bundles = $bundles;
        $this->locales = $locales;
        $this->catalogDir = $catalogDir;
        $this->textdomain = $textdomain;
    }

    public function warmUp($cacheDir)
    {
        $this->process();
        bindtextdomain($this->textdomain, $this->catalogDir);
        textdomain($this->textdomain);
    }

    public function isOptional()
    {
        return true;
    }

    protected function process()
    {
        $filesystem = new Filesystem();

        $this->eventDispatcher->dispatch(
            "agit.intl.global.translations",
            new TranslationsEvent($this));

        foreach ($this->locales as $locale) {
            $messagesPath = sprintf("%s/%s/LC_MESSAGES", $this->catalogDir, $locale);
            $catalogFile = "$messagesPath/agit.po";
            $oldCatalog = $filesystem->exists($catalogFile)
                    ? Translations::fromPoFile($catalogFile)
                    : new Translations();

            foreach ($oldCatalog as $translation) {
                $translation->deleteReferences();
            }

            $catalog = new Translations();
            $catalog->mergeWith($oldCatalog, 0);

            foreach ($this->bundles as $alias => $namespace) {
                $bundlePath = $this->kernel->locateResource("@$alias");
                $bundleCatalogFile = sprintf("%s/%s/bundle.%s.po", $bundlePath, self::BUNDLE_CATALOG_DIR, $locale);

                $bundleCatalog = $filesystem->exists($bundleCatalogFile)
                    ? Translations::fromPoFile($bundleCatalogFile)
                    : new Translations();

                $catalog->mergeWith($bundleCatalog, Merge::ADD);
            }

            if (isset($this->extraTranslations[$locale])) {
                foreach ($this->extraTranslations[$locale] as $translation) {
                    $catalog[] = $translation;
                }
            }

            $catalog->deleteHeaders();
            $catalog->setLanguage($locale);
            $catalog->setHeader("Content-Type", "text/plain; charset=UTF-8");

            $filesystem->dumpFile($catalogFile, $catalog->toPoString());
            $filesystem->dumpFile("$messagesPath/agit.mo", $catalog->toMoString());
        }
    }

    public function addTranslation($locale, Translation $translation)
    {
        if (! isset($this->extraTranslations[$locale])) {
            $this->extraTranslations[$locale] = [];
        }

        $this->extraTranslations[$locale][] = $translation;
    }
}
