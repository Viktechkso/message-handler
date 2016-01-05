<?php
namespace VR\AppBundle\Tests\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use VR\AppBundle\DependencyInjection\Configuration;

/**
 * Class ConfigurationTest
 *
 * @package VR\AppBundle\Tests\DependencyInjection
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

    public function testGetConfigTreeBuilder()
    {
        $configuration = new Configuration();

        $this->assertInstanceOf(get_class(new TreeBuilder()), $configuration->getConfigTreeBuilder());
    }
}