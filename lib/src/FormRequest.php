<?php

namespace Zenstruck;

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Zenstruck\FormRequest\FormState;
use Zenstruck\FormRequest\FormState\InMemoryFormState;
use Zenstruck\FormRequest\Validator;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @mixin Request
 */
class FormRequest implements ServiceSubscriberInterface
{
    // todo make these configurable
    private const DEFAULT_CSRF_TOKEN_ID = 'form';
    private const CSRF_TOKEN_FIELD = '_token';
    private const CSRF_TOKEN_HEADER = 'X-CSRF-TOKEN';

    // todo make globally configurable
    private ?string $csrfTokenId = self::DEFAULT_CSRF_TOKEN_ID;

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
            Validator::class,
            '?'.CsrfTokenManagerInterface::class,
        ];
    }

    /**
     * @param array<string,null|Constraint|Constraint[]> $data
     */
    final public function validate(array $data): FormState
    {
        if (!$this->isSubmitted()) {
            // not submitted so return empty state
            return new InMemoryFormState();
        }

        $state = $this->container->get(Validator::class)($this->unwrap(), $data);

        if (!$this->isCsrfEnabled()) {
            return $state;
        }

        $token = $this->request->get(self::CSRF_TOKEN_FIELD, $this->headers->get(self::CSRF_TOKEN_HEADER));

        if (!$this->isCsrfTokenValid($this->csrfTokenId, $token)) {
            // TODO: alternate behaviour: throw TokenMismatch exception to convert to 419 in event listener
            $state->addGlobalError('The CSRF token is invalid. Please try to resubmit the form.');
        }

        return $state;
    }

    final public function enableCsrf(string $tokenId = self::DEFAULT_CSRF_TOKEN_ID): self
    {
        $this->csrfTokenId = $tokenId;

        return $this;
    }

    final public function disableCsrf(): self
    {
        $this->csrfTokenId = null;

        return $this;
    }

    final public function isSubmitted(): bool
    {
        return !$this->isMethodCacheable();
    }

    final public function isCsrfTokenValid(string $id, ?string $token): bool
    {
        if (!$this->container->has(CsrfTokenManagerInterface::class)) {
            throw new \LogicException('CSRF not enabled in your application.');
        }

        return $this->container->get(CsrfTokenManagerInterface::class)->isTokenValid(new CsrfToken($id, $token));
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

    private function isCsrfEnabled(): bool
    {
        return null !== $this->csrfTokenId;
    }
}
