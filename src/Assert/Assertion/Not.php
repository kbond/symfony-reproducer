<?php

namespace App\Assert\Assertion;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Not extends Conditional
{
    public function __construct(private Conditional $inner, ?string $message = null, ?array $context = null)
    {
        parent::__construct(
            str_replace('<NOT>', 'NOT ', $message ?? $this->inner->message),
            $context ?? $this->inner->context
        );
    }

    protected function evalulate(): bool
    {
        return !$this->inner->evalulate();
    }
}
