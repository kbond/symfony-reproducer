<?php

namespace App\Assert\Assertion;

use App\Assert;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
final class Throws
{
    private \Closure $during;
    private \Closure $onCatch;
    private string $expectedException;

    public function __construct(callable $during, string|callable $expectedException, private ?string $expectedMessage = null)
    {
        $onCatch = static function () {};

        if (\is_callable($expectedException)) {
            $onCatch = $expectedException(...);
            $parameterRef = (new \ReflectionFunction($onCatch))->getParameters()[0] ?? null;

            if (!$parameterRef || !($type = $parameterRef->getType()) instanceof \ReflectionNamedType) {
                throw new \InvalidArgumentException('When $expectedException is a callback, the first parameter must be type-hinted as the expected exception.');
            }

            $expectedException = $type->getName();
        }

        if (!is_a($expectedException, \Throwable::class, true)) {
            throw new \InvalidArgumentException(sprintf('Expected exception must be an instance of %s, %s given.', \Throwable::class, $expectedException));
        }

        $this->during = $during(...);
        $this->onCatch = $onCatch;
        $this->expectedException = $expectedException;
    }

    public function __invoke(): void
    {
        try {
            ($this->during)();
        } catch (\Throwable $actual) {
            if (!$actual instanceof $this->expectedException) {
                Assert::fail(
                    'Expected "{expected}" to be thrown but got "{actual}".',
                    ['expected' => $this->expectedException, 'actual' => $actual],
                );
            }

            if ($this->expectedMessage && !str_contains($actual->getMessage(), $this->expectedMessage)) {
                Assert::fail(
                    'Expected exception message "{expected}" to contain "{actual}".',
                    ['expected' => $this->expectedMessage, 'actual' => $actual->getMessage()],
                );
            }

            ($this->onCatch)($actual);

            return;
        }

        Assert::fail('Expected "{expected}" to be thrown.', ['expected' => $this->expectedException]);
    }
}
