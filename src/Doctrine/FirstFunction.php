<?php

namespace App\Doctrine;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Subselect;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * FirstFunction ::= "FIRST" "(" Subselect ")"
 *
 * @author Colin O'Dell
 */
final class FirstFunction extends FunctionNode
{
    private Subselect $subselect;

    public function getSql(SqlWalker $sqlWalker): string
    {
        return sprintf('(%s LIMIT 1)', $this->subselect->dispatch($sqlWalker));
    }

    public function parse(Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->subselect = $parser->Subselect();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
