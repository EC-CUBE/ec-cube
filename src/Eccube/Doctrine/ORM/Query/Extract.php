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
 * EXTRACT (field FROM [type] source)
 *  field:
 *      単位指定子
 *          - YEAR
 *          - MONTH
 *          - DAY
 *          - HOUR
 *          - MINUTE
 *          - SECOND
 *          - WEEK
 *  type:
 *      日付/時刻データ型
 *      sourceが文字列の場合のみ必須
 *          - TIMESTAMP
 *          - DATE
 *          - TIME
 *  source:
 *      日付/時刻データ型、もしくは日付/時刻を表す文字列
 *      文字列の場合はtypeが必須
 *
 * @package Eccube\Doctrine\ORM\Query
 */
class Extract extends FunctionNode
{
    protected $field;
    protected $type;
    protected $source;

    protected $formats = array(
        'YEAR'      => '%Y',
        'MONTH'     => '%m',
        'DAY'       => '%d',
        'HOUR'      => '%H',
        'MINUTE'    => '%M',
        'SECOND'    => '%S',
        'WEEK'      => '%W',
    );

    protected $dateTimeTypes = array(
        'TIMESTAMP',
        'DATE',
        'TIME',
    );

    public function parse(Parser $parser)
    {
        $lexer = $parser->getLexer();
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $upperField = strtoupper($lexer->lookahead['value']);
        if ($lexer->lookahead['type'] !== Lexer::T_IDENTIFIER || !isset($this->formats[$upperField])) {
            $parser->syntaxError(implode('/', array_keys($this->formats)));
        }

        $parser->match(Lexer::T_IDENTIFIER);
        $this->field = $upperField;
        $parser->match(Lexer::T_FROM);

        $next = $lexer->glimpse();
        if (isset($next['type']) && $next['type'] === Lexer::T_STRING) {
            $upperType = strtoupper($lexer->lookahead['value']);
            if ($lexer->lookahead['type'] !== Lexer::T_IDENTIFIER || !in_array($upperType, $this->dateTimeTypes, true)) {
                $parser->syntaxError(implode('/', $this->dateTimeTypes));
            }
            $parser->match(Lexer::T_IDENTIFIER);
            $this->type = $upperType;
        }

        $this->source = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker)
    {
        $driver = $sqlWalker->getConnection()->getDriver()->getName();
        if ($driver == 'pdo_sqlite') {
            return sprintf("CAST(STRFTIME('%s', %s) AS INTEGER)", $this->formats[$this->field], $this->source->dispatch($sqlWalker));
        } else {
            return sprintf('EXTRACT(%s FROM %s %s)', $this->field, (string)$this->type, $this->source->dispatch($sqlWalker));
        }
    }
}