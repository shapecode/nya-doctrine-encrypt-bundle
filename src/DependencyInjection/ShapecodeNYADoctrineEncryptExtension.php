<?php

declare(strict_types=1);

namespace Shapecode\NYADoctrineEncryptBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

final class ShapecodeNYADoctrineEncryptExtension extends ConfigurableExtension
{
    /**
     * @inheritDoc
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container) : void
    {
        $locator = new FileLocator(__DIR__ . '/../Resources/config');
        $loader  = new Loader\YamlFileLoader($container, $locator);
        $loader->load('services.yml');

        // Set parameters
        $container->setParameter('shapecode_doctrine_encrypt.encryptor', $mergedConfig['encryptor']);
        $container->setParameter('shapecode_doctrine_encrypt.secret_directory', $mergedConfig['secret_directory']);
    }
}
