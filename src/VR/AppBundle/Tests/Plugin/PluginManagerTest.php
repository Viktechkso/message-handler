<?php

namespace VR\AppBundle\Tests\Plugin;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use VR\AppBundle\Plugin\PluginManager;

/**
 * Class PluginManagerTest
 *
 * @package VR\AppBundle\Tests\Plugin
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class PluginManagerTest extends WebTestCase
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

    protected function getContainerMock()
    {
        return $this->getMock('\Symfony\Component\DependencyInjection\ContainerBuilder');
    }

    protected function getPluginManagerService()
    {
        return $this->container->get('vr.plugin_manager');
    }

    public function testGetAppConfiguration()
    {
        $containerMock = $this->getContainerMock();

        $manager = new PluginManager($this->getContainerMock());

        $this->assertNull($manager->getAppConfiguration());


    }
}