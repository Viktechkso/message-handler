<?php

namespace VR\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use VR\AppBundle\Plugin\PluginManager;

/**
 * Class ProcessTestCommand
 *
 * @package VR\AppBundle\Command
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class ProcessTestCommand extends ContainerAwareCommand
{
    /** @var PluginManager */
    protected $pluginManager;

    protected function configure()
    {
        $this
            ->setName('process:test')
            ->setDescription('Runs selected process for test.')
            ->addArgument('shortcut', InputArgument::OPTIONAL, 'Process shortcut')
            ->addArgument('parameters', InputArgument::OPTIONAL, 'Process parameters (JSON)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $shortcut = $input->getArgument('shortcut');
        $parameters = $input->getArgument('parameters') ? json_decode($input->getArgument('parameters'), true) : [];

        $this->pluginManager = $this->getContainer()->get('vr.plugin_manager');

        $dialog = $this->getHelper('dialog');

        if (!$shortcut) {
            $output->writeln('List of available processes:');
            $output->writeln('');

            $output->writeln('<info>Collectors</info>');
            $rows = [];

            foreach ($this->pluginManager->getCollectorsRunModesList() as $shortcut => $name) {
                $rows[] = [$shortcut, $name];
            }

            $table = new Table($output);
            $table
                ->setHeaders(array('Shortcut', 'Name'))
                ->setRows($rows);
            $table->render();


            $output->writeln('<info>Workers</info>');
            $rows = [];

            foreach ($this->pluginManager->getWorkersRunModesList() as $shortcut => $name) {
                $rows[] = [$shortcut, $name];
            }

            $table = new Table($output);
            $table
                ->setHeaders(array('Shortcut', 'Name'))
                ->setRows($rows);
            $table->render();


            $shortcut = $dialog->ask(
                $output,
                'Please enter the shortcut of a process: ',
                '',
                array_keys($this->pluginManager->getAllRunModesList())
            );
        }

        if (!in_array($shortcut, array_keys($this->pluginManager->getAllRunModesList()))) {
            throw new \Exception('There is no process with shortcut = ' . $shortcut . '.');
        }

        if (!$parameters) {
            $parameters = $dialog->ask(
                $output,
                'Please enter the process parameters in JSON format: ',
                ''
            );

            $parameters = json_decode($parameters, true);
        }

        $output->writeln('Running process ' . $shortcut . '...');

        if ($parameters === null) {
            $parameters = [];
        }

        $this->pluginManager->runProcess($shortcut, $parameters);

        $output->writeln('Finished.');
    }
}