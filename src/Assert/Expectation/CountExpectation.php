<?php

namespace App\Assert\Expectation;

use App\Assert\Expectation;
use App\Assert\Expectation\Types\SizeExpectations;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @extends Expectation<int>
 */
final class CountExpectation extends Expectation
{
    use SizeExpectations;
}
