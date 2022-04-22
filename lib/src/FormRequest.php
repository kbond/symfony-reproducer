<?php

namespace Zenstruck;

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Csrf\CsrfToken;
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
        // TODO: $this->validator($data)->withGroups(...)->withContext()
        $form = $this->validator()->validate($data);

        // TODO move to validator?
        if (!$form->isSubmitted()) {
            return $form;
        }

        // TODO move to validator?
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

    final public function isCsrfTokenValid(string $id, ?string $token): bool
    {
        // TODO: is this really needed?
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

        if ('html' !== $this->getPreferredFormat()) {
            // disable by default if no html
            return $this->csrfEnabled = false;
        }

        // enable by default if available
        return $this->csrfEnabled = $this->container->has(CsrfTokenManagerInterface::class);
    }
}
