<?php

namespace App\Twig;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\ComponentFactory;
use Symfony\UX\TwigComponent\ComputedPropertiesProxy;
use Twig\Extension\AbstractExtension;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[Autoconfigure(bind: ['$factory' => '@ux.twig_component.component_factory'])]
final class ComponentExtension extends AbstractExtension
{
    public function __construct(private ComponentFactory $factory)
    {
    }

    public function getTokenParsers(): array
    {
        return [
            new ComponentTokenParser($this->factory)
        ];
    }

    public function getComponentContext(string $name, array $with, array $context): array
    {
        $mounted = $this->factory->create($name, $with);
        $component = $mounted->getComponent();
        $metadata = $this->factory->metadataFor($name);

        return array_merge(
            $context, // context first so next arrays can override

            // add the component as "this"
            ['this' => $component],

            // add computed properties proxy
            ['computed' => new ComputedPropertiesProxy($component)],

            // add attributes
            [$metadata->getAttributesVar() => $mounted->getAttributes()],

            // expose public properties and properties marked with ExposeInTemplate attribute
            iterator_to_array($this->exposedVariables($component, $metadata->isPublicPropsExposed())),
        );
    }

    private function exposedVariables(object $component, bool $exposePublicProps): \Iterator
    {
        if ($exposePublicProps) {
            yield from get_object_vars($component);
        }

        $propertyAccessor = new PropertyAccessor();
        $class = new \ReflectionClass($component);

        foreach ($class->getProperties() as $property) {
            if (!$attribute = $property->getAttributes(ExposeInTemplate::class)[0] ?? null) {
                continue;
            }

            $attribute = $attribute->newInstance();

            /** @var ExposeInTemplate $attribute */
            $value = $attribute->getter ? $component->{rtrim($attribute->getter, '()')}() : $propertyAccessor->getValue($component, $property->name);

            yield $attribute->name ?? $property->name => $value;
        }
    }
}
