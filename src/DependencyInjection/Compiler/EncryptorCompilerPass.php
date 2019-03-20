<?php

namespace Shapecode\NYADoctrineEncryptBundle\DependencyInjection\Compiler;

use Shapecode\NYADoctrineEncryptBundle\Encryption\EncryptionManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class EncryptorCompilerPass
 *
 * @package Shapecode\NYADoctrineEncryptBundle\DependencyInjection\Compiler
 * @author  Nikita Loges
 */
class EncryptorCompilerPass implements CompilerPassInterface
{
    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container): void
    {
        $definiton = $container->findDefinition(EncryptionManager::class);
        $tags = $container->findTaggedServiceIds('doctrine.encryptor');

        foreach ($tags as $id => $configs) {
            $definiton->addMethodCall('addEncryptor', [new Reference($id)]);
        }
    }
}
