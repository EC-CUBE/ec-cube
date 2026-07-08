<?php

declare(strict_types=1);

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

namespace Eccube\Tests\Service\Mcp;

use Eccube\Service\Mcp\McpSummaryFields;
use PHPUnit\Framework\TestCase;

/**
 * `McpSummaryFields` の定義が構造的に妥当であることを検証する。
 *
 * 実際の allow_list との整合 (許可外は出力しない) は `EntityArraySerializer::toSummary` の
 * fail-closed が担保し、 tool の DB テストで実データ上も subset であることを確認する。 ここでは
 * 「非空・全て文字列・ドット path は 1 段のみ」という定義側の形式だけを守る。
 */
final class McpSummaryFieldsTest extends TestCase
{
    /**
     * @return iterable<string, array{list<string>}>
     */
    public static function summaryDefinitions(): iterable
    {
        yield 'order' => [McpSummaryFields::ORDER];
        yield 'customer' => [McpSummaryFields::CUSTOMER];
        yield 'product' => [McpSummaryFields::PRODUCT];
    }

    /**
     * @param list<string> $fields
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('summaryDefinitions')]
    public function testDefinitionsAreWellFormed(array $fields): void
    {
        $this->assertNotEmpty($fields);
        foreach ($fields as $field) {
            $this->assertNotSame('', $field);
            // ドット path は 1 段まで (Rel.prop)。 多段ネストはサマリでは扱わない
            $this->assertLessThanOrEqual(1, substr_count($field, '.'), sprintf('"%s" は多段ドット path', $field));
        }
    }
}
