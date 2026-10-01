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

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;

/**
 * DATEFORMAT(source, 'format')
 *  source:
 *      日付/時刻データ型 (datetimetz カラムを想定)
 *  format:
 *      PHP の日付フォーマットの文字列リテラル (self::FORMATS のキーのみ)
 *
 * UTCDateTimeType / UTCDateTimeTzType は値を UTC に変換して保存し、
 * InitDriver が MySQL / PostgreSQL のセッションのタイムゾーンを UTC に固定するため、
 * アプリケーションのタイムゾーンとの時差を加えてから整形する (Extract と同じ方式)。
 * 時差は SQL 生成時点の値を固定で使うため、夏時間のあるタイムゾーンでは境界付近の日付がずれる。
 */
class DateFormat extends FunctionNode
{
    /**
     * PHP の日付フォーマット → 各 DB の書式指定子
     *
     * @var array<string, array{mysql: string, postgresql: string, sqlite: string}>
     */
    public const FORMATS = [
        'Y/m/d' => ['mysql' => '%Y/%m/%d', 'postgresql' => 'YYYY/MM/DD', 'sqlite' => '%Y/%m/%d'],
        'Y/m' => ['mysql' => '%Y/%m', 'postgresql' => 'YYYY/MM', 'sqlite' => '%Y/%m'],
        'Y-m-d' => ['mysql' => '%Y-%m-%d', 'postgresql' => 'YYYY-MM-DD', 'sqlite' => '%Y-%m-%d'],
        'Y-m' => ['mysql' => '%Y-%m', 'postgresql' => 'YYYY-MM', 'sqlite' => '%Y-%m'],
    ];

    protected Node|string $source;
    protected string $format;

    /**
     * @throws QueryException
     */
    #[\Override]
    public function parse(Parser $parser): void
    {
        $lexer = $parser->getLexer();
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);

        $this->source = $parser->ArithmeticPrimary();
        $parser->match(TokenType::T_COMMA);

        $parser->match(TokenType::T_STRING);
        $format = (string) $lexer->token->value;
        if (!isset(self::FORMATS[$format])) {
            $parser->syntaxError(implode('/', array_keys(self::FORMATS)));
        }
        $this->format = $format;

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }

    #[\Override]
    public function getSql(SqlWalker $sqlWalker): string
    {
        $platform = $sqlWalker->getConnection()->getDatabasePlatform();
        $source = $this->source->dispatch($sqlWalker);
        // UTCとの時差(秒数)
        $diff = intval(date('Z'));
        $second = abs($diff);
        $op = ($diff === $second) ? '+' : '-';

        return match (true) {
            $platform instanceof SQLitePlatform => sprintf(
                "STRFTIME('%s', DATETIME(%s, '{$op}{$second} SECONDS'))",
                self::FORMATS[$this->format]['sqlite'],
                $source),
            $platform instanceof PostgreSQLPlatform => sprintf(
                "TO_CHAR(%s $op INTERVAL '$second SECONDS', '%s')",
                $source,
                self::FORMATS[$this->format]['postgresql']),
            default => sprintf(
                "DATE_FORMAT(%s $op INTERVAL $second SECOND, '%s')",
                $source,
                self::FORMATS[$this->format]['mysql']),
        };
    }
}
