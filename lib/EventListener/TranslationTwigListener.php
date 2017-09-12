<?php
declare(strict_types=1);
/*
 * @package    agitation/intl-bundle
 * @link       http://github.com/agitation/intl-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\IntlBundle\EventListener;

use Agit\BaseBundle\Service\FileCollector;
use Agit\IntlBundle\Event\BundleTranslationFilesEvent;
use Symfony\Component\Filesystem\Filesystem;
use Twig_Environment;

class TranslationTwigListener
{
    protected $bundleTemplatesPath = 'Resources/views';

    private $fileCollector;

    /**
     * @var Twig_Environment
     */
    private $twig;

    public function __construct(FileCollector $fileCollector, Twig_Environment $twig)
    {
        $this->fileCollector = $fileCollector;
        $this->twig = $twig;
    }

    public function onRegistration(BundleTranslationFilesEvent $event)
    {
        $bundleAlias = $event->getBundleAlias();
        $tplDir = $this->fileCollector->resolve($bundleAlias);

        // storing the old values to reset them when we’re done
        $actualCachePath = $this->twig->getCache();
        $actualAutoReload = $this->twig->isAutoReload();

        // create tmp cache path
        $filesystem = new Filesystem();
        $cachePath = $event->getCacheBasePath() . md5(__CLASS__);
        $filesystem->mkdir($cachePath);
        $twigCache = $this->twig->getCache(false);

        // setting temporary values
        $this->twig->enableAutoReload();
        $this->twig->setCache($cachePath);

        foreach ($this->fileCollector->collect($tplDir, 'twig') as $file)
        {
            $this->twig->loadTemplate($file); // force rendering

            $cacheFilePath = $twigCache->generateKey($file, $this->twig->getTemplateClass($file));
            $fileId = str_replace($tplDir, "@$bundleAlias/", $file);
            $event->registerSourceFile($fileId, $cacheFilePath);
        }

        // resetting original values
        $this->twig->setCache($actualCachePath);
        call_user_func([$this->twig, $actualAutoReload ? 'enableAutoReload' : 'disableAutoReload']);
    }
}
