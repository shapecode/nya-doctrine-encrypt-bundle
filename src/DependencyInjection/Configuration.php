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
        $treeBuilder = new TreeBuilder('shapecode_nya_doctrine_encrypt');

        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('shapecode_nya_doctrine_encrypt');
        }

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
