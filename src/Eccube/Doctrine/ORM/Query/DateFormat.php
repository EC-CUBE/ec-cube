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
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;

/**
 * DATEFORMAT(date, format)
 *  date:
 *      日付/時刻データ型
 *  format:
 *      フォーマット文字列 (例: 'Y/m/d', 'Y/m')
 */
class DateFormat extends FunctionNode
{
    protected Node|string|null $date = null;
    protected Node|string|null $format = null;

    /**
     * PHPフォーマットをデータベースフォーマットに変換するマッピング
     *
     * @var array<string, array<string, string>>
     */
    protected array $formatMap = [
        'mysql' => [
            'Y/m/d' => '%Y/%m/%d',
            'Y/m' => '%Y/%m',
            'Y-m-d' => '%Y-%m-%d',
            'Y-m' => '%Y-%m',
        ],
        'postgresql' => [
            'Y/m/d' => 'YYYY/MM/DD',
            'Y/m' => 'YYYY/MM',
            'Y-m-d' => 'YYYY-MM-DD',
            'Y-m' => 'YYYY-MM',
        ],
        'sqlite' => [
            'Y/m/d' => '%Y/%m/%d',
            'Y/m' => '%Y/%m',
            'Y-m-d' => '%Y-%m-%d',
            'Y-m' => '%Y-%m',
        ],
    ];

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);

        // 第1引数: 日付
        $this->date = $parser->ArithmeticPrimary();
        $parser->match(TokenType::T_COMMA);

        // 第2引数: フォーマット文字列
        $this->format = $parser->ArithmeticPrimary();

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        $driver = $sqlWalker->getConnection()->getDriver()->getDatabasePlatform()->getName();
        $dateField = $this->date->dispatch($sqlWalker);
        $formatValue = $this->format->dispatch($sqlWalker);

        // フォーマット文字列から引用符を除去
        $phpFormat = trim($formatValue, "'\"");

        // データベースプラットフォームに応じたフォーマットに変換
        switch ($driver) {
            case 'sqlite':
                $dbFormat = $this->convertFormat($phpFormat, 'sqlite');
                // SQLiteの場合、DATETIME()でカラム値を正規化してからSTRFTIME()を適用
                // これにより、タイムゾーン付き日時文字列が正しく処理される
                $sql = sprintf("STRFTIME('%s', DATETIME(%s))", $dbFormat, $dateField);
                break;
            case 'postgresql':
                $dbFormat = $this->convertFormat($phpFormat, 'postgresql');
                $sql = sprintf("TO_CHAR(%s, '%s')", $dateField, $dbFormat);
                break;
            case 'mysql':
            default:
                $dbFormat = $this->convertFormat($phpFormat, 'mysql');
                $sql = sprintf("DATE_FORMAT(%s, '%s')", $dateField, $dbFormat);
                break;
        }

        return $sql;
    }

    /**
     * PHPフォーマットをデータベース固有のフォーマットに変換
     *
     * @param string $phpFormat PHPの日付フォーマット
     * @param string $driver データベースドライバ名
     *
     * @return string データベース固有のフォーマット
     */
    protected function convertFormat(string $phpFormat, string $driver): string
    {
        // マッピングにない場合はデフォルトのフォーマットを返す
        // MySQL形式をデフォルトとする
        return $this->formatMap[$driver][$phpFormat] ?? $this->formatMap['mysql'][$phpFormat] ?? '%Y/%m/%d';
    }
}
