<?php

namespace App\Entity;

use App\Messenger\Monitor\Model\ProcessedMessage as BaseProcessedMessage;
use App\Messenger\Monitor\Model\StoreResult;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(readOnly: true)]
class ProcessedMessage extends BaseProcessedMessage
{
    use StoreResult;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function id(): ?int
    {
        return $this->id;
    }
}
