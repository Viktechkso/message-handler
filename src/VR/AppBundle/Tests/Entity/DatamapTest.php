<?php

namespace VR\AppBundle\Tests\Entity;

use VR\AppBundle\Entity\Datamap;
use VR\AppBundle\Tests\SymfonyTestCase;

/**
 * Class DatamapTest
 *
 * @package VR\AppBundle\Tests\Entity
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class DatamapTest extends SymfonyTestCase
{
    protected $client;

    protected $container;

    protected function setUp()
    {
        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
    }

    protected function tearDown()
    {
    }

    public function testGettersAndSetters()
    {
        $this->checkGetId(new Datamap());

        $this->checkGetterAndSetter(new Datamap(), 'name', 'test-name', true);
        $this->checkGetterAndSetter(new Datamap(), 'type', Datamap::TYPE_DATA, true);
        $this->checkGetterAndSetter(new Datamap(), 'map', 'test-map', true);
        $this->checkGetterAndSetter(new Datamap(), 'description', 'test-description', true);
    }

    public function testGetMapArray()
    {
        $datamap = new Datamap();

        $this->assertNull($datamap->getMapArray());

        $datamap->setMap('{"some-key": "some-value"}');
        $this->assertCount(1, $datamap->getMapArray());

        $datamap->setMap('{"some-key-1": "some-value-1", "some-key-2": "some-value-2"}');
        $this->assertCount(2, $datamap->getMapArray());
    }

    /**
     * @expectedException \Exception
     */
    public function testGetMapArrayException()
    {
        $datamap = new Datamap();

        $datamap->setMap('{"some-key: "some-value"}');
        $this->assertCount(1, $datamap->getMapArray());
    }

    public function testGetTypeName()
    {
        $datamap = new Datamap();

        $datamap->setType(Datamap::TYPE_DATA);
        $this->assertEquals('Data', $datamap->getTypeName());

        $datamap->setType(Datamap::TYPE_SEARCH);
        $this->assertEquals('Search', $datamap->getTypeName());

        $datamap->setType(Datamap::TYPE_EMAIL);
        $this->assertEquals('Email', $datamap->getTypeName());

        $datamap->setType('test-string-1');
        $this->assertNull($datamap->getTypeName());

        $datamap->setType('test-string-2');
        $this->assertNull($datamap->getTypeName());
    }

    public function testValidateMap()
    {
        $validator = $this->container->get('validator');

        $datamap = new Datamap();

        $errors = $validator->validate($datamap);
        $this->assertEmpty($errors);

        $datamap->setMap('this is not a JSON string');
        $errors = $validator->validate($datamap);
        $this->assertCount(1, $errors);
        $this->assertContains('JSON parsing error', $errors[0]->getMessage());
    }
}