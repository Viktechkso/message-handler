<?php

namespace VR\AppBundle\Tests\Entity;

use VR\AppBundle\Entity\Error;
use VR\AppBundle\Entity\Message;
use VR\AppBundle\Tests\SymfonyTestCase;

/**
 * Class ErrorTest
 *
 * @package VR\AppBundle\Tests\Entity
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class ErrorTest extends SymfonyTestCase
{
    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

    public function testGettersAndSetters()
    {
        $this->checkGetId(new Error());

        $this->checkGetterAndSetter(new Error(), 'stepNo', 'test-value', true);
        $this->checkGetterAndSetter(new Error(), 'errorMessage', 'test-value', true);
        $this->checkGetterAndSetter(new Error(), 'entryAt', new \DateTime(), true);
        $this->checkGetterAndSetter(new Error(), 'errorPayload', 'test-value', true);
        $this->checkGetterAndSetter(new Error(), 'message', new Message(), true);
    }

    public function testGetErrorPayloadIds()
    {
        $error = new Error();

        $error->setErrorPayload(json_encode([
            'module' => 'TestModule',
            'ids' => [
                ['id' => 12345],
                ['id' => 54321],
            ]
        ]));

        $ids = $error->getErrorPayloadIds();

        $this->assertCount(2, $ids);
        $this->assertContains(12345, $ids);
        $this->assertContains(54321, $ids);
    }

    public function testGetErrorPayloadModule()
    {
        $error = new Error();

        $error->setErrorPayload(json_encode([
            'module' => 'TestModule',
            'ids' => [
                ['id' => 12345],
                ['id' => 54321],
            ]
        ]));

        $this->assertEquals('TestModule', $error->getErrorPayloadModule());
    }
}