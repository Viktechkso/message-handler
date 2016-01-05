<?php

namespace VR\AppBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use VR\AppBundle\Plugin\PluginManager;
use VR\AppBundle\Entity\ProcessSchedule;
use VR\SugarCRMWorkerBundle\Service\Worker;

/**
 * Class CronRunForcedCommand
 *
 * @package VR\AppBundle\Command
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class CronRunForcedCommand extends ContainerAwareCommand
{
    /** @var EntityManager */
    protected $em;

    /** @var Worker */
    protected $worker;

    protected function configure()
    {
        $this
            ->setName('cron:run-forced')
            ->setDescription('Runs scheduled processes.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine')->getManager();
        $this->worker = $this->getContainer()->get('vr.worker.main');

        $output->writeln('Checking lock file...');
        if ($this->checkLockFile()) {
            $output->writeln('Lock file exists. Process cannot continue. Bye.');

            return false;
        }

        $output->writeln('Creating lock file...');
        $this->createLockFile();

        # to prevent parsing one message by multiple CRON jobs,
        # we have to search for the message on every iteration,
        # not on the beginning of Command

        while ($forcedMessage = $this->em->getRepository('VRAppBundle:Message')->getOneUnfinishedForced()) {
            $output->write('Parsing message with ID = ' . $forcedMessage->getId() . ' and type "' . $forcedMessage->getFlowName() . '"... ');

            # before actual parse - to prevent this message to be parsed by another CRON job
            $forcedMessage->setForced(false);
            $this->em->persist($forcedMessage);
            $this->em->flush();

            $this->worker->parseMessage($forcedMessage);

            $output->writeln('<info>Done</info>');
        }

        $output->writeln('Removing lock file...');
        $this->removeLockFile();
        $output->writeln('Finished.');
    }

    protected function createLockFile()
    {
        $now = new \DateTime();

        file_put_contents($this->getLockFilePath(), $now->format('Y-m-d H:i:s'));
    }

    protected function removeLockFile()
    {
        return unlink($this->getLockFilePath());
    }

    protected function checkLockFile()
    {
        return file_exists($this->getLockFilePath());
    }

    protected function getLockFilePath()
    {
        return $path = $this->getContainer()->get('kernel')->getRootDir() . '/locks/forced';
    }
}