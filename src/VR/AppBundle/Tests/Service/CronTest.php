<?php

namespace VR\AppBundle\Tests\Service;

use VR\AppBundle\Service\CronHelper;

/**
 * Class CronTest
 *
 * @package VR\AppBundle\Tests\Service
 *
 * @author Andrzej Prusinowski <andrzej@avris.it>
 */
class CronTest extends \PHPUnit_Framework_TestCase
{
    public function timeProvider()
    {
        return [
            ['*',       range(0,59)],
            [null,      range(0,59)],
            ['0',       [0]],
            ['1-10',    range(1,10)],
            ['3,5',     [3,5]],
            ['1-10/2',  [2,4,6,8,10]],
            ['*/5',     [0,5,10,15,20,25,30,35,40,45,50,55]],
            ['1,2/2',   [2]],
        ];
    }

    /**
     * @dataProvider timeProvider
     */
    public function testGetCronPossibilities($input, $correctOutput)
    {
        $this->assertEquals($correctOutput, array_values(CronHelper::getCronPossibilities($input)));
    }
}