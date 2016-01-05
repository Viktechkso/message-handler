<?php

namespace VR\AppBundle\Tests\Entity;

use VR\AppBundle\Entity\ProcessQueue;

/**
 * Class ProcessQueueTest
 *
 * @package VR\AppBundle\Tests\Entity
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class ProcessQueueTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

    protected function getNewObject()
    {
        return new ProcessQueue([], new \DateTime(), '/some/path');
    }

    public function testCount()
    {
        $processQueue = $this->getNewObject();

        $this->assertEquals(0, $processQueue->count());
    }
}