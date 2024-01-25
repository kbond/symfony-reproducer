<?php

namespace App\Iconify;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class SetMetadata
{
    public function __construct(private array $data)
    {
    }

    public function title(): string
    {
        return $this->data['title'];
    }

    public function total(): int
    {
        return $this->data['total'];
    }

    public function all(): array
    {
        $categories = [];

        if (isset($this->data['uncategorized'])) {
            $categories[] = $this->data['uncategorized'];
        }

        foreach ($this->data['categories'] ?? [] as $category) {
            $categories[] = $category;
        }

        return array_merge(...$categories);
    }
}
