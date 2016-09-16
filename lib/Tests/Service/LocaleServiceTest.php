<?php

/*
 * @package    agitation/intl-bundle
 * @link       http://github.com/agitation/intl-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\IntlBundle\Tests\Service;

use Agit\IntlBundle\Service\LocaleService;

class LocaleServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testGetDefaultLocale()
    {
        $localeService = $this->createLocaleService();
        $this->assertSame('en_GB', $localeService->getDefaultLocale());
    }

    public function testGetAvailableLocales()
    {
        $localeService = $this->createLocaleService();
        $this->assertSame(['en_GB', 'de_DE'], $localeService->getAvailableLocales());
    }

    public function testSetLocale()
    {
        $localeService = $this->createLocaleService();
        $localeService->setLocale('de_DE');
        $this->assertSame('de_DE', $localeService->getLocale());
    }

    public function testSetLocaleThrowsException()
    {
        $this->setExpectedException('\Agit\IntlBundle\Exception\InternalErrorException');
        $localeService = $this->createLocaleService();
        $localeService->setLocale('nl_NL');
    }

    private function createLocaleService()
    {
        return new LocaleService(['en_GB', 'de_DE'], '/tmp/does/not/exist', 'foobar');
    }
}
