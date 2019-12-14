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
            ->scalarNode('platform')
                ->arrayPrototype()
                    ->children()
                        ->integerNode('api_key')->isRequired()->cannotBeEmpty()->end()
                        ->integerNode('api_secret')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
