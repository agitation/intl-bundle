<?php

/*
 * @package    agitation/base-bundle
 * @link       http://github.com/agitation/base-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\IntlBundle\Tests\Pluggable;

use Agit\IntlBundle\Pluggable\RegistrationData;
use Doctrine\Common\Cache\ArrayCache;
use Symfony\Component\EventDispatcher\EventDispatcher;

class AbstractCacheAwarePluggableServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testWarmUpValues()
    {
        //         $cacheProvider = new ArrayCache();
//         $eventDispatcher = new EventDispatcher();
//         $pluggableService = $this->createMockPluggableService($eventDispatcher, $cacheProvider);
//
//         // as we can't interact with the EventDispatcher during the warm-up,
//         // we simply set the extension manually and call warmUp afterwards.
//         $pluggableService->register($this->createRegistrationData());
//         $pluggableService->warmUp('/we/dont/care');
//
//         $this->assertEquals(
//             [(object)['foo' => 'bar']],
//             $cacheProvider->fetch('agit.test.pluggable'));
    }

    private function createMockPluggableService($eventDispatcher, $cacheProvider)
    {
        //         $pluggableService = $this->getMockBuilder('\Agit\IntlBundle\Pluggable\AbstractCacheAwarePluggableService')
//             ->setMethods(['getServiceTag'])
//             ->getMock();
//
//         $pluggableService->expects($this->any())
//             ->method('getServiceTag')
//             ->will($this->returnValue('agit.test.pluggable'));
//
//         $pluggableService->setEventDispatcher($eventDispatcher);
//         $pluggableService->setCacheProvider($cacheProvider);
//
//         return $pluggableService;
    }

    private function createRegistrationData()
    {
        //         $registrationData = new RegistrationData();
//         $registrationData->set('foo', 'bar');
//
//         return $registrationData;
    }
}
