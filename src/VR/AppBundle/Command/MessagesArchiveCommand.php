<?php

namespace VR\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use VR\AppBundle\Entity\Datamap;
use VR\AppBundle\Plugin\PluginManager;
use VR\AppBundle\Entity\ProcessSchedule;

/**
 * Class MessagesArchiveCommand
 *
 * @package VR\AppBundle\Command
 *
 * @author Andrzej Prusinowski <andrzej@avris.it>
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class MessagesArchiveCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('messages:archive')
            ->setDescription('Deletes messages older than given number of months.')
            ->addArgument('timespan', InputArgument::OPTIONAL, 'How old messages should be archived (in months)', 6);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $since = new \DateTime('-' . $input->getArgument('timespan') . ' months');
        $output->writeln('Looking for finished messages older than ' . $since->format('Y-m-d'));

        /** @var \Doctrine\DBAL\Connection $conn */
        $conn = $this->getContainer()->get('doctrine')->getManager()->getConnection();

        $stmt = $conn->prepare(
            "DELETE FROM messages WHERE message_created <= :since AND message_status IN ('Finished', 'Cancelled')"
        );
        $stmt->bindValue('since', $since->format('Y-m-d H:i:s'));
        $stmt->execute();
        $messagesDeleted = $stmt->rowCount();

        $output->writeln("Deleted $messagesDeleted messages.");
   }
}