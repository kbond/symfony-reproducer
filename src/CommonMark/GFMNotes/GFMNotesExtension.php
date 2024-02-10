<?php

namespace App\CommonMark\GFMNotes;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\CommonMark\Node\Block\BlockQuote;
use League\CommonMark\Extension\ExtensionInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class GFMNotesExtension implements ExtensionInterface
{
    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment->addRenderer(BlockQuote::class, new GFMNotesRenderer(), 10);
    }
}
