<?php

namespace VR\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use VR\AppBundle\Plugin\PluginManager;
use VR\AppBundle\Entity\ProcessSchedule;
use VR\AppBundle\Entity\ProcessQueue;

/**
 * Class CronClearLocksCommand
 *
 * @package VR\AppBundle\Command
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class CronClearLocksCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('cron:clear-locks')
            ->setDescription('Clears all locks.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write('Removing locks... ');

        $counter = $this->removeLockFiles();

        $output->writeln('<info>Done, removed ' . $counter . ' files.</info>');
    }

    protected function removeLockFiles()
    {
        $counter = 0;

        $lockFilesDirectory = $this->getContainer()->get('kernel')->getRootDir() . '/locks/';

        $finder = new Finder();
        $finder->files()->in($lockFilesDirectory);

        if (count($finder)) {
            foreach ($finder as $file) {
                unlink($file->getRealpath());

                $counter++;
            }
        }

        return $counter;
    }
}