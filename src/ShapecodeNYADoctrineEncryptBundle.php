<?php

namespace Shapecode\NYADoctrineEncryptBundle;

use Shapecode\NYADoctrineEncryptBundle\DependencyInjection\Compiler\RegisterServiceCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class ShapecodeNYADoctrineEncryptBundle
 *
 * @package Shapecode\NYADoctrineEncryptBundle
 * @author  Nikita Loges
 * @company tenolo GbR
 */
class ShapecodeNYADoctrineEncryptBundle extends Bundle
{

    /**
     * @inheritdoc
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterServiceCompilerPass());
    }
}
