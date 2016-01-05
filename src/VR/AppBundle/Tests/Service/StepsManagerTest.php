<?php

namespace VR\AppBundle\Tests\Service;

use VR\AppBundle\Service\StepsManager;

/**
 * Class StepsManagerTest
 *
 * @package VR\AppBundle\Tests\Service
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class StepsManagerTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

    public function testParse()
    {
        $input = json_encode(array(
            1 => array('Module' => 'Account', 'GUID' => '23e23q32rkrj23kj234lk23j4l23kj4', 'Datamap' => 'BF_MainOpportunity', 'Status' => 'Completed', 'ErrorID' => null),
            2 => array('Module' => 'Opportunity', 'GUID' => 'hgsdjkghksjdgnvfhsdmgrfjsgdj', 'Datamap' => 'BF_MainOpportunity', 'Status' => 'In progress', 'ErrorID' => null),
            3 => array('Module' => 'Relation', 'GUID' => '', 'SourceModule' => 'Opportunities', 'DestinationModule' => 'Opportunities', 'SourceStep' => 1, 'DestinationStep' => 2, 'Status' => 'New', 'ErrorID' => null),
        ));

        $parser = new StepsManager();
        $result = $parser->parse($input);

        $this->assertArrayHasKey('Module', $result[1]);
        $this->assertArrayHasKey('Module', $result[2]);
        $this->assertArrayHasKey('Module', $result[3]);
    }

    public function testGetCurrentStepNumber()
    {
        $input = json_encode(array(
            1 => array('Module' => 'Account', 'GUID' => '23e23q32rkrj23kj234lk23j4l23kj4', 'Datamap' => 'BF_MainOpportunity', 'Status' => 'Completed', 'ErrorID' => null),
            2 => array('Module' => 'Opportunity', 'GUID' => 'hgsdjkghksjdgnvfhsdmgrfjsgdj', 'Datamap' => 'BF_MainOpportunity', 'Status' => 'In progress', 'ErrorID' => null),
            3 => array('Module' => 'Relation', 'GUID' => '', 'SourceModule' => 'Opportunities', 'DestinationModule' => 'Opportunities', 'SourceStep' => 1, 'DestinationStep' => 2, 'Status' => 'New', 'ErrorID' => null),
        ));

        $parser = new StepsManager();
        $parser->parse($input);

        $this->assertEquals(3, $parser->getCurrentStepNumber());
    }

    public function testGetNextStepNumber()
    {
        $input = json_encode(array(
            1 => array('Module' => 'Account', 'GUID' => '23e23q32rkrj23kj234lk23j4l23kj4', 'Datamap' => 'BF_MainOpportunity', 'Status' => 'Completed', 'ErrorID' => null),
            2 => array('Module' => 'Opportunity', 'GUID' => 'hgsdjkghksjdgnvfhsdmgrfjsgdj', 'Datamap' => 'BF_MainOpportunity', 'Status' => 'In progress', 'ErrorID' => null),
            3 => array('Module' => 'Relation', 'GUID' => '', 'SourceModule' => 'Opportunities', 'DestinationModule' => 'Opportunities', 'SourceStep' => 1, 'DestinationStep' => 2, 'Status' => 'New', 'ErrorID' => null),
        ));

        $parser = new StepsManager();
        $parser->parse($input);

        $this->assertEquals(null, $parser->getNextStepNumber());
    }

    public function testUpdateStepGUID()
    {

    }
}