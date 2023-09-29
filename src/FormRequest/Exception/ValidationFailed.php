<?php

namespace App\FormRequest\Exception;

use App\FormRequest\Form;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ValidationFailed extends \RuntimeException implements \JsonSerializable
{
    public function __construct(private Form $form)
    {
        parent::__construct('The given data was invalid.');
    }

    public function form(): Form
    {
        return $this->form;
    }

    public function jsonSerialize(): array
    {
        return [
            'message' => $this->getMessage(),
            'errors' => $this->form->errors(),
        ];
    }
}
