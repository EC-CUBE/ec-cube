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
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;

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
    /**
     * @var string
     */
    protected $field;
    /**
     * @var string
     */
    protected $type;
    /**
     * @var Node|string
     */
    protected $source;

    /**
     * @var string[]
     */
    protected $formats = [
        'YEAR' => '%Y',
        'MONTH' => '%m',
        'DAY' => '%d',
        'HOUR' => '%H',
        'MINUTE' => '%M',
        'SECOND' => '%S',
        'WEEK' => '%W',
    ];
    /**
     * @var string[]
     */
    protected $dateTimeTypes = [
        'TIMESTAMP',
        'DATE',
        'TIME',
    ];

    /**
     * @throws QueryException
     */
    #[\Override]
    public function parse(Parser $parser): void
    {
        $lexer = $parser->getLexer();
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);

        $parser->match(TokenType::T_IDENTIFIER);        // ★ MONTH / YEAR / ...
        $upperField = strtoupper((string) $lexer->token->value);
        if ($lexer->token->type !== TokenType::T_IDENTIFIER || !isset($this->formats[$upperField])) {
            $parser->syntaxError(implode('/', array_keys($this->formats)));
        }

        $this->field = $upperField;
        $parser->match(TokenType::T_FROM);

        $next = $lexer->glimpse();
        if (isset($next->type) && $next->type === TokenType::T_STRING) {
            $upperType = strtoupper((string) $lexer->token->value);
            if (!in_array($upperType, $this->dateTimeTypes, true)) {
                $parser->syntaxError(implode('/', $this->dateTimeTypes));
            }
            $parser->match(TokenType::T_IDENTIFIER);
            $this->type = $upperType;
        }

        $this->source = $parser->ArithmeticPrimary();
        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }

    #[\Override]
    public function getSql(SqlWalker $sqlWalker): string
    {
        $driver = $sqlWalker->getConnection()->getDriver()->getDatabasePlatform()->getName();
        // UTCとの時差(秒数)
        $diff = intval(date('Z'));
        $second = abs($diff);
        $op = ($diff === $second) ? '+' : '-';

        $sql = match ($driver) {
            'sqlite' => sprintf(
                "CAST(STRFTIME('%s', DATETIME(%s, '{$op}{$second} SECONDS')) AS INTEGER)",
                $this->formats[$this->field],
                $this->source->dispatch($sqlWalker)),
            'postgresql' => sprintf(
                "EXTRACT(%s FROM %s %s $op INTERVAL '$second SECONDS')",
                $this->field,
                (string) $this->type,
                $this->source->dispatch($sqlWalker)),
            default => sprintf(
                "EXTRACT(%s FROM %s %s $op INTERVAL $second SECOND)",
                $this->field,
                (string) $this->type,
                $this->source->dispatch($sqlWalker)),
        };

        return $sql;
    }
}
