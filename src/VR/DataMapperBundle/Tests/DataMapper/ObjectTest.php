<?php

namespace VR\DataMapperBundle\Tests\DataMapper;

use VR\DataMapperBundle\DataMapper\Object;

/**
 * Class ObjectTest
 *
 * @package VR\DataMapperBundle\Tests\DataMapper
 *
 * @author Jimmie Louis Borch
 */
class ObjectTest extends \PHPUnit_Framework_TestCase
{
    public function testExists()
    {
        $array = array(
            'foo' => array(
                'bar' => array(
                    array(
                        'biz' => array(
                            0,
                            1,
                            array(
                                'baz' => 'This is the value we want',
                            ),
                        )
                    ),
                    1,
                    2,
                    3,
                )
            )
        );
        $key = 'foo.bar[0].biz[2].baz';
        $expectedValue = 'This is the value we want';

        $result = Object::exists($key, $array, $value);
        $this->assertTrue($result);
        $this->assertEquals($expectedValue, $value);
    }

    public function testAssign()
    {
        $array = array(
            'foo' => array(
                'bar' => array(
                    array(
                        'biz' => array(
                            0,
                            1,
                            array(
                                'baz' => 'This is the value we want',
                            ),
                        )
                    ),
                    1,
                    2,
                    3,
                )
            )
        );
        $expected = array(
            'foo' => array(
                'bar' => array(
                    array(
                        'biz' => array(
                            0,
                            1,
                            array(
                                'baz' => 'This is the value we want',
                                'waz' => 'This is the value we want to set',
                            ),
                        )
                    ),
                    1,
                    2,
                    3,
                )
            )
        );
        $key = 'foo.bar[0].biz[2].waz';
        $value = 'This is the value we want to set';

        Object::assign($key, $array, $value);
        $this->assertEquals($expected, $array);
    }

    public function testAssignObject()
    {
        $key = '->foo->bar->biz[0]->baz';
        $value = 'Some random value';
        Object::assign($key, $object, $value);
        $anotherKey = '->fii.test->value';
        $anotherValue = 'Another random value';
        Object::assign($anotherKey, $object, $anotherValue);

        $this->assertInstanceOf('\stdClass', $object);
        $this->assertEquals($value, $object->foo->bar->biz[0]->baz);
        $this->assertEquals($anotherValue, $object->fii['test']->value);
    }
}
