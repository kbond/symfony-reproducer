<?php

namespace Zenstruck;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Zenstruck\FormRequest\DependencyInjection\FormRequestPass;
use Zenstruck\FormRequest\DependencyInjection\FormRequestExtension;

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

    /**
     * @internal
     */
    public function getContainerExtension(): FormRequestExtension
    {
        return new FormRequestExtension();
    }
}
