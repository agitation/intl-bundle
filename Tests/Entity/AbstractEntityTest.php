<?php

/*
 * @package    agitation/base-bundle
 * @link       http://github.com/agitation/base-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\IntlBundle\Tests\Entity;

class AbstractEntityTest extends \PHPUnit_Framework_TestCase
{
    public function test__ToString()
    {
        $entity = $this->createMockEntity();
        $this->assertSame('123', $entity->__toString());
    }

    public function testGetEntityClass()
    {
        $mockName = 'Dummy';
        $entity = $this->createMockEntity($mockName);
        $this->assertSame($mockName, $entity->getEntityClass());
    }

    private function createMockEntity($mockName = null)
    {
        $mockInst = $this
            ->getMockBuilder('\Agit\IntlBundle\Entity\IdentityAwareTrait')
            ->setMethods(['getId']);

        if ($mockName) {
            $mockInst->setMockClassName('Dummy');
        }

        $entity = $mockInst->getMock();

        $entity->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(123));

        return $entity;
    }
}
