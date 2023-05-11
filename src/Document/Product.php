<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[MongoDB\Document]
class Product
{
    #[MongoDB\Id]
    public readonly string $id;

    #[MongoDB\Field(type: 'string')]
    public string $name;

    #[MongoDB\Field(type: 'int')]
    public int $quantity;

    #[MongoDB\Field(type: 'float')]
    public float $price;
}
