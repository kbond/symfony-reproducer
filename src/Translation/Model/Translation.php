<?php

namespace App\Translation\Model;

use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
#[ORM\UniqueConstraint(name: 'lookup_idx', columns: ['locale', 'object', 'object_id', 'field'])]
abstract class Translation
{
    /** @readonly */
    #[ORM\Column(type: 'string', length: 6)]
    public string $locale;

    /** @readonly */
    #[ORM\Column(type: 'string', length: 100)]
    public string $object;

    /** @readonly */
    #[ORM\Column(type: 'string', length: 50)]
    public string $objectId;

    /** @readonly */
    #[ORM\Column(type: 'string', length: 50)]
    public string $field;

    #[ORM\Column(type: 'text')]
    public ?string $value = null;

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
