<?php

namespace App\Assert\Test;

use App\Assert\Expectation;

use function App\Assert\expect;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
trait Expectations
{
    protected function expect(mixed $what): Expectation
    {
        return expect($what);
    }
}
