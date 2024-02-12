<?php

namespace App\CommonMark\ShikiHighlighter;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\IndentedCode;
use League\CommonMark\Extension\ExtensionInterface;
use Spatie\CommonMarkShikiHighlighter\Renderers\FencedCodeRenderer;
use Spatie\CommonMarkShikiHighlighter\Renderers\IndentedCodeRenderer;
use Spatie\CommonMarkShikiHighlighter\ShikiHighlighter;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ShikiHighlighterExtension implements ExtensionInterface
{
    private ShikiHighlighter $shikiHighlighter;

    public function __construct(CacheInterface $cache, string $defaultTheme = 'nord')
    {
        $this->shikiHighlighter = new ShikiHighlighter(new CachedShiki($cache, $defaultTheme));
    }

    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment
            ->addRenderer(FencedCode::class, new FencedCodeRenderer($this->shikiHighlighter), 10)
            ->addRenderer(IndentedCode::class, new IndentedCodeRenderer($this->shikiHighlighter), 10)
        ;
    }
}
