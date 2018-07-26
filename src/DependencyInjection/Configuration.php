<?php

namespace Shapecode\NYADoctrineEncryptBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package Shapecode\NYADoctrineEncryptBundle\DependencyInjection
 * @author  Nikita Loges
 */
class Configuration implements ConfigurationInterface
{

    /**
     * @inheritdoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('shapecode_doctrine_encrypt');

        $rootNode
            ->children()
                ->scalarNode('encryptor')
                    ->defaultValue('defuse')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('secret_directory_path')
                    ->defaultValue('%kernel.project_dir%/var/shapecode/encrypt')
                ->end()
            ->end();

        return $treeBuilder;
    }

}
