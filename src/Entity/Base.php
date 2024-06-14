<?php

namespace App\Entity;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class Base
{
    public function __construct(private int $id)
    {
    }

    public function getId(): int
    {
        return $this->id;
    }
}
