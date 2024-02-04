<?php

namespace App\Assert;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @phpstan-type Context array<string, mixed>
 */
final class AssertionFailed extends \RuntimeException
{
    private const MAX_STR_LENGTH = 100;

    /**
     * @param Context $context
     */
    public function __construct(string $message, public readonly array $context = [], ?\Throwable $previous = null)
    {
        // normalize context into scalar values
        $normalizedContext = array_map([self::class, 'normalizeContextValue'], $context);

        parent::__construct(strtr($message, self::convertToReplaceArray($normalizedContext)), 0, $previous);
    }

    public function __invoke(): void
    {
        throw $this;
    }

    /**
     * @param Context $context
     */
    public static function throw(string $message, array $context = [], ?\Throwable $previous = null): self
    {
        throw new self($message, $context, $previous);
    }

    /**
     * @param array<string,string> $context
     *
     * @return array<string,string>
     */
    private static function convertToReplaceArray(array $context): array
    {
        $replace = [];

        foreach ($context as $key => $value) {
            if (!preg_match('#^{.+}$#', $key)) {
                $key = "{{$key}}";
            }

            $replace[$key] = $value;
        }

        return $replace;
    }

    private static function normalizeContextValue(mixed $value): string
    {
        if (\is_object($value)) {
            return $value::class;
        }

        if (\is_array($value) && !$value) {
            return '(array:empty)';
        }

        if (\is_array($value)) {
            return array_is_list($value) ? '(array:list)' : '(array:assoc)';
        }

        if (!\is_scalar($value)) {
            return sprintf('(%s)', get_debug_type($value));
        }

        if (\is_bool($value)) {
            return sprintf('(%s)', var_export($value, true));
        }

        if (!\is_string($value)) {
            return (string) $value;
        }

        $value = (string) preg_replace('/\s+/', ' ', $value);

        if (mb_strlen($value) <= self::MAX_STR_LENGTH) {
            return sprintf('"%s"', $value);
        }

        // shorten to max
        return sprintf('"%s...%s"', mb_substr($value, 0, self::MAX_STR_LENGTH - 40 - 3), mb_substr($value, -40));
    }
}
