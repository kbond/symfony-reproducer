<?php

namespace App\Messenger\Monitor\Transport;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Receiver\ListableReceiverInterface;
use Symfony\Component\Messenger\Transport\Receiver\MessageCountAwareInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @implements \IteratorAggregate<Envelope>
 */
final class TransportStatus implements \IteratorAggregate, \Countable
{
    public function __construct(
        public readonly string $name,
        public readonly TransportInterface $transport,
    ) {
    }

    public function isCountable(): bool
    {
        return $this->transport instanceof MessageCountAwareInterface;
    }

    public function isListable(): bool
    {
        return $this->transport instanceof ListableReceiverInterface;
    }

    public function count(): int
    {
        if (!$this->transport instanceof MessageCountAwareInterface) {
            throw new \LogicException(sprintf('Transport "%s" is not countable.', $this->name));
        }

        return $this->transport->getMessageCount();
    }

    public function getIterator(): \Traversable
    {
        if (!$this->transport instanceof ListableReceiverInterface) {
            throw new \LogicException(sprintf('Transport "%s" is not listable.', $this->name));
        }

        yield from $this->transport->all();
    }
}
