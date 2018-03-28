<?php

namespace Gopro\FullcalendarBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('gopro_fullcalendar');
        $rootNode
            ->children()
                ->arrayNode('calendars')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('entity')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('repositorymethod')->end()
                            ->scalarNode('relatedproperty')->end()
                            ->arrayNode('parameters')
                                ->isRequired()
                                ->children()
                                    ->scalarNode('title')
                                        ->isRequired()
                                        ->cannotBeEmpty()
                                    ->end()
                                    ->scalarNode('start')
                                        ->isRequired()
                                        ->cannotBeEmpty()
                                    ->end()
                                    ->scalarNode('end')
                                        ->isRequired()
                                        ->cannotBeEmpty()
                                    ->end()
                                    ->scalarNode('color')->end()
                                    ->arrayNode('url')
                                        ->children()
                                            ->scalarNode('id')
                                                ->isRequired()
                                                ->cannotBeEmpty()
                                            ->end()
                                            ->arrayNode('show')
                                                ->children()
                                                    ->scalarNode('route')
                                                        ->isRequired()
                                                        ->cannotBeEmpty()
                                                    ->end()
                                                    ->scalarNode('role')
                                                        ->isRequired()
                                                        ->cannotBeEmpty()
                                                    ->end()
                                                ->end()
                                            ->end()
                                            ->arrayNode('edit')
                                                ->children()
                                                    ->scalarNode('route')
                                                        ->isRequired()
                                                        ->cannotBeEmpty()
                                                    ->end()
                                                    ->scalarNode('role')
                                                        ->isRequired()
                                                        ->cannotBeEmpty()
                                                    ->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('resource')
                                ->children()
                                    ->scalarNode('id')
                                        ->isRequired()
                                        ->cannotBeEmpty()
                                    ->end()
                                    ->scalarNode('title')
                                        ->isRequired()
                                        ->cannotBeEmpty()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}