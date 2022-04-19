<?php

namespace Zenstruck\FormRequest;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccessor;
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
    private static PropertyAccessor $accessor;

    public function __construct(private ValidatorInterface $validator)
    {
    }

    /**
     * @param array<string,null|Constraint|Constraint[]>|object $data
     */
    public function __invoke(Request $request, array|object $data): InMemoryFormState
    {
        if (\is_object($data)) {
            return $this->validateObject($request, $data);
        }

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

    private function validateObject(Request $request, object $object): InMemoryFormState
    {
        // TODO: "null trim" data
        $fields = \array_merge($request->request->all(), $request->files->all());
        $state = new InMemoryFormState();

        foreach ($fields as $field => $value) {
            if (!self::accessor()->isWritable($object, $field)) {
                // todo extra data strategy? ignore/exception?
                continue;
            }

            // set raw data on form state
            $state->set($field, $value);

            // set value on object
            self::accessor()->setValue($object, $field, $value);
        }

        foreach ($this->validator->validate($object) as $violation) {
            /** @var ConstraintViolationInterface $violation */
            if ('' === $path = $violation->getPropertyPath()) {
                $state->addGlobalError($violation->getMessage());

                continue;
            }

            $state->addError($path, $violation->getMessage());
        }

        return $state;
    }

    private static function accessor(): PropertyAccessor
    {
        return self::$accessor ??= new PropertyAccessor(PropertyAccessor::DISALLOW_MAGIC_METHODS);
    }
}
