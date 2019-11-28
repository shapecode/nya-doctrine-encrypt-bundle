<?php

declare(strict_types=1);

namespace Shapecode\NYADoctrineEncryptBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public const ROOT_NODE = 'shapecode_nya_doctrine_encrypt';

    public function getConfigTreeBuilder() : TreeBuilder
    {
        $treeBuilder = new TreeBuilder(self::ROOT_NODE);
        $rootNode    = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('encryptor')
                    ->defaultValue('halite')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('secret_directory')
                    ->defaultValue('%kernel.project_dir%/var/shapecode/encrypt')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
