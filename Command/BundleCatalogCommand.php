<?php

/*
 * @package    agitation/intl-bundle
 * @link       http://github.com/agitation/intl-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\IntlBundle\Command;

use Exception;
use Agit\IntlBundle\Event\TranslationFilesEvent;
use Agit\IntlBundle\Event\TranslationsEvent;
use Agit\IntlBundle\Tool\Translate;
use Gettext\Translation;
use Gettext\Translations;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class BundleCatalogCommand extends ContainerAwareCommand
{
    private $catalogSubdir = "Resources/translations";

    private $frontendSubdir = "Resources/public/js/var";

    private $cacheBasePath;

    private $extractorOptions = ["functions" => [
        "t" => "gettext", "ts" => "gettext", "tl" => "gettext", "noop" => "gettext",
        "x" => "pgettext", "xl" => "pgettext", "noopX" => "pgettext",
        "n" => "ngettext", "nl" => "ngettext", "noopN" => "ngettext"
    ]];

    private $extraSourceFiles = [];

    protected function configure()
    {
        $this
            ->setName("agit:translations:bundle")
            ->setDescription("Extract translatable strings in a bundle’s into .po and .json files, then add/merge them to the global catalogs.")
            ->addArgument("bundle", InputArgument::REQUIRED, "bundle alias, e.g. AcmeFoobarBundle.")
            ->addArgument("locales", InputArgument::OPTIONAL, "Comma-separated list of locales supported by the bundle. Optional; if empty, locales from parameters.yml will be used.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $filesystem = new Filesystem();

        $bundleAlias = $input->getArgument("bundle");
        $bundlePath = $container->get("agit.common.filecollector")->resolve($bundleAlias);

        $defaultLocale = $container->get("agit.intl.locale")->getDefaultLocale();
        $locales = array_map("trim", explode(",", (string)$input->getArgument("locales"))) ?: $container->getParameter("agit.intl.locales");

        $globalCatalogPath = $container->getParameter("kernel.root_dir") . "/$this->catalogSubdir";
        $this->cacheBasePath = sprintf("%s/agit.intl.temp/%s", sys_get_temp_dir(), $bundleAlias);
        $filesystem->mkdir($this->cacheBasePath);

        $finder = (new Finder())->in("$bundlePath")
            ->name("*\.php")
            ->name("*\.js")
            ->notPath("/test.*/i")
            ->notPath("public/js/ext");

        $files = [];

        foreach ($finder as $file) {
            $filePath = $file->getRealpath();
            $alias = str_replace($bundlePath, "@$bundleAlias/", $filePath);
            $files[$alias] = $filePath;
        }

        $this->getContainer()->get("event_dispatcher")->dispatch(
            "agit.intl.files.register",
            new TranslationFilesEvent($this, $bundleAlias, $this->cacheBasePath)
        );

        $files += $this->extraSourceFiles;

        $frontendFiles = array_filter($files, function ($file) { return preg_match("|\.js$|", $file); });
        $backendFiles = array_filter($files, function ($file) { return ! preg_match("|\.js$|", $file); });

        $frontendCatalogs = "";

        foreach ($locales as $locale) {

            if (!preg_match("|^[a-z]{2}_[A-Z]{2}|", $locale))
                throw new Exception("Invlid locale: $locale");

            // we use the global catalog as source for already translated strings
            $globalCatalogFile = "$globalCatalogPath/$locale/LC_MESSAGES/agit.po";
            $globalCatalog = $filesystem->exists($globalCatalogFile)
                ? Translations::fromPoFile($globalCatalogFile)
                : new Translations();

            $bundleCatalogFile = "$bundlePath/$this->catalogSubdir/bundle.$locale.po";
            $oldBundleCatalog = ($filesystem->exists($bundleCatalogFile))
                ? Translations::fromPoFile($bundleCatalogFile)
                : new Translations();

            // NOTE: we delete all headers and only set language, in order to avoid garbage commits
            $bundleCatalog = new Translations();
            $bundleCatalog->deleteHeaders();
            $bundleCatalog->setLanguage($locale);

            // first: only JS messages

            foreach ($frontendFiles as $file) {
                $bundleCatalog->addFromJsCodeFile($file, $this->extractorOptions);
            }

            $bundleCatalog->mergeWith($oldBundleCatalog, 0);
            $bundleCatalog->mergeWith($globalCatalog, 0);

            if ($bundleCatalog->count() && $locale !== $defaultLocale) {
                $transMap = [];

                foreach ($bundleCatalog as $entry) {
                    $msgid = ltrim($entry->getId(), "\004");
                    $msgstr = $entry->getTranslation();

                    $transMap[$msgid] = $entry->hasPlural()
                        ? array_merge([$msgstr], $entry->getPluralTranslations())
                        : $msgstr;
                }

                $frontendCatalogs .= sprintf("ag.intl.register(\"%s\", %s);\n\n",
                    $locale,
                    json_encode($transMap, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
                );
            }

            // now the same with all messages

            foreach ($backendFiles as $file) {
                $bundleCatalog->addFromPhpCodeFile($file, $this->extractorOptions);
            }

            $bundleCatalog->mergeWith($oldBundleCatalog, 0);
            $bundleCatalog->mergeWith($globalCatalog, 0);

            $catalog = $bundleCatalog->toPoString();
            $catalog = str_replace(array_values($files), array_keys($files), $catalog);

            if ($bundleCatalog->count()) {
                $filesystem->dumpFile("$bundlePath/$this->catalogSubdir/bundle.$locale.po", $catalog);
            }
        }

        if ($frontendCatalogs) {
            $filesystem->dumpFile("$bundlePath/$this->frontendSubdir/translations.js", $frontendCatalogs);
        }

        $filesystem->remove($this->cacheBasePath);
    }

    public function registerSourceFile($alias, $path)
    {
        $this->extraSourceFiles[$alias] = $path;
    }
}
