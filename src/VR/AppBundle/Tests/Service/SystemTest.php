<?php

namespace VR\AppBundle\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class SystemTest
 *
 * @package VR\AppBundle\Tests\Service
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class SystemTest extends WebTestCase
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

    public function testIsProcessRunningByPid()
    {
        $helper = $this->container->get('vr.system');

        $this->assertFalse($helper->isProcessRunningByPid(123456789));
        $this->assertTrue($helper->isProcessRunningByPid(getmypid()));
    }
}