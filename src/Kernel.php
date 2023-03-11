<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Zenstruck\Collection\Doctrine\EntityRepository;
use Zenstruck\Collection\Doctrine\EntityRepositoryFactory;
use Zenstruck\Collection\Doctrine\ForClass;
use Zenstruck\Collection\Doctrine\ObjectRepositoryFactory;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container): void
    {
        $container->registerAttributeForAutoconfiguration(
            ForClass::class,
            function(Definition $definition, ForClass $attribute, \ReflectionClass $class) {
                $definition->addTag('zenstruck_collection.orm_repository', ['class' => $attribute->name]);

                if (EntityRepository::class === $class->getConstructor()?->class) {
                    $definition->setArguments([new Reference('doctrine'), $attribute->name]);
                }
            }
        );

        $container->register('zenstruck_collection.orm_repository_factory', EntityRepositoryFactory::class)
            ->setArguments([
                new Reference('doctrine'),
                new ServiceLocatorArgument(new TaggedIteratorArgument('zenstruck_collection.orm_repository', 'class', needsIndexes: true)),
            ])
        ;
        $container->setAlias(EntityRepositoryFactory::class, 'zenstruck_collection.orm_repository_factory');
        $container->setAlias(ObjectRepositoryFactory::class, 'zenstruck_collection.orm_repository_factory');
    }
}
