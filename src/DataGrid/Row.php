<?php

namespace App\DataGrid;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface Row
{
    public function cells(): Cells;
}
