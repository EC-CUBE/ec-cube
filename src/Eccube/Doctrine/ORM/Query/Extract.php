<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2016 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Eccube\Doctrine\ORM\Query;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * OK:
 *  - EXTRACT(DATE FROM p.create_date)
 *  - EXTRACT(YEAR FROM TIMESTAMP '2016-09-29')
 *
 * NG:
 *  - EXTRACT(YEAR_MONTH FROM p.create_date) YEAR_MONTHはPostgreSQLで使えない
 *  - EXTRACT(MONTH FROM INTERVAL '3 MONTHS') INTERVALはMySQLで使えない
 *
 * @package Eccube\Doctrine\ORM\Query
 */
class Extract extends FunctionNode
{
    protected $field;
    protected $type;
    protected $source;

    public function parse(Parser $parser)
    {
        $lexer = $parser->getLexer();
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $parser->match(Lexer::T_IDENTIFIER);
        $this->field = $lexer->token['value'];
        $parser->match(Lexer::T_FROM);

        $next = $lexer->glimpse();
        if (isset($next['type']) && $next['type'] === Lexer::T_STRING) {
            $parser->match(Lexer::T_IDENTIFIER);
            $this->type = $lexer->token['value'];
        }

        $this->source = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker)
    {
        return sprintf('EXTRACT(%s FROM %s %s)', $this->field, (string)$this->type, $this->source->dispatch($sqlWalker));
    }
}