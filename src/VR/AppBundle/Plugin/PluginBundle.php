<?php

namespace VR\AppBundle\Plugin;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class PluginBundle
 *
 * @package VR\AppBundle\Plugin
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class PluginBundle extends Bundle
{
    protected $workspace = [
        'type' => null,
        'shortcut' => null,
        'name' => null,
        'modes' => [],
        'parameters' => [],
        'message_types' => []
    ];

    public function boot()
    {
        $this->initPlugin();
        $this->savePluginWorkspace();
    }

    public function initPlugin()
    {
        throw new \Exception('Method initPlugin must be implemented!');
    }

    public function setPluginType($type)
    {
        $this->workspace['type'] = $type;
    }

    public function setPluginShortcut($shortcut)
    {
        $this->workspace['shortcut'] = $shortcut;
    }

    public function setPluginName($name)
    {
        $this->workspace['name'] = $name;
    }

    public function addRunMode($shortcut, $name)
    {
        $this->workspace['modes'][$shortcut] = $name;
    }

    public function addParameter($runMode, $name, $type, $required = false, $description = null)
    {
        $this->workspace['parameters'][$runMode][$name] = [
            'type' => $type,
            'required' => $required,
            'description' => $description
        ];
    }

    public function addMessageType($runMode, $shortcut, $name)
    {
        $this->workspace['message_types'][$runMode][$shortcut] = $name;
    }

    protected function savePluginWorkspace()
    {
        $pluginManager = $this->container->get('vr.plugin_manager');

        switch ($this->workspace['type']) {
            case 'collector':
                $pluginManager->addCollector($this->workspace['shortcut'], $this->workspace);
                break;
            case 'worker':
                $pluginManager->addWorker($this->workspace['shortcut'], $this->workspace);
                break;
            default:
                throw new \Exception('Invalid plugin type "' . $this->workspace['type'] . '"');
        }
    }
}