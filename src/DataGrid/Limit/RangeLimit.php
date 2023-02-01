<?php

namespace App\DataGrid\Limit;

use App\DataGrid\Limit;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class RangeLimit implements Limit
{
    /**
     * @param positive-int $max
     * @param positive-int $min
     * @param positive-int $default
     */
    public function __construct(private int $max = 100, private int $min = 1, private int $default = 20)
    {
    }

    public function process(mixed $value): int
    {
        return match(true) {
            !\is_numeric($value) => $this->default,
            $value < $this->min => $this->min,
            $value > $this->max => $this->max,
            default => $value,
        };
    }
}
