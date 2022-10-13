<?php

namespace App;

use Symfony\Component\VarExporter\LazyObjectInterface;
use Symfony\Component\VarExporter\ProxyHelper;
use Zenstruck\Foundry\AnonymousFactory;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ProxyFactory
{
    /**
     * @template T of object
     *
     * @param class-string<T> $class
     *
     * @return T&Proxy
     */
    public static function create(string $class, callable|array $attributes): object
    {
        $object = AnonymousFactory::new($class)->create($attributes)->object();

        return self::generateProxy($object)::createLazyProxy(fn() => $object);
    }

    /**
     * @return class-string<LazyObjectInterface>
     */
    private static function generateProxy(object $object): string
    {
        $proxyClass = \str_replace('\\', '', $object::class).'Proxy';

        if (\class_exists($proxyClass)) {
            return $proxyClass;
        }

        $proxyCode = 'class '.$proxyClass.ProxyHelper::generateLazyProxy(new \ReflectionClass($object::class));
        $proxyCode = \str_replace(
            [
                'implements \Symfony\Component\VarExporter\LazyObjectInterface',
                'use \Symfony\Component\VarExporter\LazyProxyTrait;',
                'if (isset($this->lazyObjectReal)) {'
            ],
            [
                \sprintf('implements \%s, \Symfony\Component\VarExporter\LazyObjectInterface', Proxy::class),
                \sprintf('use \%s, \Symfony\Component\VarExporter\LazyProxyTrait;', IsProxy::class),
                "\$this->autoRefresh();\n\n        if (isset(\$this->lazyObjectReal)) {"
            ],
            $proxyCode
        );

        // todo cache to file and require
        eval($proxyCode);

        return $proxyClass;
    }
}
