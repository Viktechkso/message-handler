<?php

namespace VR\DataMapperBundle\Tests\DataMapper;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class MuleDataMapperTest
 *
 * @package VR\DataMapperBundle\Tests\DataMapper
 *
 * @author Jimmie Louis Borch
 */
class MuleDataMapperTest extends WebTestCase
{
    protected $client;

    protected $container;

    protected function setUp()
    {
        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->container->enterScope('request');
    }

    public function testApplicationWorksWithFixture()
    {
        $service = $this->container->get('vr.data_mapper');
        $map = $service->loadMapFromString(file_get_contents(dirname(__DIR__).'/_data/payloads/mapping.json'));
        $payload = json_decode(file_get_contents(dirname(__DIR__).'/_data/payloads/payload.json'), true);

        $result = $service->run($map, $payload);

        $this->assertArrayHasKey('company', $result);
        $this->assertArrayHasKey('company_trade', $result);
        $this->assertArrayHasKey('company_finance', $result);
        $this->assertArrayHasKey('modified_date', $result);
//        $this->assertEquals('2014-12-24 20:00:00', $result['modified_date']); // disabled because of time diff problem on server
        $this->assertEquals('Hello World!', $result['test_target']);
        $this->assertEquals('Anpartsselskab København N', $result['test_concat']);
    }
}