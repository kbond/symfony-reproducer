<?php

namespace Zenstruck;

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Zenstruck\FormRequest\FormState;
use Zenstruck\FormRequest\FormState\InMemoryFormState;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @mixin Request
 */
class FormRequest implements ServiceSubscriberInterface
{
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
        ];
    }

    /**
     * @param array<string,null|Constraint|Constraint[]> $data
     */
    final public function validate(array $data): FormState
    {
        $state = new InMemoryFormState();

        if (!$this->isSubmitted()) {
            // not submitted so return empty state
            return $state;
        }

        foreach (\array_keys($data) as $field) {
            // TODO: "null trim" data
            $value = $this->request->get($field) ?? $this->files->get($field);

            $state->set($field, $value);

            if (null === $constraints = $data[$field]) {
                // empty rule is just "allowed"
                continue;
            }

            foreach ($this->container->get(ValidatorInterface::class)->validate($value, $constraints) as $violation) {
                /** @var ConstraintViolationInterface $violation */
                $state->addError($field, $violation->getMessage());
            }
        }

        // TODO: check csrf
        // TODO: configure globally in config (enabled/disabled, default token_id, default token_field)
        // TODO: Allow CSRF Request Header

        return $state;
    }

    final public function isSubmitted(): bool
    {
        return !$this->isMethodCacheable();
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
        return $this->container->get(RequestStack::class)->getCurrentRequest() ?? throw new \LogicException(\sprintf('%s can only be used within the scope of a request.', static::class));
    }
}
