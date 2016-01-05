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
 * Class CronRunCommand
 *
 * @package VR\AppBundle\Command
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 * @author Andrzej Prusinowski <andrzej@avris.it>
 */
class CronRunCommand extends ContainerAwareCommand
{
    const NUMBER_OF_THREADS = 8;

    protected function configure()
    {
        $this
            ->setName('cron:run')
            ->setDescription('Runs scheduled processes.')
            ->addArgument('cronId', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $now = new \DateTime();
        $cronId = $input->getArgument('cronId');

        $em = $this->getContainer()->get('doctrine')->getManager();
        /** @var PluginManager $processManager */
        $processManager = $this->getContainer()->get('vr.plugin_manager');

        $scheduledProcesses = $em->getRepository('VRAppBundle:ProcessSchedule')
            ->findForCronTask(self::NUMBER_OF_THREADS, $cronId);
        $output->writeln('CRON ID: ' . $cronId . "\t" . 'Found ' . count($scheduledProcesses) . ' scheduled processes (' . $this->getIdsList($this->getIds($scheduledProcesses)) . ').');

        $queue = new ProcessQueue($scheduledProcesses, $now, $this->getContainer()->get('kernel')->getRootDir() . '/locks/');
        $output->writeln('CRON ID: ' . $cronId . "\t" . $queue->count() . ' processes queued.');

        while ($scheduledProcess = $queue->dequeue()) {
            $output->writeln('CRON ID: ' . $cronId . "\t" . 'Running ID = ' . $scheduledProcess->getId() . '... ');

            try {
                $scheduledProcess->setLastRunAt($now);
                $em->persist($scheduledProcess);
                $em->flush();

                $processManager->runScheduledProcess($scheduledProcess);

                $output->writeln('<info>Done</info>');
            } catch (\Exception $e) {
                $output->writeln('Encountered an error: ' . $e->getMessage());
                $this->getContainer()->get('logger')->error($e->getMessage());
            }

            $queue->unlock($scheduledProcess->getType());
            $output->writeln('CRON ID: ' . $cronId . "\t" . 'Finished ID = ' . $scheduledProcess->getId() . '... ');
        }

        $output->writeln('CRON ID: ' . $cronId . "\t" . 'Finished.');
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