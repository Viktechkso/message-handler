<?php

namespace VR\AppBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use VR\AppBundle\Entity\Error;
use VR\AppBundle\Entity\Message;
use VR\AppBundle\Entity\StepChange;
use VR\AppBundle\Tests\SymfonyTestCase;

/**
 * Class MessageTest
 *
 * @package VR\AppBundle\Tests\Entity
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class MessageTest extends SymfonyTestCase
{
    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

    public function testResetGuids()
    {
        $message = new Message();
        $message->setFlow(json_encode([
            1 => array('Module' => 'Account', 'GUID' => 'guid-step-1', 'Datamap' => 'example_datamap', 'Status' => 'Done', 'ErrorID' => null),
            2 => array('Module' => 'Opportunity', 'GUID' => 'guid-step-2', 'Datamap' => 'example_datamap', 'Status' => 'Error', 'ErrorID' => null),
            3 => array('Module' => 'Opportunity', 'GUID' => 'guid-step-3', 'Datamap' => 'example_datamap', 'Status' => 'In progress', 'ErrorID' => null),
            4 => array('Module' => 'Relation', 'GUID' => '', 'SourceModule' => 'Opportunities', 'DestinationModule' => 'Opportunities', 'SourceStep' => 1, 'DestinationStep' => 2, 'Status' => 'New', 'ErrorID' => null),
        ]));

        $steps = json_decode($message->getFlow(), true);
        $this->assertEquals('guid-step-1', $steps[1]['GUID']);

        $message->resetGuids();

        $steps = json_decode($message->getFlow(), true);
        $this->assertEquals('', $steps[1]['GUID']);
        $this->assertEquals('', $steps[2]['GUID']);
        $this->assertEquals('', $steps[3]['GUID']);
        $this->assertEquals('', $steps[4]['GUID']);
    }

    public function testGenerateMd5()
    {
        $message = new Message();
        $message->generateMd5();

        $this->assertEquals('d41d8cd98f00b204e9800998ecf8427e', $message->getMd5());

        $message->setFlow('test flow');
        $message->setFlowMessage('test flow message');
        $message->setFlowName('test flow name');

        $this->assertEquals('d41d8cd98f00b204e9800998ecf8427e', $message->getMd5());

        $message->setFlow('another flow');

        $this->assertEquals('d41d8cd98f00b204e9800998ecf8427e', $message->getMd5());
    }

    public function testGetSetForced()
    {
        $message = new Message();

        $this->assertFalse($message->getForced());

        $message->setForced(true);

        $this->assertTrue($message->getForced());
    }

    public function testGetSetRunAt()
    {
        $message = new Message();

        $this->assertNull($message->getRunAt());

        $message->setRunAt(new \DateTime());

        $this->assertNotNull($message->getRunAt());
        $this->assertInstanceOf(get_class(new \DateTime()), $message->getRunAt());
    }

    public function testIsStatus()
    {
        $message = new Message();

        $this->assertTrue($message->isStatus(null));

        $message->setFlowStatus('New');
        $this->assertTrue($message->isStatus('New'));
        $this->assertTrue($message->isStatus('new'));

        $message->setFlowStatus('Finished');
        $this->assertTrue($message->isStatus('Finished'));
        $this->assertTrue($message->isStatus('finished'));
    }

    public function testGetFlowStatusCssClass()
    {
        $message = new Message();

        # known statuses
        $message->setFlowStatus('Error');
        $this->assertEquals('label-danger', $message->getFlowStatusCssClass());

        $message->setFlowStatus('In progress');
        $this->assertEquals('label-primary', $message->getFlowStatusCssClass());

        $message->setFlowStatus('Halted');
        $this->assertEquals('label-warning', $message->getFlowStatusCssClass());

        # other statuses (random strings)
        $message->setFlowStatus('Finished');
        $this->assertEquals('label-default', $message->getFlowStatusCssClass());

        $message->setFlowStatus('Test status');
        $this->assertEquals('label-default', $message->getFlowStatusCssClass());
    }

    public function testErrorCollection()
    {
        $message = new Message();

        $this->assertInstanceOf(get_class(new ArrayCollection()), $message->getErrors());
        $this->assertEmpty($message->getErrors());

        $error1 = new Error();
        $message->addError($error1);
        $this->assertCount(1, $message->getErrors());

        $error2 = new Error();
        $message->addError($error2);
        $this->assertCount(2, $message->getErrors());

        $message->removeError($error1);
        $this->assertCount(1, $message->getErrors());
    }

    public function testGetFlowCreatedAt()
    {
        $message = new Message();

        $this->assertInstanceOf(get_class(new \DateTime()), $message->getFlowCreatedAt());
    }

    public function testStepChangesCollection()
    {
        $message = new Message();

        $this->assertInstanceOf(get_class(new ArrayCollection()), $message->getStepChanges());
        $this->assertEmpty($message->getStepChanges());

        $stepChange1 = new StepChange();
        $message->addStepChange($stepChange1);
        $this->assertCount(1, $message->getStepChanges());

        $stepChange2 = new StepChange();
        $message->addStepChange($stepChange2);
        $this->assertCount(2, $message->getStepChanges());

        $message->removeStepChange($stepChange1);
        $this->assertCount(1, $message->getStepChanges());
    }

    public function testGetSetFlowName()
    {
        $message = new Message();

        $this->assertNull($message->getFlowName());

        $message->setFlowName('test1');

        $this->assertNotNull($message->getFlowName());
    }

    public function testGetSetFlowMessage()
    {
        $message = new Message();

        $this->assertNull($message->getFlowMessage());

        $message->setFlowMessage('test1');

        $this->assertNotNull($message->getFlowMessage());
    }

    public function testGetSetFlowStatus()
    {
        $message = new Message();

        $this->assertNull($message->getFlowStatus());

        $message->setFlowStatus('test1');

        $this->assertNotNull($message->getFlowStatus());
    }

    public function testGetCurrentStepNumber()
    {
        # 1
        $message = new Message();
        $this->assertFalse($message->getCurrentStepNumber());

        # 2
        $message = new Message();
        $message->setFlow(json_encode([
            1 => array('Module' => 'Account', 'GUID' => 'guid-step-1', 'Datamap' => 'example_datamap', 'Status' => 'Done', 'ErrorID' => null),
            2 => array('Module' => 'Opportunity', 'GUID' => 'guid-step-2', 'Datamap' => 'example_datamap', 'Status' => 'Error', 'ErrorID' => null),
            3 => array('Module' => 'Opportunity', 'GUID' => 'guid-step-3', 'Datamap' => 'example_datamap', 'Status' => 'In progress', 'ErrorID' => null),
            4 => array('Module' => 'Relation', 'GUID' => '', 'SourceModule' => 'Opportunities', 'DestinationModule' => 'Opportunities', 'SourceStep' => 1, 'DestinationStep' => 2, 'Status' => 'New', 'ErrorID' => null),
        ]));

        $this->assertEquals(4, $message->getCurrentStepNumber());

        # 3
        $message = new Message();
        $message->setFlow(json_encode([
            1 => array('Module' => 'Account', 'GUID' => 'guid-step-1', 'Datamap' => 'example_datamap', 'Status' => 'Done', 'ErrorID' => null),
            2 => array('Module' => 'Opportunity', 'GUID' => 'guid-step-2', 'Datamap' => 'example_datamap', 'Status' => 'Error', 'ErrorID' => null),
            3 => array('Module' => 'Opportunity', 'GUID' => '', 'Datamap' => 'example_datamap', 'Status' => 'In progress', 'ErrorID' => null),
            4 => array('Module' => 'Relation', 'GUID' => '', 'SourceModule' => 'Opportunities', 'DestinationModule' => 'Opportunities', 'SourceStep' => 1, 'DestinationStep' => 2, 'Status' => 'New', 'ErrorID' => null),
        ]));

        $this->assertEquals(3, $message->getCurrentStepNumber());

        # 4
        $message = new Message();
        $message->setFlow(json_encode([
            1 => array('Module' => 'Account', 'GUID' => 'guid-step-1', 'Datamap' => 'example_datamap', 'Status' => 'Done', 'ErrorID' => null),
            2 => array('Module' => 'Opportunity', 'GUID' => 'guid-step-2', 'Datamap' => 'example_datamap', 'Status' => 'Error', 'ErrorID' => null),
            3 => array('Module' => 'Opportunity', 'GUID' => 'guid-step-3', 'Datamap' => 'example_datamap', 'Status' => 'In progress', 'ErrorID' => null),
            4 => array('Module' => 'Relation', 'GUID' => 'guid-step-4', 'SourceModule' => 'Opportunities', 'DestinationModule' => 'Opportunities', 'SourceStep' => 1, 'DestinationStep' => 2, 'Status' => 'New', 'ErrorID' => null),
        ]));

        $this->assertFalse($message->getCurrentStepNumber());
    }

    public function testIsActionAllowed()
    {
        $message = new Message();

        $this->assertTrue($message->isActionAllowed('run'));
        $this->assertTrue($message->isActionAllowed('new'));
        $this->assertTrue($message->isActionAllowed('halt'));
        $this->assertTrue($message->isActionAllowed('cancel'));
        $this->assertTrue($message->isActionAllowed('reset_guids'));

        $message->setFlowStatus('in progress');
        $this->assertFalse($message->isActionAllowed('run'));
        $this->assertTrue($message->isActionAllowed('new'));
        $this->assertTrue($message->isActionAllowed('halt'));
        $this->assertTrue($message->isActionAllowed('cancel'));
        $this->assertTrue($message->isActionAllowed('reset_guids'));

        $message->setFlowStatus('finished');
        $this->assertFalse($message->isActionAllowed('run'));
        $this->assertFalse($message->isActionAllowed('new'));
        $this->assertFalse($message->isActionAllowed('halt'));
        $this->assertFalse($message->isActionAllowed('cancel'));
        $this->assertFalse($message->isActionAllowed('reset_guids'));

        $message->setFlowStatus('cancelled');
        $this->assertFalse($message->isActionAllowed('run'));
        $this->assertTrue($message->isActionAllowed('new'));
        $this->assertFalse($message->isActionAllowed('halt'));
        $this->assertFalse($message->isActionAllowed('cancel'));
        $this->assertTrue($message->isActionAllowed('reset_guids'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testIsActionAllowedException()
    {
        $message = new Message();
        $message->isActionAllowed('test-action');
    }

    public function testChangeStepParameter()
    {
        $message = new Message();
        $message->setFlow(json_encode([
            1 => array('Module' => 'Account', 'GUID' => 'guid-step-1', 'Datamap' => 'example_datamap', 'Status' => 'Done', 'ErrorID' => null),
            2 => array('Module' => 'Opportunity', 'GUID' => 'guid-step-2', 'Datamap' => 'example_datamap', 'Status' => 'Done', 'ErrorID' => null),
            3 => array('Module' => 'Opportunity', 'GUID' => 'guid-step-3', 'Datamap' => 'example_datamap', 'Status' => 'In progress', 'ErrorID' => null),
            4 => array('Module' => 'Relation', 'GUID' => '', 'SourceModule' => 'Opportunities', 'DestinationModule' => 'Opportunities', 'SourceStep' => 1, 'DestinationStep' => 2, 'Status' => 'New', 'ErrorID' => null),
        ]));

        $steps = $message->getStepsArray();

        $this->assertEquals('guid-step-1', $steps[1]['GUID']);
        $this->assertEquals('guid-step-2', $steps[2]['GUID']);
        $this->assertEquals('guid-step-3', $steps[3]['GUID']);
        $this->assertEquals('', $steps[4]['GUID']);

        $message->changeStepParameter(2, 'GUID', 'new-guid-step-2');

        $steps = $message->getStepsArray();

        $this->assertEquals('guid-step-1', $steps[1]['GUID']);
        $this->assertEquals('new-guid-step-2', $steps[2]['GUID']);
        $this->assertEquals('guid-step-3', $steps[3]['GUID']);
        $this->assertEquals('', $steps[4]['GUID']);
    }

    /**
     * @expectedException \Exception
     */
    public function testChangeStepParameterException()
    {
        $message = new Message();
        $message->changeStepParameter(20, 'GUID', 'new-guid');
    }

    public function testChangeStepStatus()
    {
        $message = new Message();
        $message->setFlow(json_encode([
            1 => array('Module' => 'Account', 'GUID' => 'guid-step-1', 'Datamap' => 'example_datamap', 'Status' => 'Done', 'ErrorID' => null),
            2 => array('Module' => 'Opportunity', 'GUID' => 'guid-step-2', 'Datamap' => 'example_datamap', 'Status' => 'Done', 'ErrorID' => null),
            3 => array('Module' => 'Opportunity', 'GUID' => 'guid-step-3', 'Datamap' => 'example_datamap', 'Status' => 'In progress', 'ErrorID' => null),
            4 => array('Module' => 'Relation', 'GUID' => '', 'SourceModule' => 'Opportunities', 'DestinationModule' => 'Opportunities', 'SourceStep' => 1, 'DestinationStep' => 2, 'Status' => 'New', 'ErrorID' => null),
        ]));

        $steps = $message->getStepsArray();

        $this->assertEquals('Done', $steps[1]['Status']);
        $this->assertEquals('Done', $steps[2]['Status']);
        $this->assertEquals('In progress', $steps[3]['Status']);
        $this->assertEquals('New', $steps[4]['Status']);

        $message->changeStepStatus(2, 'Error');

        $steps = $message->getStepsArray();

        $this->assertEquals('Done', $steps[1]['Status']);
        $this->assertEquals('Error', $steps[2]['Status']);
        $this->assertEquals('In progress', $steps[3]['Status']);
        $this->assertEquals('New', $steps[4]['Status']);
    }

    public function testBatchChangeStepStatuses()
    {
        $message = new Message();
        $message->setFlow(json_encode([
            1 => array('Module' => 'Account', 'GUID' => 'guid-step-1', 'Datamap' => 'example_datamap', 'Status' => 'Done', 'ErrorID' => null),
            2 => array('Module' => 'Opportunity', 'GUID' => 'guid-step-2', 'Datamap' => 'example_datamap', 'Status' => 'Done', 'ErrorID' => null),
            3 => array('Module' => 'Opportunity', 'GUID' => 'guid-step-3', 'Datamap' => 'example_datamap', 'Status' => 'In progress', 'ErrorID' => null),
            4 => array('Module' => 'Relation', 'GUID' => '', 'SourceModule' => 'Opportunities', 'DestinationModule' => 'Opportunities', 'SourceStep' => 1, 'DestinationStep' => 2, 'Status' => 'New', 'ErrorID' => null),
        ]));

        $message->batchChangeStepStatuses('Done', 'New');

        $steps = $message->getStepsArray();

        $this->assertEquals('New', $steps[1]['Status']);
        $this->assertEquals('New', $steps[2]['Status']);
        $this->assertEquals('In progress', $steps[3]['Status']);
        $this->assertEquals('New', $steps[4]['Status']);
    }

    public function testGetErrorsForStep()
    {
        $message = new Message();

        $this->assertInstanceOf(get_class(new ArrayCollection()), $message->getErrorsForStep(1));
        $this->assertEmpty($message->getErrorsForStep(1));
        $this->assertEmpty($message->getErrorsForStep(2));

        $error1 = new Error();
        $error1->setStepNo(1);
        $message->addError($error1);

        $this->assertInstanceOf(get_class(new ArrayCollection()), $message->getErrorsForStep(2));
        $this->assertCount(1, $message->getErrorsForStep(1));
        $this->assertEmpty($message->getErrorsForStep(2));

        $error2 = new Error();
        $error2->setStepNo(1);
        $message->addError($error2);

        $this->assertInstanceOf(get_class(new ArrayCollection()), $message->getErrorsForStep(3));
        $this->assertCount(2, $message->getErrorsForStep(1));
        $this->assertEmpty($message->getErrorsForStep(2));

        $error3 = new Error();
        $error3->setStepNo(2);
        $message->addError($error3);

        $this->assertInstanceOf(get_class(new ArrayCollection()), $message->getErrorsForStep(4));
        $this->assertCount(2, $message->getErrorsForStep(1));
        $this->assertCount(1, $message->getErrorsForStep(2));
    }

    public function testGetStepsArray()
    {
        $message = new Message();

        $this->assertNull($message->getStepsArray());

        $message->setFlow(json_encode([
            1 => array('Module' => 'Account', 'GUID' => 'guid-step-1', 'Datamap' => 'example_datamap', 'Status' => 'Done', 'ErrorID' => null),
            2 => array('Module' => 'Opportunity', 'GUID' => 'guid-step-2', 'Datamap' => 'example_datamap', 'Status' => 'Done', 'ErrorID' => null),
            3 => array('Module' => 'Opportunity', 'GUID' => 'guid-step-3', 'Datamap' => 'example_datamap', 'Status' => 'In progress', 'ErrorID' => null),
            4 => array('Module' => 'Relation', 'GUID' => '', 'SourceModule' => 'Opportunities', 'DestinationModule' => 'Opportunities', 'SourceStep' => 1, 'DestinationStep' => 2, 'Status' => 'New', 'ErrorID' => null),
        ]));

        $this->assertCount(4, $message->getStepsArray());
    }

    /**
     * @expectedException \Exception
     */
    public function testGetStepsArrayException()
    {
        $message = new Message();
        $message->setFlow('{"example flow: ""}');
        $message->getStepsArray();
    }

    public function testGetPrettyFlow()
    {
        $message = new Message();

        $message->setFlow('{"example flow: ""}');
        $this->assertNull($message->getPrettyFlow());

        $steps = [
            1 => array('Module' => 'Account', 'GUID' => 'guid-step-1', 'Datamap' => 'example_datamap', 'Status' => 'Done'),
            2 => array('Module' => 'Opportunity', 'GUID' => 'guid-step-2', 'Datamap' => 'example_datamap', 'Status' => 'Done'),
        ];

        $message->setFlow(json_encode($steps));
        $this->assertEquals(json_encode($steps, JSON_PRETTY_PRINT), $message->getPrettyFlow());
    }

    public function testGetPrettyFlowMessage()
    {
        $message = new Message();

        $message->setFlowMessage('{"example flow: ""}');
        $this->assertNull($message->getPrettyFlowMessage());

        $flowMessage = [
            'a' => array('some-key-1' => 'some-value-1'),
            'b' => array('some-key-1' => 'some-value-2'),
        ];

        $message->setFlowMessage(json_encode($flowMessage));
        $this->assertEquals(json_encode($flowMessage, JSON_PRETTY_PRINT), $message->getPrettyFlowMessage());
    }

    public function testGetPayloadArray()
    {
        $message = new Message();

        $this->assertNull($message->getPayloadArray());

        $flowMessage = [
            'a' => array('some-key-1' => 'some-value-1'),
            'b' => array('some-key-1' => 'some-value-2'),
        ];

        $message->setFlowMessage(json_encode($flowMessage));

        $this->assertCount(2, $message->getPayloadArray());
    }

    public function testGetId()
    {
        $message = new Message();

        $this->checkGetId($message);
    }
}