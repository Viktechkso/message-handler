<?php

namespace VR\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

/**
 * Class CronTestCommand
 *
 * @package VR\AppBundle\Command
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class CronTestCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('cron:test')
            ->setDescription('Runs scheduled processes for test.')
            ->addArgument('id', InputArgument::OPTIONAL, 'ProcessSchedule');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $processManager = $this->getContainer()->get('vr.plugin_manager');

        $dialog = $this->getHelper('dialog');

        $id = $input->getArgument('id');

        if ($id) {
            $scheduledProcess = $em->getRepository('VRAppBundle:ProcessSchedule')->find($id);

            if ($scheduledProcess) {
                $output->writeln('Testing schedule (ID: ' . $scheduledProcess->getId() . ', type: "' . $scheduledProcess->getTypeName() . '")...');
            } else {
                throw new \Exception('Cannot find ProcessSchedule with given ID in the database.');
            }
        } else {
            $scheduledProcesses = $em->getRepository('VRAppBundle:ProcessSchedule')->findAll();

            $rows = [];
            $ids = [];

            foreach ($scheduledProcesses as $scheduledProcess) {
                $rows[] = [$scheduledProcess->getId(), $scheduledProcess->getTime(), $scheduledProcess->getType()];
                $ids[] = $scheduledProcess->getId();
            }

            $table = new Table($output);
            $table
                ->setHeaders(array('ID', 'Time', 'Type'))
                ->setRows($rows);
            $table->render();

            $id = $dialog->ask(
                $output,
                'Please enter the shortcut of a process: ',
                '',
                $ids
            );

            $scheduledProcess = $em->getRepository('VRAppBundle:ProcessSchedule')->find($id);
        }

        $processManager->runScheduledProcess($scheduledProcess);

        $output->writeln('Finished.');
    }
}