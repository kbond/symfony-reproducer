<?php

namespace App\Translation\Model;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
final class TranslatableValueMap
{
    /** @var array<string,mixed> */
    private array $values;

    /** @var array<string,string> */
    private array $lookup;

    /**
     * @param array<string,string> $values
     */
    public function __construct(array $values)
    {
        $this->values = $values;
        $this->lookup = \array_combine(\array_keys(\array_change_key_case($values, \CASE_UPPER)), \array_keys($values));
    }

    public function get(string $property): mixed
    {
        $property = \strtoupper($property);

        if (\str_starts_with($property, 'GET')) {
            $property = \substr($property, 3);
        }

        if (isset($this->lookup[$property])) {
            return $this->values[$this->lookup[$property]];
        }

        return null;
    }

    /**
     * @return array<string,mixed>
     */
    public function values(): array
    {
        return $this->values;
    }
}
