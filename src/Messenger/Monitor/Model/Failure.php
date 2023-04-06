<?php

namespace App\Messenger\Monitor\Model;

use Symfony\Component\Messenger\Exception\HandlerFailedException;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Failure
{
    private string $message;
    private \Throwable $exception;

    public function __construct(private string $class, string $message)
    {
        $this->message = \trim($message);
    }

    public function __toString(): string
    {
        return \sprintf('%s: %s', $this->class, $this->message);
    }

    public static function from(\Throwable|string $exception): self
    {
        if (\is_string($exception)) {
            return new self(...\explode(':', $exception, 2));
        }

        if ($exception instanceof HandlerFailedException) {
            $exception = $exception->getPrevious() ?? $exception;
        }

        $failure = new self($exception::class, $exception->getMessage());
        $failure->exception = $exception;

        return $failure;
    }

    public function class(): string
    {
        return $this->class;
    }

    public function message(): string
    {
        return $this->message;
    }

    public function exception(): \Throwable
    {
        return $this->exception ?? throw new \LogicException('Exception is not available after it\'s been stored.');
    }
}
