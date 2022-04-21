<?php

namespace Zenstruck;

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Zenstruck\FormRequest\Form;
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
    private string $csrfTokenId = self::DEFAULT_CSRF_TOKEN_ID;
    private bool $csrfEnabled;
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
            Validator::class,
            '?'.CsrfTokenManagerInterface::class,
        ];
    }

    /**
     * @template T of object
     *
     * @param array<string,null|Constraint|Constraint[]>|T $data
     *
     * @return Form<T>
     */
    final public function validate(array|object $data): Form
    {
        if (!$this->isSubmitted()) {
            // not submitted so return empty state
            return new Form(\is_object($data) ? $data : null);
        }

        $form = $this->container->get(Validator::class)($this->rawData(), $data);

        if (!$this->isCsrfEnabled()) {
            return $form;
        }

        $token = $this->request->get(self::CSRF_TOKEN_FIELD, $this->headers->get(self::CSRF_TOKEN_HEADER));

        if (!$this->isCsrfTokenValid($this->csrfTokenId, $token)) {
            // TODO: alternate behaviour: throw TokenMismatch exception to convert to 419 in event listener
            $form->addGlobalError('The CSRF token is invalid. Please try to resubmit the form.');
        }

        return $form;
    }

    /**
     * @template T of object
     *
     * @param array<string,null|Constraint|Constraint[]>|T $data
     *
     * @return Form<T>
     */
    final public function validateOrFail(array|object $data): Form
    {
        return $this->validate($data)->throwIfInvalid();
    }

    final public function disableCsrf(): self
    {
        $this->csrfEnabled = false;

        return $this;
    }

    final public function enableCsrf(string $tokenId = self::DEFAULT_CSRF_TOKEN_ID): self
    {
        $this->csrfTokenId = $tokenId;
        $this->csrfEnabled = true;

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
        return $this->wrapped ??= $this->container->get(RequestStack::class)->getCurrentRequest() ?? throw new \LogicException(\sprintf('%s can only be used within the scope of a request.', static::class));
    }

    private function isCsrfEnabled(): bool
    {
        if (isset($this->csrfEnabled)) {
            return $this->csrfEnabled;
        }

        if ($this->isJson()) {
            // disable by default if json
            return $this->csrfEnabled = false;
        }

        // enable by default if available
        return $this->csrfEnabled = $this->container->has(CsrfTokenManagerInterface::class);
    }

    private function rawData(): array
    {
        // todo get from json body
        $data = [...$this->request->all(), ...$this->files->all(), ...$this->jsonBody()];

        \array_walk_recursive($data, static function(&$value) {
            if (!\is_string($value)) {
                return;
            }

            if ('' === $value = \trim($value)) {
                $value = null;
            }
        });

        return $data;
    }

    private function jsonBody(): array
    {
        if (!$this->isJson()) {
            return [];
        }

        try {
            $json = \json_decode($this->getContent(), associative: true, flags: \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return [];
        }

        return \is_array($json) ? $json : [];
    }

    private function isJson(): bool
    {
        return 'json' === $this->getPreferredFormat();
    }
}
