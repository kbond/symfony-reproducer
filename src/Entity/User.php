<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Zenstruck\Filesystem\Doctrine\Attribute\Mapping;
use Zenstruck\Filesystem\Node\File;
use Zenstruck\Filesystem\Node\File\Image;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[Mapping('public')]
    #[ORM\Column(type: 'zs_file', nullable: true)]
    public ?File $file1 = null;

    #[ORM\Column(type: 'zs_image', nullable: true, options: ['filesystem' => 'public'])]
    public ?Image $image1 = null;

    #[Mapping('public', namer: 'expression:{this.username}.txt')]
    private File $virtualFile1;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function virtualFile1(): ?File
    {
        return $this->virtualFile1->exists() ? $this->virtualFile1 : null;
    }
}
