<?php

namespace App\Twig;

use Twig\Node\Expression\AbstractExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\IncludeNode;
use Twig\Node\Node;

/**
 * @author Giorgio Pogliani
 */
final class ComponentNode extends IncludeNode
{
    public function __construct(string $template, Node $slot, ?AbstractExpression $variables, int $lineno)
    {
        parent::__construct(new ConstantExpression($template, $lineno), $variables, false, false, $lineno);

        $this->setNode('slot', $slot);
    }
}
