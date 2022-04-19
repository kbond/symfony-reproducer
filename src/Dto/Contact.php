<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Contact
{
    #[Assert\NotBlank]
    public ?string $name = null;

    #[Assert\NotBlank]
    public ?string $email = null;

    #[Assert\Choice(['sales', 'marketing'])]
    public ?string $department = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 10)]
    public ?string $message = null;

    #[Assert\Count(max: 3)]
    #[Assert\All(new Assert\Image())]
    public array $screenshots = [];

    #[Assert\IsTrue(message: 'You must agree!')]
    public bool $agree = false;

    public bool $newsletter = false;
}
