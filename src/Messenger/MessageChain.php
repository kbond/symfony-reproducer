<?php

namespace App\Messenger;

use Symfony\Component\Messenger\Stamp\StampInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class MessageChain implements StampInterface
{
    /** @var object[] */
    private array $messages;

    public function __construct(object ...$messages)
    {
        if (\count($messages) < 2) {
            throw new \InvalidArgumentException('At least two messages are required.');
        }

        $this->messages = $messages;
    }

    /**
     * @internal
     *
     * @return array{0:self|null,1:object}
     */
    public function pop(): array
    {
        $clone = clone $this;
        $message = \array_shift($clone->messages);

        return [$clone->messages ? $clone : null, $message];
    }
}
