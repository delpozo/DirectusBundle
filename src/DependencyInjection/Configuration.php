<?php

namespace SBA\DirectusBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('sba_directus_symfony');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->scalarNode('base_url')->isRequired()->end()
                ->scalarNode('project_name')->end()

                ->arrayNode('authentication')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('email')
                            ->isRequired()
                            ->defaultValue('%env(string:DIRECTUS_EMAIL)%')
                        ->end()
                        ->scalarNode('password')
                            ->isRequired()
                            ->defaultValue('%env(string:DIRECTUS_PASSWORD)%')
                        ->end()
                        ->enumNode('mode')
                            ->isRequired()
                            ->values(['jwt', 'cookie'])
                            ->defaultValue('jwt')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
