<?php

namespace VR\DataMapperBundle\Tests\DataMapper;

use VR\DataMapperBundle\DataMapper\MapElement;

/**
 * Class MapElementTest
 *
 * @package VR\DataMapperBundle\Tests\DataMapper
 *
 * @author Jimmie Louis Borch
 */
class MapElementTest extends \PHPUnit_Framework_TestCase
{
    public function elementProvider()
    {
        return array(
            array(
                new MapElement(
                    null,
                    'bar',
                    MapElement::FORMAT_FUNCTION,
                    array('function' => 'date', 'arguments' => array('Y'))
                ),
                array(),
                date('Y')
            ),
            array(
                new MapElement(
                    'foo',
                    'bar',
                    MapElement::FORMAT_FUNCTION,
                    array('function' => 'substr', 'arguments' => array('{value}', 0, 11))
                ),
                'This is a long string',
                'This is a l'
            ),
            array(
                new MapElement(
                    'foo',
                    'bar',
                    MapElement::FORMAT_FUNCTION,
                    array('function' => 'ucwords', 'arguments' => array('{value}'))
                ),
                'uppercase words',
                'Uppercase Words'
            ),
            array(
                new MapElement(
                    'foo',
                    'bar',
                    MapElement::FORMAT_FUNCTION,
                    array('function' => 'regex', 'arguments' => array('{value}', '/(\S*)\s(.*)/i', 1))
                ),
                'First Middle Last',
                'First'
            ),
            array(
                new MapElement(
                    'foo',
                    'bar',
                    MapElement::FORMAT_FUNCTION,
                    array('function' => 'regex', 'arguments' => array('{value}', '/(\S*)\s(.*)/i', 2))
                ),
                'First Middle Last',
                'Middle Last'
            ),
            array(new MapElement('foo', 'bar', MapElement::FORMAT_STRING), 1, '1'),
            array(new MapElement('foo', 'bar', MapElement::FORMAT_STRING), 'test', 'test'),
            array(new MapElement('foo', 'bar', MapElement::FORMAT_INTEGER), '1', 1),
            array(new MapElement('foo', 'bar', MapElement::FORMAT_INTEGER), 123, 123),
            array(
                new MapElement('foo', 'bar', MapElement::FORMAT_MAPPING, array('1' => true,'0' => false)),
                '1',
                true
            ),
            array(
                new MapElement('foo', 'bar', MapElement::FORMAT_MAPPING, array('1' => true,'0' => false)),
                '0',
                false
            ),
            array(
                new MapElement('foo', 'bar', MapElement::FORMAT_MAPPING, array('1' => true,'0' => false), false),
                'true',
                false
            ),
            array(
                new MapElement('foo', 'bar', MapElement::FORMAT_DATETIME, array('input-format' => 'Y-m-d H:i:s', 'output-format' => 'H:i:s Y-m-d')),
                \DateTime::createFromFormat('Y-m-d H:i:s', '2014-11-14 13:30:00')->format('Y-m-d H:i:s'),
                \DateTime::createFromFormat('Y-m-d H:i:s', '2014-11-14 13:30:00')->format('H:i:s Y-m-d')
            ),
        );
    }

    /**
     * @dataProvider elementProvider
     */
    public function testEncode(MapElement $element, $input, $expectedOutput)
    {
        $this->assertSame($expectedOutput, $element->encode($input));
    }

    public function testMapWithMode()
    {
        $input = array(
            'foo' => 'Foo',
            'bar' => 'Bar',
        );

        $a = new MapElement('foo', 'target');
        $a->map($input, $output);

        $b = new MapElement(null, 'target', 'string', array(), ' ');
        $b->setMode('append');
        $b->map($input, $output);

        $c = new MapElement('bar', 'target', 'string');
        $c->setMode('append');
        $c->map($input, $output);

        $c = new MapElement(null, 'target', 'string', array(), 'Test ');
        $c->setMode('prepend');
        $c->map($input, $output);


        $expected = array(
            'target' => 'Test Foo Bar',
        );

        $this->assertEquals($expected, $output);
    }
}
