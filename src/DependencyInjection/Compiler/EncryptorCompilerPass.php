<?php

declare(strict_types=1);

namespace Shapecode\NYADoctrineEncryptBundle\DependencyInjection\Compiler;

use Shapecode\NYADoctrineEncryptBundle\Encryption\EncryptionManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class EncryptorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container) : void
    {
        $definition = $container->findDefinition(EncryptionManager::class);
        $tags       = $container->findTaggedServiceIds('doctrine.encryptor');

        foreach ($tags as $id => $configs) {
            $definition->addMethodCall('addEncryptor', [new Reference($id)]);
        }
    }
}
