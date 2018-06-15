<?php

namespace Shapecode\NYADoctrineEncryptBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class EncryptorCompilerPass
 *
 * @package Shapecode\NYADoctrineEncryptBundle\DependencyInjection\Compiler
 * @author  Nikita Loges
 * @company tenolo GbR
 */
class EncryptorCompilerPass implements CompilerPassInterface
{
    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container)
    {
        $definiton = $container->getDefinition('shapecode_doctrine_encrypt.encryption.manager');
        $tags = $container->findTaggedServiceIds('doctrine.encryptor');

        foreach ($tags as $id => $configs) {
            foreach ($configs as $config) {
                $definiton->addMethodCall('addEncryptor', [new Reference($id)]);
            }
        }
    }
}
