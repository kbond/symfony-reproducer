<?php

namespace App\Assert\Assertion;

use function App\Assert\fail;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Contains extends Conditional
{
    public function __construct(
        private mixed $needle,
        private mixed $haystack,
        private bool $strict = false,
        string $message = 'Expected {haystack} to <NOT>contain {needle}.',
        array $context = [],
    ) {
        parent::__construct($message, array_merge($context, ['haystack' => $this->haystack, 'needle' => $needle]));
    }

    protected function evalulate(): bool
    {
        if (\is_array($this->haystack)) {
            return \in_array($this->needle, $this->haystack, $this->strict);
        }

        if (\is_string($this->haystack) && \is_string($this->needle)) {
            return str_contains($this->haystack, $this->needle);
        }

        fail('Cannot check if {haystack} contains {needle}.', ['haystack' => $this->haystack, 'needle' => $this->needle]);
    }
}
