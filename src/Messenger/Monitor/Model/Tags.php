<?php

namespace App\Messenger\Monitor\Model;

use App\Messenger\Monitor\Stamp\Tag;
use Symfony\Component\Messenger\Envelope;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @implements \IteratorAggregate<string>
 */
final class Tags implements \IteratorAggregate, \Countable
{
    /** @var string[] */
    private array $tags;

    /**
     * @param string[]|string|null $tags
     */
    public function __construct(array|string|null $tags = [])
    {
        if (null === $tags) {
            $tags = [];
        }

        if (\is_string($tags)) {
            $tags = \explode(',', $tags);
        }

        $this->tags = \array_values(\array_filter(\array_unique(\array_map('trim', $tags))));
    }

    public function __toString(): string
    {
        return (string) $this->implode();
    }

    public static function from(Envelope $envelope): self
    {
        $tags = [];

        foreach ((new \ReflectionClass($envelope->getMessage()))->getAttributes(Tag::class, \ReflectionAttribute::IS_INSTANCEOF) as $stamp) {
            $tags[] = $stamp->newInstance()->tags;
        }

        foreach ($envelope->all(Tag::class) as $stamp) {
            $tags[] = $stamp->tags;
        }

        return new self(\array_merge(...$tags));
    }

    public function implode(string $separator = ','): ?string
    {
        return $this->tags ? \implode($separator, $this->tags) : null;
    }

    /**
     * @return string[]
     */
    public function all(): array
    {
        return $this->tags;
    }

    public function expand(): self
    {
        $clone = clone $this;

        $clone->tags = \array_merge(
            ...\array_map(
                static function (string $tag): array {
                    $parts = \explode(':', $tag);

                    return \array_map(
                        static fn (int $i) => \implode(':', \array_slice($parts, 0, $i + 1)),
                        \array_keys($parts)
                    );
                },
                $this->tags
            )
        );

        return $clone;
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->tags);
    }

    public function count(): int
    {
        return \count($this->tags);
    }
}
