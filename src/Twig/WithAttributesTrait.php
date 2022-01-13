<?php

namespace App\Twig;

use Symfony\UX\TwigComponent\Attribute\PreMount;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
trait WithAttributesTrait
{
    public AttributeBag $attributes;

    #[PreMount]
    public function mapAttributes(array $data): array
    {
        $data['attributes'] = AttributeBag::create($data['attributes'] ?? []);

        return $data;
    }
}
