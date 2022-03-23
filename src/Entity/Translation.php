<?php

namespace App\Entity;

use App\Translation\Model\Translation as BaseTranslation;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Translation extends BaseTranslation
{
    /** @readonly */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public ?int $id = null;
}
