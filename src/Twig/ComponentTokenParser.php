<?php

namespace App\Twig;

use Symfony\UX\TwigComponent\ComponentFactory;
use Twig\Node\Expression\AbstractExpression;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\NameExpression;
use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\IncludeTokenParser;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ComponentTokenParser extends IncludeTokenParser
{
    public function __construct(private ComponentFactory $factory)
    {
    }

    public function parse(Token $token): Node
    {
        $stream = $this->parser->getStream();
        $parent = $this->parser->getExpressionParser()->parseExpression();
        $componentName = $this->componentName($parent);
        $componentMetadata = $this->factory->metadataFor($componentName);

        list($variables, $only) = $this->parseArguments();

        if (null === $variables) {
            $variables = new ArrayExpression([], $parent->getTemplateLine());
        }

        $parentToken = new Token(Token::STRING_TYPE, $componentMetadata->getTemplate(), $token->getLine());
        $fakeParentToken = new Token(Token::STRING_TYPE, '__parent__', $token->getLine());

        // inject a fake parent to make the parent() function work
        $stream->injectTokens([
            new Token(Token::BLOCK_START_TYPE, '', $token->getLine()),
            new Token(Token::NAME_TYPE, 'extends', $token->getLine()),
            $parentToken,
            new Token(Token::BLOCK_END_TYPE, '', $token->getLine()),
        ]);

        $module = $this->parser->parse($stream, fn(Token $token) => $token->test("end{$this->getTag()}"), true);

        // override the parent with the correct one
        if ($fakeParentToken === $parentToken) {
            $module->setNode('parent', $parent);
        }

        $this->parser->embedTemplate($module);

        $stream->expect(Token::BLOCK_END_TYPE);

        return new ComponentNode($componentName, $module->getTemplateName(), $module->getAttribute('index'), $variables, $only, $token->getLine(), $this->getTag());
    }

    public function getTag(): string
    {
        return 'component';
    }

    private function componentName(AbstractExpression $expression): string
    {
        if ($expression instanceof ConstantExpression) { // using {% component 'name' %}
            return $expression->getAttribute('value');
        }

        if ($expression instanceof NameExpression) { // using {% component name %}
            return $expression->getAttribute('name');
        }

        throw new \LogicException('Could not parse component name.');
    }
}
