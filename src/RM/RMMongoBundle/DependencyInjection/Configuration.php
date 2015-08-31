<?php

namespace RM\RMMongoBundle\DependencyInjection;

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
        $rootNode    = $treeBuilder->root('rm_mongo');

        $rootNode
            ->children()
                ->scalarNode('default_connection')->defaultValue('default')->end()
                ->append($this->addConnectionNode())
            ->end();
        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }

    public function addConnectionNode() {
        $treeBuilder = new TreeBuilder();
        $node        = $treeBuilder->root('connections');

        $node
            ->isRequired()
            ->requiresAtLeastOneElement()
            ->prototype('array')
                ->children()
                    ->scalarNode('host')->end()
                    ->scalarNode('port')->defaultValue(27017)->end()
                    ->scalarNode('database')->isRequired()->cannotBeEmpty()->end()
                ->end()
            ->end()
        ;
        return $node;
    }

}
