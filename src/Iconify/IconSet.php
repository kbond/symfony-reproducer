<?php

namespace App\Iconify;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class IconSet
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

    /**
     * @return array<string,string[]>
     */
    public function categories(): array
    {
        $categories = [
            'All' => $this->all(),
        ];

        if (!isset($this->data['categories'])) {
            // there are only uncategorized icons
            return $categories;
        }

        if (isset($this->data['uncategorized'])) {
            $categories['Uncategorized'] = $this->data['uncategorized'];
        }

        foreach ($this->data['categories'] ?? [] as $title => $icons) {
            $categories[$title] = $icons;
        }

        return $categories;
    }

    /**
     * @return string[]
     */
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
