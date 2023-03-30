<?php

namespace App\Routing;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[Autoconfigure(public: true)]
final class ObjectUrlGenerator implements UrlGeneratorInterface
{
    public function __construct(
        private UrlGeneratorInterface $inner,
        private PropertyAccessorInterface $propertyAccessor,

        #[Target('systemCache')]
        private CacheInterface $cache,
    ) {
    }

    /**
     * @param string|object $object If object, must have #[ObjectRoute] attribute
     * @param array<string,mixed>|string|null $type The object route type or $parameters
     * @param int|array<string,mixed> $parameters The parameters or $referenceType
     */
    public function generate(string|object $object, array|string|null $type = null, int|array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string
    {
        $referenceType = \is_int($parameters) ? $parameters : $referenceType;
        $parameters = \is_array($type) ? $type : $parameters;
        $type = \is_array($type) ? null : $type;

        if (\is_int($parameters)) {
            $parameters = [];
        }

        if (\is_string($object)) {
            return $this->inner->generate($object, $parameters, $referenceType);
        }

        $route = ObjectRoute::get($object, $type, $this->cache);

        foreach ($route->accessors as $key => $accessor) {
            if (\is_int($key)) {
                $key = $accessor;
            }

            $parameters[$key] = $this->propertyAccessor->getValue($object, $accessor);
        }

        return $this->inner->generate($route->name, \array_merge($route->parameters, $parameters), $referenceType);
    }

    public function setContext(RequestContext $context): void
    {
        $this->inner->setContext($context);
    }

    public function getContext(): RequestContext
    {
        return $this->inner->getContext();
    }
}
