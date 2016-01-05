<?php

namespace VR\AppBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Michał Jabłoński <mjapko@gmail.com>
 *
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('vr_app');

        $rootNode
            ->children()
                ->scalarNode('sugarcrm_url')
                    ->info('SugarCRM instance\'s URL used to generate external links in GUI.')
                    ->isRequired()
                ->end()

                ->arrayNode('create_account')
                    ->info('Section for "Create Account" functionality (module available in GUI).')
                    ->children()
                        ->scalarNode('provider')
                            ->info('Provider for collecting data, like "nne" or "cvrapi".')
                            ->isRequired()
                        ->end()
                        ->scalarNode('add_sub_companies')
                            ->info('[true] Create 2 companies (main & sub) or [false] only one (main)?')
                            ->isRequired()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('plugins')
                    ->info('General plugins configuration')
                    ->isRequired()
                    ->children()
                        ->arrayNode('enabled_run_modes')
                            ->info('For each plugin, the list of all available modes is defined in the main Bundle\'s class.')
                            ->isRequired()
                            ->prototype('array')
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
