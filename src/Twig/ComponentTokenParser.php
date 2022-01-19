<?php

namespace App\Twig;

use Symfony\UX\TwigComponent\ComponentFactory;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

/**
 * @author Giorgio Pogliani
 */
final class ComponentTokenParser extends AbstractTokenParser
{
    public function __construct(private ComponentFactory $factory)
    {
    }

    public function parse(Token $token): Node
    {
        $expr = $this->parser->getExpressionParser()->parseExpression();
        $variables = $this->parseArguments();
        $name = $expr->getAttribute('value');

        $slot = $this->parser->subparse(fn() => "end{$this->getTag()}", true);

        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);

        // todo, how to inject component as "this"?
        $component = $this->factory->get($name);
        $template = $this->factory->metadataFor($name)->getTemplate();

        return new ComponentNode($template, $slot, $variables, $token->getLine());
    }

    public function getTag(): string
    {
        return 'component';
    }

    private function parseArguments(): ?ArrayExpression
    {
        $stream = $this->parser->getStream();
        $variables = null;

        if ($stream->nextIf(Token::NAME_TYPE, 'with')) {
            $variables = $this->parser->getExpressionParser()->parseExpression();
        }

        $stream->expect(Token::BLOCK_END_TYPE);

        return $variables;
    }
}
