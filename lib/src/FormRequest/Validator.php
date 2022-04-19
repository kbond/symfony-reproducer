<?php

namespace Zenstruck\FormRequest;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Zenstruck\FormRequest\FormState\InMemoryFormState;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
final class Validator
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    /**
     * @param array<string,null|Constraint|Constraint[]> $data
     */
    public function __invoke(Request $request, array $data): InMemoryFormState
    {
        $state = new InMemoryFormState();

        foreach (\array_keys($data) as $field) {
            // TODO: "null trim" data
            $value = $request->get($field) ?? $request->files->get($field);

            $state->set($field, $value);

            if (null === $constraints = $data[$field]) {
                // empty rule is just "allowed"
                continue;
            }

            foreach ($this->validator->validate($value, $constraints) as $violation) {
                /** @var ConstraintViolationInterface $violation */
                $state->addError($field, $violation->getMessage());
            }
        }

        return $state;
    }
}
