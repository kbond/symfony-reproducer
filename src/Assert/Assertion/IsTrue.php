<?php

namespace App\Assert\Assertion;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class IsTrue extends Conditional
{
    public function __construct(private bool $condition, string $message, array $context = [])
    {
        parent::__construct($message, $context);
    }

    protected function evalulate(): bool
    {
        return $this->condition;
    }
}
