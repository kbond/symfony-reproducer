<?php

namespace App\Translation\Model;

use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
#[ORM\UniqueConstraint(name: 'lookup_idx', fields: ['locale', 'object', 'objectId', 'field'])]
abstract class Translation
{
    #[ORM\Column(type: 'text')]
    private ?string $value = null;

    public function __construct(
        #[ORM\Column(type: 'string', length: 6)]
        private string $locale,

        #[ORM\Column(type: 'string', length: 100)]
        private string $object,

        #[ORM\Column(type: 'string', length: 50)]
        private string $objectId,

        #[ORM\Column(type: 'string', length: 50)]
        private string $field,
    ) {
    }

    public function locale(): string
    {
        return $this->locale;
    }

    public function object(): string
    {
        return $this->object;
    }

    public function objectId(): string
    {
        return $this->objectId;
    }

    public function field(): string
    {
        return $this->field;
    }

    public function value(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return array<string,string>
     */
    public function toArray(): array
    {
        return [
            'locale' => $this->locale,
            'object' => $this->object,
            'object_id' => $this->objectId,
            'field' => $this->field,
            'value' => $this->value,
        ];
    }
}
