<?php

namespace VR\AppBundle\Tests\Entity;

use VR\AppBundle\Entity\ProcessSchedule;
use VR\AppBundle\Tests\SymfonyTestCase;

/**
 * Class ProcessScheduleTest
 *
 * @package VR\AppBundle\Tests\Entity
 *
 * @author Michał Jabłoński <mjapko@gmail.com>s
 */
class ProcessScheduleTest extends SymfonyTestCase
{
    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

    public function testGettersAndSetters()
    {
        $this->checkGetId(new ProcessSchedule());

        $this->checkGetterAndSetter(new ProcessSchedule(), 'dayOfWeek', 10, true);
        $this->checkGetterAndSetter(new ProcessSchedule(), 'month', 10, true);
        $this->checkGetterAndSetter(new ProcessSchedule(), 'dayOfMonth', 10, true);
        $this->checkGetterAndSetter(new ProcessSchedule(), 'hour', 10, true);
        $this->checkGetterAndSetter(new ProcessSchedule(), 'minute', 10, true);
        $this->checkGetterAndSetter(new ProcessSchedule(), 'type', 'test-value', true);
        $this->checkGetterAndSetter(new ProcessSchedule(), 'parameters', 'test-value', true);
        $this->checkGetterAndSetter(new ProcessSchedule(), 'description', 'test-value', true);
        $this->checkGetterAndSetter(new ProcessSchedule(), 'createdAt', new \DateTime(), true);
        $this->checkGetterAndSetter(new ProcessSchedule(), 'lastRunAt', new \DateTime(), true);
        $this->checkGetterAndSetter(new ProcessSchedule(), 'enabled', true, true);
    }

    public function testEnableDisable()
    {
        $processSchedule = new ProcessSchedule();

        $this->assertTrue($processSchedule->getEnabled());

        $processSchedule->disable();
        $this->assertFalse($processSchedule->getEnabled());

        $processSchedule->enable();
        $this->assertTrue($processSchedule->getEnabled());
    }

    public function testGetTime()
    {
        $processSchedule = new ProcessSchedule();

        $this->assertEquals('* * * * *', $processSchedule->getTime());

        $processSchedule->setMinute(2);
        $processSchedule->setHour(3);
        $processSchedule->setDayOfMonth(4);
        $processSchedule->setMonth(5);
        $processSchedule->setDayOfWeek(6);

        $this->assertEquals('2 3 4 5 6', $processSchedule->getTime());
    }

    public function testGetParametersArray()
    {
        $processSchedule = new ProcessSchedule();

        $this->assertInternalType('array', $processSchedule->getParametersArray());

        $processSchedule->setParameters(json_encode([
            'some-key-1' => 'some-value-1',
            'some-key-2' => 'some-value-2'
        ]));

        $this->assertCount(2, $processSchedule->getParametersArray());
        $this->assertContains('some-value-1', $processSchedule->getParametersArray());
        $this->assertContains('some-value-2', $processSchedule->getParametersArray());

        # for SQL types:
//        $sql = 'SELECT somefield FROM sometable;';
//
//        $processSchedule->setType('collector.sql.main');
//        $processSchedule->setParameters($sql);
//
//        $this->assertInternalType('array', $processSchedule->getParametersArray());
//        $this->assertArrayHasKey('sql', $processSchedule->getParametersArray());
//        $this->assertEquals($sql, $processSchedule->getParametersArray()['sql']);
    }
}