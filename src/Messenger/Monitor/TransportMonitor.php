<?php

namespace App\Messenger\Monitor;

use App\Messenger\Monitor\Transport\TransportStatus;
use Symfony\Component\DependencyInjection\Attribute\TaggedLocator;
use Symfony\Contracts\Service\ServiceProviderInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @implements \IteratorAggregate<string,TransportStatus>
 */
final class TransportMonitor implements \Countable, \IteratorAggregate
{
    private bool $countable = false;
    private bool $listable = false;

    /**
     * @internal
     */
    public function __construct(#[TaggedLocator('messenger.receiver', 'alias')] private ServiceProviderInterface $transports)
    {
    }

    public function get(string $name): TransportStatus
    {
        if (!$this->transports->has($name)) {
            throw new \InvalidArgumentException(\sprintf('Transport "%s" does not exist.', $name));
        }

        return new TransportStatus($name, $this->transports->get($name));
    }

    public function countable(): self
    {
        $clone = clone $this;
        $clone->countable = true;

        return $clone;
    }

    public function listable(): self
    {
        $clone = clone $this;
        $clone->listable = true;

        return $clone;
    }

    public function all(): array
    {
        return \iterator_to_array($this);
    }

    public function getIterator(): \Traversable
    {
        foreach (\array_keys($this->transports->getProvidedServices()) as $name) {
            $status = new TransportStatus($name, $this->transports->get($name));

            if ($this->countable && !$status->isCountable()) {
                continue;
            }

            if ($this->listable && !$status->isListable()) {
                continue;
            }

            yield $name => $status;
        }
    }

    public function count(): int
    {
        return \iterator_count($this);
    }

    /**
     * @return string[]
     */
    public function names(): array
    {
        return \array_keys($this->all());
    }
}
