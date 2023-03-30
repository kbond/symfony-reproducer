<?php

namespace App\Messenger\Monitor\Statistics\Metric;

use App\Messenger\Monitor\Statistics\Metric;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface Calculator
{
    public function calculate(Metric $metric): float;
}
