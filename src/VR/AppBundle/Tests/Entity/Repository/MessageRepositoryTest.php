<?php

namespace VR\AppBundle\Tests\Entity\Repository;

use Doctrine\ORM\Mapping\ClassMetadata;
use VR\AppBundle\Entity\Repository\MessageRepository;

/**
 * Class MessageRepositoryTest
 *
 * @package VR\AppBundle\Tests\Entity\Repository
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class MessageRepositoryTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

    public function getEntityManagerMock()
    {
        return $this->getMock('\Doctrine\ORM\EntityManager', [], [], '', false);
    }

    public function getRepository($em)
    {
        return new MessageRepository($em, new ClassMetadata('Message'));
    }

    public function testInsert()
    {
        $stmtMock = $this->getMock('\Doctrine\DBAL\Driver\Statement', [], [], '', false);
        $stmtMock->expects($this->once())->method('execute');

        $connectionMock = $this->getMock('\Doctrine\DBAL\Connection', [], [], '', false);
        $connectionMock->expects($this->once())->method('prepare')->willReturn($stmtMock);
        $connectionMock->expects($this->once())->method('lastInsertId')->willReturn(12345);

        $emMock = $this->getEntityManagerMock();
        $emMock->expects($this->exactly(2))->method('getConnection')->willReturn($connectionMock);

        $messageRepository = $this->getRepository($emMock);

        $id = $messageRepository->insert(
            'TestType',
            'New',
            json_encode([1 => ['Status' => 'New']]),
            json_encode(['key1' => 'value1', 'key2' => 'value2']),
            false,
            false
        );
    }
}