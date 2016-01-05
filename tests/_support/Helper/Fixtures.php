<?php

namespace Helper;

use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as DataFixturesLoader;
use InvalidArgumentException;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

class Fixtures extends \Codeception\Module
{
    /**
     * Loads data fixtures (ported from LoadDataFixturesDoctrineCommand class)
     *
     * @throws \Codeception\Exception\Module
     */
    public function reloadFixtures()
    {
        $container = $this->getModule('Symfony2')->container;
        $kernel = $this->getModule('Symfony2')->kernel;

        $doctrine = $container->get('doctrine');
        $em = $doctrine->getManager();

        $paths = array();
        foreach ($kernel->getBundles() as $bundle) {
            $paths[] = $bundle->getPath().'/DataFixtures/ORM';
        }

        $loader = new DataFixturesLoader($container);
        foreach ($paths as $path) {
            if (is_dir($path)) {
                $loader->loadFromDirectory($path);
            }
        }
        $fixtures = $loader->getFixtures();
        if (!$fixtures) {
            throw new InvalidArgumentException(
                sprintf('Could not find any fixtures to load in: %s', "\n\n- ".implode("\n- ", $paths))
            );
        }
        $purger = new ORMPurger($em);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_DELETE);
        $executor = new ORMExecutor($em, $purger);
        $executor->execute($fixtures, false);
    }
}