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

use Eccube\Service\Mcp\McpMarkdownFormatter;
use PHPUnit\Framework\TestCase;

/**
 * `McpMarkdownFormatter` のユニット検証。 DB / kernel 不要。
 *
 * 一覧 (items) の表化・詳細の定義リスト化・列=全行キー和集合・{min,max} レンジ整形と
 * 非スカラーガード・パイプ/改行エスケープを確認する。
 */
final class McpMarkdownFormatterTest extends TestCase
{
    public function testListRendersMetaAndTable(): void
    {
        $md = $this->format([
            'total' => 2,
            'items' => [['id' => 1, 'name' => 'a'], ['id' => 2, 'name' => 'b']],
        ]);

        $this->assertStringContainsString('**total**: 2', $md);
        $this->assertStringContainsString('| id | name |', $md);
        $this->assertStringContainsString('| 1 | a |', $md);
    }

    public function testTableColumnsAreUnionOfAllRowKeys(): void
    {
        // 回帰: 列は先頭行でなく全行キーの和集合。 後続行のみが持つキーも列落ちしない。
        $md = $this->format(['items' => [['id' => 1], ['id' => 2, 'extra' => 'x']]]);

        $this->assertStringContainsString('| id | extra |', $md);
        $this->assertStringContainsString('| 2 | x |', $md);
    }

    public function testEmptyItemsRendersNoData(): void
    {
        $this->assertStringContainsString('該当なし', $this->format(['items' => []]));
    }

    public function testDetailRendersDefinitionList(): void
    {
        $md = $this->format(['id' => 1, 'name' => 'a']);

        $this->assertStringContainsString('- **id**: 1', $md);
        $this->assertStringContainsString('- **name**: a', $md);
    }

    public function testMinMaxRenderedAsRange(): void
    {
        $md = $this->format(['items' => [['stock' => ['min' => 0, 'max' => 5]]]]);

        $this->assertStringContainsString('0 – 5', $md);
    }

    public function testMinMaxBothNullWithUnlimited(): void
    {
        $md = $this->format(['items' => [['stock' => ['min' => null, 'max' => null, 'unlimited' => true]]]]);

        $this->assertStringContainsString('無制限', $md);
    }

    public function testNonScalarMinMaxFallsBackToJson(): void
    {
        // round2 ガード: min/max が非スカラーならレンジ整形せず JSON 経路へ落とす。
        $md = $this->format(['items' => [['weird' => ['min' => [1, 2], 'max' => 5]]]]);

        $this->assertStringNotContainsString(' – ', $md);
        $this->assertStringContainsString('"min"', $md);
    }

    public function testPipeAndNewlineAreEscaped(): void
    {
        $md = $this->format(['items' => [['note' => "a|b\nc"]]]);

        $this->assertStringContainsString('a\\|b c', $md);
    }

    /**
     * @param array<string, mixed> $result
     */
    private function format(array $result): string
    {
        return (new McpMarkdownFormatter())->format($result);
    }
}
