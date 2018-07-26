<?php

namespace Shapecode\NYADoctrineEncryptBundle;

use Shapecode\NYADoctrineEncryptBundle\DependencyInjection\Compiler\EncryptorCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class ShapecodeNYADoctrineEncryptBundle
 *
 * @package Shapecode\NYADoctrineEncryptBundle
 * @author  Nikita Loges
 */
class ShapecodeNYADoctrineEncryptBundle extends Bundle
{

    /**
     * @inheritdoc
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new EncryptorCompilerPass());
    }
}
