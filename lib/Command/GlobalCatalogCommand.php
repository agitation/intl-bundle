<?php

/*
 * @package    agitation/intl-bundle
 * @link       http://github.com/agitation/intl-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\IntlBundle\Command;

use Agit\IntlBundle\Event\TranslationFilesEvent;
use Agit\IntlBundle\Event\TranslationsEvent;
use Gettext\Merge;
use Gettext\Translation;
use Gettext\Translations;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class GlobalCatalogCommand extends ContainerAwareCommand
{
    private $catalogSubdir = "Resources/translations";

    private $extraTranslations = [];

    protected function configure()
    {
        $this
            ->setName("agit:translations:global")
            ->setDescription("Update the global translations catalogs from bundle’s translations.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $filesystem = new Filesystem();
        $locator = $container->get("agit.common.filecollector");

        $bundles = $container->getParameter("kernel.bundles");
        $catalogPath = $container->getParameter("kernel.root_dir") . "/$this->catalogSubdir";

        $this->getContainer()->get("event_dispatcher")->dispatch(
            "agit.intl.translations.register",
            new TranslationsEvent($this));

        foreach ($container->getParameter("agit.intl.locales") as $locale) {
            $catalog = new Translations();

            foreach ($bundles as $alias => $namespace) {
                $bundlePath = $locator->resolve($alias);
                $bundleCatalogFile = "$bundlePath/$this->catalogSubdir/bundle.$locale.po";

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

            // NOTE: we delete all headers and only set language in order to avoid garbage commits
            $catalog->deleteHeaders();
            $catalog->setLanguage($locale);
            $catalog->setHeader("Content-Type", "text/plain; charset=UTF-8");

            $messagesPath = "$catalogPath/$locale/LC_MESSAGES";
            $filesystem->dumpFile("$messagesPath/agit.po", $catalog->toPoString());
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
