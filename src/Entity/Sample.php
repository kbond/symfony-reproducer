<?php

namespace App\Entity;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class Sample extends Base
{
    public function __construct(
        int $id,
        private string $prop1,
        protected string $prop2,
        public string $prop3,
    ) {
        parent::__construct($id);
    }

    public function getProp1(): string
    {
        return $this->prop1;
    }

    public function getProp2(): string
    {
        return $this->prop2;
    }
}
