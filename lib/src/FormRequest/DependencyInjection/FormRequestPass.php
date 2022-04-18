<?php

namespace Zenstruck\FormRequest\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Zenstruck\FormRequest\HttpFoundation\FormSessionFactory;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
final class FormRequestPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        // FormRequest's @mixin creates issues when validating SelfValidatingFormRequest's so ensure @mixin is ignored
        if ($container->hasDefinition('annotations.reader')) {
            $container->getDefinition('annotations.reader')->addMethodCall('addGlobalIgnoredName', ['mixin']);
        }

        if ($container->hasDefinition('session.factory')) {
            // decorate the session factory to add our own flash bag
            $container->register('zenstruck_form_request.session_factory', FormSessionFactory::class)
                ->setDecoratedService('session.factory')
                ->setArguments([new Reference('.inner')])
            ;
        }
    }
}
