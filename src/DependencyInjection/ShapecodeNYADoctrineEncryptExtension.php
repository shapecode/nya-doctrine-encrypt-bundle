<?php

namespace Shapecode\NYADoctrineEncryptBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

/**
 * Class ShapecodeNYADoctrineEncryptExtension
 *
 * @package Shapecode\NYADoctrineEncryptBundle\DependencyInjection
 * @author  Nikita Loges
 */
class ShapecodeNYADoctrineEncryptExtension extends ConfigurableExtension
{

    /**
     * @inheritdoc
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $locator = new FileLocator(__DIR__ . '/../Resources/config');
        $loader = new Loader\YamlFileLoader($container, $locator);
        $loader->load('services.yml');

        // Set parameters
        $container->setParameter('shapecode_doctrine_encrypt.encryptor', $mergedConfig['encryptor']);
        $container->setParameter('shapecode_doctrine_encrypt.secret_key_path', $mergedConfig['secret_directory_path']);
    }
}
