<?php

namespace Lexik\Bundle\ColissimoBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('lexik_colissimo');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        $rootNode
            ->children()
                ->arrayNode('ws_coliposte_letter_service')
                    ->children()
                        ->scalarNode('contract_number')->isRequired(true)->cannotBeEmpty()->end()
                        ->scalarNode('password')->isRequired(true)->cannotBeEmpty()->end()
                        ->arrayNode('service_call_context')
                            ->children()
                                ->scalarNode('commercial_name')->end()
                            ->end()
                        ->end()
                        ->arrayNode('sender')
                            ->children()
                                ->scalarNode('company_name')->end()
                                ->scalarNode('line_0')->end()
                                ->scalarNode('line_1')->end()
                                ->scalarNode('line_2')->end()
                                ->scalarNode('line_3')->end()
                                ->scalarNode('postal_code')->end()
                                ->scalarNode('city')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
