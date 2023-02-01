<?php

namespace App\DataGrid\Limit;

use App\DataGrid\Limit;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class StaticLimit implements Limit
{
    /**
     * @param positive-int $value
     */
    public function __construct(private int $value)
    {
    }

    public function process(mixed $value): int
    {
        return $this->value;
    }
}
