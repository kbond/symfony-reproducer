<?php

namespace App\Factory;

use App\Entity\Article;
use Doctrine\ORM\EntityRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;
use function Zenstruck\Foundry\lazy;

/**
 * @extends ModelFactory<Article>
 *
 * @method        Article|Proxy                    create(array|callable $attributes = [])
 * @method static Article|Proxy                    createOne(array $attributes = [])
 * @method static Article|Proxy                    find(object|array|mixed $criteria)
 * @method static Article|Proxy                    findOrCreate(array $attributes)
 * @method static Article|Proxy                    first(string $sortedField = 'id')
 * @method static Article|Proxy                    last(string $sortedField = 'id')
 * @method static Article|Proxy                    random(array $attributes = [])
 * @method static Article|Proxy                    randomOrCreate(array $attributes = [])
 * @method static EntityRepository|RepositoryProxy repository()
 * @method static Article[]|Proxy[]                all()
 * @method static Article[]|Proxy[]                createMany(int $number, array|callable $attributes = [])
 * @method static Article[]|Proxy[]                createSequence(iterable|callable $sequence)
 * @method static Article[]|Proxy[]                findBy(array $attributes)
 * @method static Article[]|Proxy[]                randomRange(int $min, int $max, array $attributes = [])
 * @method static Article[]|Proxy[]                randomSet(int $number, array $attributes = [])
 */
final class ArticleFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            'title' => self::faker()->realTextBetween(5, 20),
            'description' => self::faker()->realTextBetween(20, 100),
            'body' => self::faker()->realTextBetween(100, 1000),
            'publishedAt' => self::faker()->optional()->dateTimeBetween('-1 year'),
            'views' => self::faker()->numberBetween(0, 50000),
        ];
    }

    protected function initialize(): self
    {
        return parent::initialize()
            ->beforeInstantiate(function(array $attributes) {
                if (null === $attributes['publishedAt']) {
                    $attributes['views'] = 0;
                }

                return $attributes;
            })
        ;
    }

    protected static function getClass(): string
    {
        return Article::class;
    }
}
