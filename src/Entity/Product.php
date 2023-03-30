<?php

namespace App\Entity;

use App\Routing\ObjectRoute;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[ObjectRoute('product')]
#[ObjectRoute('product_admin', type: 'admin', parameters: ['extra' => 'param'])]
final class Product
{
    public function __construct(private readonly int $id = 7)
    {
    }

    public function id(): int
    {
        return $this->id;
    }
}
