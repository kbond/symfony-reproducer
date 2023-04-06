<?php

namespace App\Entity;

use App\Messenger\Monitor\Storage\Model\ProcessedMessage as BaseProcessedMessage;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\HandledStamp;

#[ORM\Entity(readOnly: true)]
class ProcessedMessage extends BaseProcessedMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $content;

    public static function create(Envelope $envelope, ?\Throwable $exception = null): static
    {
        $object = parent::create($envelope, $exception);
        $result = $envelope->last(HandledStamp::class)?->getResult();

        if (\is_scalar($result) || $result instanceof \Stringable) {
            $object->content = $result;
        }

        return $object;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function content(): ?string
    {
        return $this->content;
    }
}
