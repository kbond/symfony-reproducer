<?php

namespace App\Assert;

use App\Assert\Expectation\PrimaryExpectation;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
trait Expectations
{
    protected function expect(mixed $what): PrimaryExpectation
    {
        return expect($what);
    }
}
