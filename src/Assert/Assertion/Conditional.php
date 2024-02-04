<?php

namespace App\Assert\Assertion;

use App\Assert\AssertionFailed;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @phpstan-import-type Context from AssertionFailed
 */
abstract class Conditional
{
    /**
     * @param Context $context
     */
    public function __construct(public readonly string $message, public readonly array $context = [])
    {
    }

    final public function __invoke(): void
    {
        if (!$this->evalulate()) {
            throw new AssertionFailed(str_replace('<NOT>', '', $this->message), $this->context);
        }
    }

    abstract protected function evalulate(): bool;
}
