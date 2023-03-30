<?php

namespace App\Routing;

use Symfony\Contracts\Cache\CacheInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class ObjectRoute
{
    public function __construct(
        /**
         * @readonly
         */
        public string $name,

        /**
         * @readonly
         *
         * @var array<array-key,mixed>
         */
        public array $accessors = ['id'],

        /**
         * @readonly
         */
        public ?string $type = null,

        /**
         * @readonly
         *
         * @var array<array-key,mixed>
         */
        public array $parameters = [],
    ) {
    }

    /**
     * @internal
     */
    public static function get(object $object, ?string $type, CacheInterface $cache): self
    {
        return self::fromCache($object::class, $cache)[(string) $type] ?? throw new \InvalidArgumentException(\sprintf('No object route of type "%s" for object "%s".', $type ?: '(null)', $object::class));
    }

    /**
     * @internal
     *
     * @return array<?string,self>
     */
    public static function fromCache(string $class, CacheInterface $cache, bool $force = false): array
    {
        return $cache->get(
            'zs-object-route-'.\hash(\PHP_VERSION_ID >= 80100 ? 'xxh3' : 'crc32c', $class),
            fn() => self::for($class),
            $force ? \INF : null,
        );
    }

    /**
     * @return array<?string,self>
     */
    private static function for(string $class): array
    {
        $ret = [];

        foreach ((new \ReflectionClass($class))->getAttributes(self::class) as $attribute) {
            $route = $attribute->newInstance();

            if (isset($ret[$route->type])) {
                throw new \LogicException(\sprintf('Duplicate object route type "%s" for object "%s".', $route->type ?? '(null)', $class));
            }

            $ret[$route->type] = $route;
        }

        return $ret;
    }
}
