<?php

namespace App\DataGrid;

use Zenstruck\Collection\Page;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface Grid
{
    public function columns(): Columns;

    public function page(): Page;
}
