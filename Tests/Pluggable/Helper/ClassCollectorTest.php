<?php

/*
 * @package    agitation/base-bundle
 * @link       http://github.com/agitation/base-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\IntlBundle\Tests\Helper;

use Agit\IntlBundle\Service\ClassCollector;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Config\FileLocator;

class ClassCollectorTest extends WebTestCase
{
    // functional test, because of dependency on Kernel
    // (the FileLocator used by ClassCollector must resolve the paths of registered bundles)
    public function testCollect()
    {
        $dummyNamespace = 'AgitIntlBundle:Tests:Pluggable:Helper:_data';

        $kernel = static::createKernel();
        $kernel->boot();

        $classCollector = new ClassCollector(new FileLocator($kernel));
        $fileList = $classCollector->collect($dummyNamespace);

        sort($fileList);

        $expected = [
            '\Agit\IntlBundle\Tests\Helper\_data\DummyFile1',
            '\Agit\IntlBundle\Tests\Helper\_data\DummyFile2'
        ];

        $this->assertSame($expected, $fileList);
    }
}
