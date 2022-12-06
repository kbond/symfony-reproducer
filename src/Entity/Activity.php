<?php

namespace App\Entity;

use App\Repository\ActivityRepository;
use Doctrine\ORM\Mapping as ORM;
use Zenstruck\Activity as BaseActivity;
use Zenstruck\Activity\ActorAware;
use Zenstruck\Activity\TargetAware;
use Zenstruck\Activity\HasTarget;
use Zenstruck\Activity\HasActorAndUser;
use Zenstruck\Activity\HasNamedConstructor;

#[ORM\Entity(repositoryClass: ActivityRepository::class, readOnly: true)]
class Activity extends BaseActivity implements TargetAware, ActorAware
{
    use HasNamedConstructor, HasTarget, HasActorAndUser;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
