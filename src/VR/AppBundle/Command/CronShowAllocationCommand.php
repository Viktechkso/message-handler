<?php

namespace VR\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use VR\AppBundle\Plugin\PluginManager;
use VR\AppBundle\Entity\ProcessSchedule;
use VR\AppBundle\Entity\ProcessQueue;

/**
 * Class CronShowAllocationCommand
 *
 * @package VR\AppBundle\Command
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class CronShowAllocationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('cron:show-allocation')
            ->setDescription('Runs scheduled processes.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $numberOfThreads = CronRunCommand::NUMBER_OF_THREADS;

        for ($i = 1; $i <= $numberOfThreads; $i++) {
            $scheduledProcesses = $em->getRepository('VRAppBundle:ProcessSchedule')->findForCronTask($numberOfThreads, $i);
            $output->writeln('CRON ID: ' . $i . "\t" . 'Found ' . count($scheduledProcesses) . ' scheduled processes (' . $this->getIdsList($this->getIds($scheduledProcesses)) . ').');
        }
    }

    protected function getIdsList($ids)
    {
        return implode(', ', $ids);
    }

    protected function getIds($objects)
    {
        $ids = [];

        if (count($objects)) {
            foreach ($objects as $object) {
                $ids[] = $object->getId();
            }

        }

        return $ids;
    }
}