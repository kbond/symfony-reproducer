<?php

namespace App\Routing\Twig;

use App\Routing\ObjectUrlGenerator;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[AutoconfigureTag('twig.runtime')]
final class ObjectUrlGeneratorRuntime
{
    public function __construct(private ObjectUrlGenerator $generator)
    {
    }

    public function generatePath(string|object $object, array|string|null $type = null, array $parameters = []): string
    {
        return $this->generate(ObjectUrlGenerator::ABSOLUTE_PATH, $object, $type, $parameters);
    }

    public function generateUrl(string|object $object, array|string|null $type = null, array $parameters = []): string
    {
        return $this->generate(ObjectUrlGenerator::ABSOLUTE_URL, $object, $type, $parameters);
    }

    private function generate(int $referenceType, string|object $object, array|string|null $type = null, array $parameters = []): string
    {
        if (\is_array($type)) {
            $parameters = $type;
            $type = null;
        }

        return $this->generator->generate($object, $type, $parameters, $referenceType);
    }
}
