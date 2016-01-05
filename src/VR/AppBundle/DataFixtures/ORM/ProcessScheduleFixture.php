<?php
namespace VR\AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use VR\AppBundle\Entity\ProcessSchedule;
use VR\AppBundle\DataFixtures\FixturesOrdering;
use VR\AppBundle\Plugin\PluginManager;

/**
 * Message
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class ProcessScheduleFixture extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Loads fixtures.
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $processSchedule = new ProcessSchedule();
        $processSchedule->setDayOfWeek(null);
        $processSchedule->setMonth(null);
        $processSchedule->setDayOfMonth(null);
        $processSchedule->setHour(null);
        $processSchedule->setMinute(null);
        $processSchedule->setType('collector.example.main');

        $manager->persist($processSchedule);
        $this->addReference('process-schedule-1', $processSchedule);


        $processSchedule = new ProcessSchedule();
        $processSchedule->setDayOfWeek(null);
        $processSchedule->setMonth(null);
        $processSchedule->setDayOfMonth(null);
        $processSchedule->setHour(15);
        $processSchedule->setMinute(10);
        $processSchedule->setType('worker.sugar_crm.main');

        $manager->persist($processSchedule);
        $this->addReference('process-schedule-2', $processSchedule);

        $manager->flush();
    }

    /**
     * Returns fixtures class ordering.
     *
     * @return int
     */
    public function getOrder()
    {
        return FixturesOrdering::getOrdering('ProcessSchedule');
    }
}