<?php

namespace App\Assert\Assertion;

use App\Assert\AssertionFailed;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @phpstan-import-type Context from AssertionFailed
 */
final class Not extends Conditional
{
    /**
     * @param Context|null $context
     */
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
