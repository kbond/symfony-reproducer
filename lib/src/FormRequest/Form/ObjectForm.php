<?php

namespace Zenstruck\FormRequest\Form;

use Zenstruck\FormRequest\Form;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @template T of object
 */
final class ObjectForm extends Form
{
    /**
     * @param T $object
     */
    public function __construct(private object $object, array $data = [])
    {
        parent::__construct($data);
    }

    /**
     * @return T
     */
    public function object(): object
    {
        return $this->object ?? throw new \LogicException('An object was not set.');
    }
}
