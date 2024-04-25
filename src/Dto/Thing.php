<?php

namespace App\Dto;

use App\Form\Field;
use App\Form\Form;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[Form]
final class Thing
{
    #[Field('title', TextType::class)]
    public string $title;

    #[Field('body', TextareaType::class, ['required' => false])]
    public ?string $body = null;
}
