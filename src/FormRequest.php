<?php

namespace App;

use App\FormRequest\Form;
use App\FormRequest\Validator;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @mixin Request
 */
final class FormRequest
{
    public function __construct(
        #[AutowireLocator([
            RequestStack::class,
            ValidatorInterface::class,
            '?'.CsrfTokenManagerInterface::class
        ])]
        private ContainerInterface $container,
    ){
    }

    public function __call(string $name, array $arguments): mixed
    {
        return $this->unwrap()->{$name}(...$arguments);
    }

    public function __get(string $name): mixed
    {
        return $this->unwrap()->{$name};
    }

    /**
     * @param array<string,null|Constraint|Constraint[]> $data
     */
    public function validate(array $data): Form
    {
        return $this->validator()->validate($data);
    }

    public function unwrap(): Request
    {
        return $this->container->get(RequestStack::class)->getCurrentRequest() ?? throw new \RuntimeException('No request found.');
    }

    private function validator(): Validator
    {
        return new Validator(
            $this->unwrap(),
            $this->container->get(ValidatorInterface::class),
            $this->container->has(CsrfTokenManagerInterface::class) ? $this->container->get(CsrfTokenManagerInterface::class) : null,
        );
    }
}
