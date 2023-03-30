<?php

namespace App\Messenger\Monitor\Storage;

use App\Entity\StoredMessage;
use App\Messenger\Monitor\Storage;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Messenger\Envelope;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ORMStorage implements Storage
{
    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly string $storedMessageClass = StoredMessage::class, // todo make configurable
    ) {
    }

    public function save(Envelope $envelope, ?\Throwable $exception = null): void
    {
        $om = $this->registry->getManagerForClass($this->storedMessageClass) ?? throw new \LogicException('No object manager for class.');
        $object = $this->storedMessageClass::create($envelope, $exception);

        $om->persist($object);
        $om->flush();
    }
}
