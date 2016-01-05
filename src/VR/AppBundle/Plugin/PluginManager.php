<?php

namespace VR\AppBundle\Plugin;

use VR\AppBundle\Entity\ProcessSchedule;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class PluginManager
 *
 * @package VR\AppBundle\Plugin
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 * @author Andrzej Prusinowski <andrzej@avris.it>
 */
class PluginManager
{
    public static $runModeNames = [];

    /** @var Container */
    protected $container;

    protected $collectors;

    protected $workers;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getAppConfiguration($node = null)
    {
        $configuration = $this->container->getParameter('vr_app');

        return $node ? $configuration[$node] : $configuration;
    }

    public function getPluginsConfiguration($node = null)
    {
        $configuration = $this->getAppConfiguration('plugins');

        return $node ? $configuration[$node] : $configuration;
    }

    public function runScheduledProcess(ProcessSchedule $processSchedule)
    {
        $this->runProcess($processSchedule->getType(), $processSchedule->getParametersArray());
    }

    public function runProcess($shortcut, $parameters = [])
    {
        $runParameters = explode('.', $shortcut);

        $type = $runParameters[0];
        $plugin = $runParameters[1];
        $runMode = $runParameters[2];

        $this->container->get('plugin.' . $type . '.' . $plugin . '.bootstrap')->run($runMode, $parameters);
    }

    public function addCollector($shortcut, $workspace)
    {
        $this->validatePluginWorkspace($workspace);

        $this->collectors[$shortcut] = $workspace;

        if (count($workspace['modes'])) {
            foreach ($workspace['modes'] as $shortcut => $name) {
                self::$runModeNames['collector.' . $workspace['shortcut'] . '.' . $shortcut] = $name;
            }
        }
    }

    public function addWorker($shortcut, $workspace)
    {
        $this->validatePluginWorkspace($workspace);

        $this->workers[$shortcut] = $workspace;

        if (count($workspace['modes'])) {
            foreach ($workspace['modes'] as $shortcut => $name) {
                self::$runModeNames['worker.' . $workspace['shortcut'] . '.' . $shortcut] = $name;
            }
        }
    }

    protected function validatePluginWorkspace($workspace)
    {
        //@todo
    }

    public function getCollectorsRunModesList()
    {
        $enabledRunModes = $this->getPluginsConfiguration('enabled_run_modes');

        $list = [];

        if (count($this->collectors)) {
            foreach ($this->collectors as $collectorShortcut => $collectorParameters) {
                foreach ($collectorParameters['modes'] as $modeShortcut => $modeName) {
                    if (!isset($enabledRunModes[$collectorShortcut]) || (isset($enabledRunModes[$collectorShortcut]) && in_array($modeShortcut, $enabledRunModes[$collectorShortcut]))) {
                        $list['collector.' . $collectorShortcut . '.' . $modeShortcut] = $modeName;
                    }
                }
            }
        }

        return $list;
    }

    public function getWorkersRunModesList()
    {
        $enabledRunModes = $this->getPluginsConfiguration('enabled_run_modes');

        $list = [];

        if (count($this->workers)) {
            foreach ($this->workers as $workerShortcut => $workerParameters) {
                foreach ($workerParameters['modes'] as $modeShortcut => $modeName) {
                    if (!isset($enabledRunModes[$workerShortcut]) || (isset($enabledRunModes[$workerShortcut]) && in_array($modeShortcut, $enabledRunModes[$workerShortcut]))) {
                        $list['worker.' . $workerShortcut . '.' . $modeShortcut] = $modeName;
                    }
                }
            }
        }

        return $list;
    }

    public function getAllRunModesList()
    {
        return array_merge($this->getCollectorsRunModesList(), $this->getWorkersRunModesList());
    }

    public function getAvailableMessageTypes()
    {
        $availableMessageTypes = [];

        $runModes = $this->getCollectorsRunModesList();

        if (count($this->collectors)) {
            foreach ($this->collectors as $collectorShortcut => $collectorParameters) {
                foreach ($collectorParameters['message_types'] as $runModeShortcut => $typeParameters) {
                    foreach ($typeParameters as $typeShortcut => $typeName) {
                        if (in_array('collector.' . $collectorShortcut . '.' . $runModeShortcut, array_keys($runModes))) {
                            $availableMessageTypes[$typeShortcut] = $typeName;
                        }
                    }
                }
            }
        }

        return $availableMessageTypes;
    }

    public function getAllPluginsParameters()
    {
        $list = [];

        if (count($this->collectors)) {
            foreach ($this->collectors as $name => $workspace) {
                foreach ($workspace['parameters'] as $runMode => $parameters) {
                    $list['collector.' . $name . '.' . $runMode] = $parameters;
                }
            }

        }

        if (count($this->workers)) {
            foreach ($this->workers as $name => $workspace) {
                foreach ($workspace['parameters'] as $runMode => $parameters) {
                    $list['worker.' . $name . '.' . $runMode] = $parameters;
                }
            }

        }

        return $list;
    }
}