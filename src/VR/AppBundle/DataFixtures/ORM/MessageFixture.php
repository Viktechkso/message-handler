<?php
namespace VR\AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use VR\AppBundle\Entity\Message;
use VR\AppBundle\DataFixtures\FixturesOrdering;

/**
 * Message
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class MessageFixture extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Loads fixtures.
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $statuses = [
            'New',
            'In progress',
            'Rerun',
            'Error',
            'Finished',
            'Cancelled',
            'Halted'
        ];

        $this->generateWithJsonErrorInFlow($manager);
        $this->generateWithJsonErrorInFlowMessage($manager);

        for ($i = 1; $i <= 1500; $i++) {
            $message = new Message();
            $message->setFlowCreatedAt(new \DateTime());
            $message->setFlow(json_encode([
                1 => [
                    'Module' => 'Accounts',
                    'GUID' => uniqid(),
                    'Datamap' => 'ExampleDatamap',
                    'Status' => 'Done'
                ],
                2 => [
                    'Module' => 'Contacts',
                    'GUID' => uniqid(),
                    'Datamap' => 'ExampleDatamap',
                    'Status' => 'Error'
                ],
                3 => [
                    'Module' => 'Tasks',
                    'GUID' => uniqid(),
                    'Datamap' => 'ExampleDatamap',
                    'Status' => 'In progress'
                ],
                4 => [
                    'Module' => 'Relation',
                    'GUID' => '',
                    'SourceModule' => 'Accounts',
                    'DestinationModule' => 'Contacts',
                    'SourceStep' => 1,
                    'DestinationStep' => 2,
                    'Status' => 'New'
                ],
            ]));
            $message->setFlowStatus($statuses[$i % count($statuses)]);
            $message->setFlowMessage(json_encode([
                'some_key' => 'This is an example payload in JSON format.'
            ]));
            $message->setFlowName('example');
            $message->generateMd5();

            $manager->persist($message);
            $this->addReference('message-' . $i, $message);
        }

        $manager->flush();
    }

    public function generateWithJsonErrorInFlow($manager)
    {
        $message = new Message();
        $message->setFlowCreatedAt(new \DateTime());
        $message->setFlow('some text');
        $message->setFlowStatus('New');
        $message->setFlowMessage(json_encode([
            'some_key' => 'This is an example payload in JSON format.'
        ]));
        $message->setFlowName('example');

        $manager->persist($message);
    }

    public function generateWithJsonErrorInFlowMessage($manager)
    {
        $message = new Message();
        $message->setFlowCreatedAt(new \DateTime());
        $message->setFlow(json_encode([
            1 => [
                'Module' => 'Accounts',
                'GUID' => uniqid(),
                'Datamap' => 'ExampleDatamap',
                'Status' => 'Done'
            ],
            2 => [
                'Module' => 'Contacts',
                'GUID' => uniqid(),
                'Datamap' => 'ExampleDatamap',
                'Status' => 'Error'
            ],
            3 => [
                'Module' => 'Tasks',
                'GUID' => uniqid(),
                'Datamap' => 'ExampleDatamap',
                'Status' => 'In progress'
            ],
            4 => [
                'Module' => 'Relation',
                'GUID' => '',
                'SourceModule' => 'Accounts',
                'DestinationModule' => 'Contacts',
                'SourceStep' => 1,
                'DestinationStep' => 2,
                'Status' => 'New'
            ],
        ]));
        $message->setFlowStatus('New');
        $message->setFlowMessage('some text');
        $message->setFlowName('nne');

        $manager->persist($message);
    }

    /**
     * Returns fixtures class ordering.
     *
     * @return int
     */
    public function getOrder()
    {
        return FixturesOrdering::getOrdering('Message');
    }
}