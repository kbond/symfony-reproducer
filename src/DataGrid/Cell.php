<?php

namespace App\DataGrid;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface Cell
{
    public function column(): Column;

    public function __invoke(mixed $data): mixed;
}
