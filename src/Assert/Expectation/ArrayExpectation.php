<?php

namespace App\Assert\Expectation;

use App\Assert\Expectation;
use App\Assert\Expectation\Types\TraversableExpectations;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @extends Expectation<array>
 */
final class ArrayExpectation extends Expectation
{
    use TraversableExpectations;
}
