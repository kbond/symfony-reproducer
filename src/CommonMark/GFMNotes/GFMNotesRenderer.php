<?php

namespace App\CommonMark\GFMNotes;

use League\CommonMark\Extension\CommonMark\Renderer\Block\BlockQuoteRenderer;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class GFMNotesRenderer implements NodeRendererInterface
{
    private const NOTE_TYPES = [
        'NOTE',
        'TIP',
        'IMPORTANT',
        'WARNING',
        'CAUTION',
    ];

    private BlockQuoteRenderer $baseRenderer;

    public function __construct()
    {
        $this->baseRenderer = new BlockQuoteRenderer();
    }

    public function render(Node $node, ChildNodeRendererInterface $childRenderer)
    {
        if (!$parsed = $this->parseBlockQuote($node)) {
            return $this->baseRenderer->render($node, $childRenderer);
        }

        [$textNode, $type] = $parsed;

        $textNode->detach();

        $p = new Paragraph();
        $p->data->set('attributes', ['class' => 'markdown-note-label']);
        $p->appendChild(new Text(ucfirst($type)));

        $node->prependChild($p);
        $node->data->set('attributes', ['class' => sprintf('markdown-note markdown-note-%s', $type)]);

        return $this->baseRenderer->render($node, $childRenderer);
    }

    /**
     * @return array{0:Text,1:string}|null
     */
    private function parseBlockQuote(Node $node): ?array
    {
        $textNode = $node->firstChild()?->firstChild();

        if (!$textNode instanceof Text || !preg_match('#^\[!([A-Z]+)]$#', $textNode->getLiteral(), $matches)) {
            return null;
        }

        $type = $matches[1];

        if (!in_array($type, self::NOTE_TYPES, true)) {
            return null;
        }

        return [$textNode, strtolower($type)];
    }
}
