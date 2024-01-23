<?php

namespace App\Twig\Components\Form;

use Symfony\Component\Form\FormView;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[AsTwigComponent]
final class Row
{
    private const TYPES = [
        'choice',
        'textarea',
        'button',
        'submit',
        'hidden',
        'checkbox',
        'radio',
        'email',
        'file',
        'date',
        'datetime-local',
        'color',
        'month',
        'number',
        'password',
        'range',
        'tel',
        'time',
        'url',
        'week',
    ];

    public ?FormView $field;
    public ?string $type = null;
    public bool $hasErrors;
    public bool $isValid;

    public function mount(
        ?FormView $field = null,
        bool $error = false,
        bool $valid = false,
    ): void {
        $this->field = $field;
        $this->hasErrors = $error ?: !($field?->vars['valid'] ?? true);

        if (!$valid && $field) {
            $valid = ($field->vars['valid'] ?? false) && ($field->vars['submitted'] ?? false);
        }

        $this->isValid = $valid;

        if (!$field) {
            return;
        }

        // determine the type of field
        foreach (array_reverse($field->vars['block_prefixes'] ?? []) as $prefix) {
            if (in_array($prefix, self::TYPES, true)) {
                $this->type = $prefix;

                break;
            }
        }
    }
}
