<?php

namespace VR\AppBundle\Tests\Plugin;

use VR\AppBundle\Plugin\PluginBundle;

/**
 * Class PluginBundleTest
 *
 * @package VR\AppBundle\Tests\Plugin
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class PluginBundleTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

    /**
     * @expectedException \Exception
     */
    public function testInitPlugin()
    {
        $bundle = new PluginBundle();

        $bundle->initPlugin();
    }

    public function testSetPluginType()
    {
        $bundle = new PluginBundle();

        $bundle->setPluginType('collector');
    }

//    public function testBoot()
//    {
//        $bundle = $this
//            ->getMockBuilder('PluginBundle')
//            ->setMethods(['boot'])
//            ->getMock();
//
//        $bundle->expects($this->once())->method('savePluginWorkspace')->willReturn(null);
//
//        $bundle->boot();
//    }
}