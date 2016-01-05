<?php

namespace VR\DataMapperBundle\Tests\DataMapper;
use VR\DataMapperBundle\DataMapper\DataMapper;
use VR\DataMapperBundle\DataMapper\Map;
use VR\DataMapperBundle\DataMapper\MapElement;

/**
 * Class DataMapperTest
 *
 * @package unit
 *
 * @author Jimmie Louis Borch
 */
class DataMapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DataMapper
     */
    public $class;

    public function setUp()
    {
        $this->class = new DataMapper();
    }

    public function testSetMap()
    {
        $map = new Map();
        $this->class->setMap($map);
        $this->assertEquals($map, $this->class->getMap());
    }

    public function mapProvider()
    {
        $data = array();
        $data[] = array(
            new Map(
                array(
                    new MapElement('fooSource', 'fooTarget', 'boolean', array(), false),
                    new MapElement('barSource', 'barTarget'),
                    new MapElement(null, 'modifiedDate', 'function', array('function' => 'date', 'arguments' => array('d/m Y', strtotime('2014-12-24')))),
                )
            ),
            array(
                'barSource' => 'barValue',
            ),
            array(
                'barTarget' => 'barValue',
                'fooTarget' => false,
                'modifiedDate' => '24/12 2014'
            )
        );
        $data[] = array(
            new Map(
                array(
                    array(
                        'source' => 'source_date0',
                        'target' => 'target_date0',
                        'format' => 'datetime',
                        'mapping' => array(
                            'input-format' => 'Y-m-d',
                            'output-format' => 'Y-m',
                        ),
                        'default' => 'test',
                    ),
                    array(
                        'source' => 'source_date1',
                        'target' => 'target_date1',
                        'format' => 'datetime',
                        'mapping' => array(
                            'input-format' => 'Y-m-d',
                            'output-format' => 'Y-m',
                        ),
                        'default' => 'test',
                    ),
                )
            ),
            array(
                'source_date0' => '2014-12-17',
                'source_date1' => 'Wrong format',
            ),
            array(
                'target_date0' => '2014-12',
                'target_date1' => 'test',
            )
        );
        return $data;
    }

    /**
     * @dataProvider mapProvider
     */
    public function testMap($map, $input, $expectedResult)
    {
        $this->class->setMap($map);
        $result = $this->class->map($input);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @dataProvider mapProvider
     */
    public function testMapWithFixture()
    {
        $map = json_decode(file_get_contents(dirname(__DIR__).'/_data/payloads/mapping.json'), true);
        $payload = json_decode(file_get_contents(dirname(__DIR__).'/_data/payloads/payload.json'), true);
        $this->class->setMap(new Map($map));
        $result = $this->class->map($payload);
        $this->assertInternalType('array', $result);
    }
}
