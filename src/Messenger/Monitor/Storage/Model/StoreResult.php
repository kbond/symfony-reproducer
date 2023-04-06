<?php

namespace App\Messenger\Monitor\Storage\Model;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\HandledStamp;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
trait StoreResult
{
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $result;

    public static function create(Envelope $envelope, ?\Throwable $exception = null): static
    {
        $object = parent::create($envelope, $exception);
        $result = $envelope->last(HandledStamp::class)?->getResult();

        if (\is_scalar($result) || $result instanceof \Stringable) {
            $object->result = $result;
        }

        return $object;
    }

    public function result(): ?string
    {
        return $this->result;
    }
}
