<?php

namespace App\DataGrid\Limit;

use App\DataGrid\Limit;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class SetLimit implements Limit
{
    private int $default;

    /**
     * @param non-empty-list<positive-int> $set
     * @param positive-int|null $default
     */
    public function __construct(private array $set = [20, 50, 100], ?int $default = null)
    {
        $this->default = \in_array($default, $this->set, true) ? $default : $this->set[0];
    }

    public function process(mixed $value): int
    {
        return \in_array($value, $this->set) ? $value : $this->default;
    }
}
