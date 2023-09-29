<?php

namespace App;

use App\FormRequest\Exception\ValidationFailed;
use App\FormRequest\Form;
use App\FormRequest\Validator;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Service\Attribute\SubscribedService;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @mixin Request
 */
final class FormRequest
{
    private Request $request;

    public function __construct(
        private RequestStack $requestStack,
        private ValidatorInterface $validator,
        private ?CsrfTokenManagerInterface $csrfTokenManager = null,
    ) {
    }

    public function __call(string $name, array $arguments): mixed
    {
        return $this->request()->{$name}(...$arguments);
    }

    /**
     * @param array<string,null|Constraint|Constraint[]> $data
     */
    public function validate(array $data): Form
    {
        // TODO: $this->validator()->withGroups(...)->withContext()
        return $this->validator()->validate($data);
    }

    /**
     * @param array<string,null|Constraint|Constraint[]> $data
     *
     * @throws ValidationFailed
     */
    public function validateOrFail(array $data): Form
    {
        return $this->validate($data)->throwIfInvalid();
    }

    public function request(): Request
    {
        return $this->wrapped ??= $this->requestStack->getCurrentRequest() ?? throw new \LogicException(\sprintf('%s can only be used within the scope of a request.', self::class));
    }

    private function validator(): Validator
    {
        return new Validator($this->request(), $this->validator, $this->csrfTokenManager);
    }
}
