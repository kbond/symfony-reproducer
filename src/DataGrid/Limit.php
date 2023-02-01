<?php

namespace App\DataGrid;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface Limit
{
    /**
     * @return positive-int
     */
    public function process(mixed $value): int;
}
