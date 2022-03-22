<?php

namespace App\Translation\Model;

use App\Translation\TranslationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TranslationRepository::class)]
class Translation
{
    /** @readonly */
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 6)]
    public string $locale;

    /** @readonly */
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 100)]
    public string $object;

    /** @readonly */
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 50)]
    public string $objectId;

    /** @readonly */
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 50)]
    public string $field;

    #[ORM\Column(type: 'text')]
    public string $value;
}
