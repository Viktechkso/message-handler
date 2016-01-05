<?php

namespace VR\AppBundle\Tests\Entity;

use VR\AppBundle\Entity\Message;
use VR\AppBundle\Entity\StepChange;
use VR\AppBundle\Tests\SymfonyTestCase;

/**
 * Class StepChangeTest
 *
 * @package VR\AppBundle\Tests\Entity
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class StepChangeTest extends SymfonyTestCase
{
    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

    public function testGettersAndSetters()
    {
        $this->checkGetId(new StepChange());

        $this->checkGetterAndSetter(new StepChange(), 'changedAt', new \DateTime(), true);
        $this->checkGetterAndSetter(new StepChange(), 'message', new Message(), true);
        $this->checkGetterAndSetter(new StepChange(), 'messageStatusBefore', 'test-value', true);
        $this->checkGetterAndSetter(new StepChange(), 'messageStatusAfter', 'test-value', true);
        $this->checkGetterAndSetter(new StepChange(), 'stepNumber', 10, true);
    }
}