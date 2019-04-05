<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
 */
class Extract extends FunctionNode
{
    protected $field;
    protected $type;
    protected $source;

    protected $formats = [
        'YEAR' => '%Y',
        'MONTH' => '%m',
        'DAY' => '%d',
        'HOUR' => '%H',
        'MINUTE' => '%M',
        'SECOND' => '%S',
        'WEEK' => '%W',
    ];

    protected $dateTimeTypes = [
        'TIMESTAMP',
        'DATE',
        'TIME',
    ];

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
        // UTCとの時差(秒数)
        $diff = intval(date('Z'));
        $second = abs($diff);
        $op = ($diff === $second) ? '+' : '-';

        switch ($driver) {
            case 'pdo_sqlite':
                $sql = sprintf(
                    "CAST(STRFTIME('%s', DATETIME(%s, '${op}{$second} SECONDS')) AS INTEGER)",
                    $this->formats[$this->field],
                    $this->source->dispatch($sqlWalker));
                break;
            case 'pdo_pgsql':
                $sql = sprintf(
                    "EXTRACT(%s FROM %s %s $op INTERVAL '$second SECONDS')",
                    $this->field,
                    (string) $this->type,
                    $this->source->dispatch($sqlWalker));
                break;
            default:
                $sql = sprintf(
                    "EXTRACT(%s FROM %s %s $op INTERVAL $second SECOND)",
                    $this->field,
                    (string) $this->type,
                    $this->source->dispatch($sqlWalker));
        }

        return $sql;
    }
}
