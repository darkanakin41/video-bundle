<?php

/*
 * This file is part of the Darkanakin41VideoBundle package.
 */

namespace Darkanakin41\VideoBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('darkanakin41_video');
        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            $rootNode = $treeBuilder->root('darkanakin41_video');
        }

        $rootNode
            ->children()
            ->scalarNode('video_class')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('channel_class')->isRequired()->cannotBeEmpty()->end()
            ->arrayNode('platform')
                ->children()
                    ->arrayNode('google')
                        ->children()
                            ->scalarNode('application_key')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('referer')->isRequired()->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                    ->arrayNode('twitch')
                        ->children()
                            ->scalarNode('client_id')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
