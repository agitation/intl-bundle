<?php

/*
 * @package    agitation/base-bundle
 * @link       http://github.com/agitation/base-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\IntlBundle\Tests\Service;

use Agit\IntlBundle\Service\CronService;
use Symfony\Component\EventDispatcher\EventDispatcher;

class CronServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerValidCronTimes
     */
    public function testParseCronTime($cronTime, $expectedResult)
    {
        $cronService = new CronService(new EventDispatcher());
        $this->assertSame($expectedResult, $cronService->parseCronTime($cronTime));
    }

    /**
     * @dataProvider providerInvalidCronTimes
     */
    public function testParseCronTimeException($cronTime)
    {
        $this->setExpectedException('Agit\IntlBundle\Exception\InternalErrorException');

        $cronService = new CronService(new EventDispatcher());
        $cronService->parseCronTime($cronTime);
    }

    /**
     * @dataProvider providerCronAppliesTrue
     */
    public function testCronAppliesTrue($cronTime)
    {
        $cronService = new CronService(new EventDispatcher());
        $cronService->setDate(new \DateTime("2015-09-30 12:15"));

        $this->assertTrue($cronService->cronApplies($cronTime));
    }

    /**
     * @dataProvider providerCronAppliesFalse
     */
    public function testCronAppliesFalse($cronTime)
    {
        $cronService = new CronService(new EventDispatcher());
        $cronService->setDate(new \DateTime("2015-09-30 12:15"));

        $this->assertFalse($cronService->cronApplies($cronTime));
    }

    public function providerValidCronTimes()
    {
        return [
            ['* * * * *', [null, null, null, null, null]],
            ['*/15 * * * *', [[0, 15, 30, 45], null, null, null, null]],
            ['* 6,12,18 * * *', [null, [6, 12, 18], null, null, null]]
        ];
    }

    public function providerInvalidCronTimes()
    {
        return [
            ['* * * *'],
            ['** * * * *'],
            ['* 40 * * *'],
            ['* ,1,2 * * *'],
            ['* 1,2, * * *'],
            ['* -3 * * *'],
            ['* .5 * * *'],
            ['* * 0 * *'],
            ['**/10 * * * *']
        ];
    }

    public function providerCronAppliesTrue()
    {
        return [
            ['* * * * *'],
            ['*/15 * * * *'],
            ['* 6,12,18 * * *'],
            ['*/15 12 30 9 *'],
            ['* * * * 3'],
        ];
    }

    public function providerCronAppliesFalse()
    {
        return [
            ['* * * * 5'],
            ['*/23 * * * *'],
            ['1 2,3 * * 3']
        ];
    }
}
