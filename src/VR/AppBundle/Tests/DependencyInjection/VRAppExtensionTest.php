<?php
namespace VR\AppBundle\Tests\DependencyInjection;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use VR\AppBundle\DependencyInjection\VRAppExtension;

/**
 * Class VRAppExtensionTest
 *
 * @package VR\AppBundle\Tests\DependencyInjection
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class VRAppExtensionTest extends WebTestCase
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

    public function testLoad()
    {
        $extension = new VRAppExtension();



        $this->assertTrue($this->container->hasParameter('vr_app'));

//        $extension->load([], new ContainerBuilder());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testLoadException()
    {
        $extension = new VRAppExtension();
        $extension->load([], new ContainerBuilder());
    }
}