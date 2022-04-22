<?php

namespace Zenstruck;

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Zenstruck\FormRequest\Form;
use Zenstruck\FormRequest\Form\ObjectForm;
use Zenstruck\FormRequest\Validator;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @mixin Request
 */
class FormRequest implements ServiceSubscriberInterface
{
    private Request $wrapped;

    private ContainerInterface $container;

    final public function __call(string $name, array $arguments): mixed
    {
        // todo improve error message
        return $this->unwrap()->{$name}(...$arguments);
    }

    final public function __get(string $name): mixed
    {
        // todo improve error message
        return $this->unwrap()->{$name};
    }

    final public function __isset(string $name): bool
    {
        return isset($this->unwrap()->{$name});
    }

    public static function getSubscribedServices(): array
    {
        return [
            RequestStack::class,
            ValidatorInterface::class,
            DecoderInterface::class,
            DenormalizerInterface::class,
            '?'.CsrfTokenManagerInterface::class,
        ];
    }

    /**
     * @template T of object
     *
     * @param class-string<T>|T|array<string,null|Constraint|Constraint[]> $data
     *
     * @return Form<T>
     */
    final public function validate(string|array|object $data): Form
    {
        // TODO: $this->validator()->withGroups(...)->withContext()
        return $this->validator()->validate($data);
    }

    /**
     * @template T of object
     *
     * @param class-string<T>|T|array<string,null|Constraint|Constraint[]> $data
     *
     * @return Form|ObjectForm<T>
     */
    final public function validateOrFail(string|array|object $data): Form
    {
        return $this->validate($data)->throwIfInvalid();
    }

    public function validator(): Validator
    {
        return new Validator($this->unwrap(), $this->container);
    }

    /**
     * @internal
     */
    final public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    final public function unwrap(): Request
    {
        return $this->wrapped ??= $this->container->get(RequestStack::class)->getCurrentRequest() ?? throw new \LogicException(\sprintf('%s can only be used within the scope of a request.', static::class));
    }
}
