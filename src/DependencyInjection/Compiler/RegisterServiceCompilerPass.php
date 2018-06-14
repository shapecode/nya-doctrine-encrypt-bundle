<?php

namespace Shapecode\NYADoctrineEncryptBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class RegisterServiceCompilerPass
 *
 * @package Shapecode\NYADoctrineEncryptBundle\DependencyInjection\Compiler
 * @author  Nikita Loges
 * @company tenolo GbR
 */
class RegisterServiceCompilerPass implements CompilerPassInterface
{
    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container)
    {
    }
}
