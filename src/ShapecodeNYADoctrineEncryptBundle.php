<?php

declare(strict_types=1);

namespace Shapecode\NYADoctrineEncryptBundle;

use Shapecode\NYADoctrineEncryptBundle\DependencyInjection\Compiler\EncryptorCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class ShapecodeNYADoctrineEncryptBundle extends Bundle
{
    public function build(ContainerBuilder $container) : void
    {
        parent::build($container);

        $container->addCompilerPass(new EncryptorCompilerPass());
    }
}
