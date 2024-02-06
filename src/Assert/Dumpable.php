<?php

namespace App\Assert;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
trait Dumpable
{
    public function dump(): static
    {
        \function_exists('dump') ? dump($this->dumpValue()) : var_dump($this->dumpValue());

        return $this;
    }

    /**
     * @return never
     */
    public function dd(): void
    {
        $this->dump();
        exit(1);
    }

    abstract protected function dumpValue(): mixed;
}
