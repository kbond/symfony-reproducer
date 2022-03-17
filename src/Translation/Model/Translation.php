<?php

namespace App\Translation\Model;

use App\Translation\TranslationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TranslationRepository::class)]
class Translation
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 6)]
    public string $locale;

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 100)]
    public string $object;

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 50)]
    public string $objectId;

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 50)]
    public string $field;

    #[ORM\Column(type: 'text')]
    public string $value;
}
