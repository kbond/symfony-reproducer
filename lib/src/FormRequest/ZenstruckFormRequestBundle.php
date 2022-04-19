<?php

namespace Zenstruck\FormRequest;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Zenstruck\FormRequest\DependencyInjection\FormRequestPass;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ZenstruckFormRequestBundle extends Bundle
{
    /**
     * @internal
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new FormRequestPass());
    }
}
