<?php

namespace VR\AppBundle\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use VR\AppBundle\Form\LogsFilterData;
use VR\AppBundle\Service\LogReader;

/**
 * Class LogReaderTest
 *
 * @package VR\AppBundle\Tests\Service
 *
 * @author Andrzej Prusinowski <andrzej@avris.it>
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class LogReaderTest extends WebTestCase
{
    /** @var LogReader */
    private $reader;

    protected $client;

    protected $container;

    protected function setUp()
    {
        $this->client = static::createClient();
        $this->container = $this->client->getContainer();

        $rootPath = $this->container->get('kernel')->getRootDir();

        $this->reader = new LogReader($rootPath . '/../tests/_data/logs');
    }

    public function testGetFileList()
    {
        $this->assertContains('test.log', $this->reader->getFileList());
    }

    public function testRead()
    {
        $filter = new LogsFilterData();
        $filter->filename = 'test.log';
        $filter->date = new \DateTime('2015-04-17');
        $this->assertCount(7, $this->reader->read($filter));

        $filter->type = 'doctrine.DEBUG';
        $this->assertCount(4, $this->reader->read($filter));

        $filter->type = 'doctrine.DEBUG';
        $this->assertCount(4, $this->reader->read($filter));

        $filter->search = 'process_schedules';
        $this->assertCount(2, $this->reader->read($filter));

        $filter->search = 'select'; // case insensitive
        $this->assertCount(1, $this->reader->read($filter));
    }
}