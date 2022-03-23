<?php

namespace App\Factory;

use App\Entity\Translation;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Translation>
 *
 * @method static Translation|Proxy createOne(array $attributes = [])
 * @method static Translation[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Translation|Proxy find(object|array|mixed $criteria)
 * @method static Translation|Proxy findOrCreate(array $attributes)
 * @method static Translation|Proxy first(string $sortedField = 'id')
 * @method static Translation|Proxy last(string $sortedField = 'id')
 * @method static Translation|Proxy random(array $attributes = [])
 * @method static Translation|Proxy randomOrCreate(array $attributes = [])
 * @method static Translation[]|Proxy[] all()
 * @method static Translation[]|Proxy[] findBy(array $attributes)
 * @method static Translation[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Translation[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static RepositoryProxy repository()
 * @method Translation|Proxy create(array|callable $attributes = [])
 */
final class TranslationFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            'value' => self::faker()->text(),
        ];
    }

    protected static function getClass(): string
    {
        return Translation::class;
    }
}
