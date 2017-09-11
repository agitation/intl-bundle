<?php
declare(strict_types=1);
/*
 * @package    agitation/intl-bundle
 * @link       http://github.com/agitation/intl-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\IntlBundle\Tests\Service;

use Agit\IntlBundle\Tool\Translate;

class TranslateTest extends \PHPUnit_Framework_TestCase
{
    public function testT()
    {
        // without a locale and a textdomain loaded,
        // the translation service should just return the original string.
        $this->assertSame('foobar', Translate::t('foobar'));
    }

    public function testX()
    {
        // without a locale and a textdomain loaded,
        // the translation service should just return the original string.
        $this->assertSame('foobar', Translate::x('foo', 'foobar'));
    }

    public function testN()
    {
        $this->assertSame('%s cars', Translate::n('%s car', '%s cars', 0));
        $this->assertSame('%s car', Translate::n('%s car', '%s cars', 1));
        $this->assertSame('%s cars', Translate::n('%s car', '%s cars', 2));
    }
}
