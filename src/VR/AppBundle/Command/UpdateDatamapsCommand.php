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
 * Class UpdateDatamapsCommand
 *
 * @package VR\AppBundle\Command
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class UpdateDatamapsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('datamaps:update')
            ->setDescription('Updates datamap from given instance.')
            ->addArgument('url', InputArgument::REQUIRED, 'URL of Message Handler instance');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        /** @var PluginManager $processManager */

        $output->writeln('Updating Datamaps...');

        $json = file_get_contents($input->getArgument('url') . '/datamap/list/json');

        if (!$json) {
            throw new \Exception('Wrong JSON response.');
        }

        $datamaps = json_decode($json, true);

        if (count($datamaps)) {
            foreach ($datamaps as $datamap) {
                $output->write('Updating map "' . $datamap['name'] . '... ');

                $localDatamap = $em->getRepository('VRAppBundle:Datamap')->findOneBy(['name' => $datamap['name']]);

                if (!$localDatamap) {
                    $localDatamap = new Datamap();
                    $localDatamap->setName($datamap['name']);
                }

                $localDatamap->setType($datamap['type']);
                $localDatamap->setMap(base64_decode($datamap['map']));
                $localDatamap->setDescription($datamap['description']);
                $em->persist($localDatamap);
                $em->flush();

                $output->writeln('<info>Done</info>');
            }
        }

        $output->writeln('Finished.');
    }
}