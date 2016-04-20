<?php

namespace Efrag\Bundle\PaginatorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package Efrag\Bundle\PaginatorBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('efrag_paginator');

        $rootNode
            ->children()
                ->integerNode('perPage')->defaultValue(15)->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
