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

namespace Eccube\Tests\Skill;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * Skill（AI 向けレイヤ規約）の frontmatter が YAML として健全かを検査する.
 *
 * description はクォートされないプレーンスカラーのため, 値の中に YAML の特殊文字が入ると
 * そこから先が捨てられる. 一覧には切れた description が載り, 失われたトリガ語では
 * 自動発火しなくなる. 本文は Markdown として普通に読めるため, 気づく契機が無い.
 *
 * 実害の例:
 *  - `「PR #XXXX を見て」` の ` #` がコメント開始と解釈され 63% が切り捨てられた
 *  - `注意: ` のコロン+空白がマッピング区切りと解釈されパースエラーになった
 */
#[Group('architecture')]
final class SkillFrontmatterTest extends TestCase
{
    /**
     * @return array<string, array{string}>
     */
    public static function skillProvider(): array
    {
        $root = dirname(__DIR__, 4);
        $cases = [];
        foreach (glob($root.'/.claude/skills/*/SKILL.md') ?: [] as $path) {
            $cases[basename(dirname($path))] = [$path];
        }
        self::assertNotEmpty($cases, 'SKILL.md が 1 件も見つかりません');

        return $cases;
    }

    #[DataProvider(methodName: 'skillProvider')]
    public function testFrontmatterIsValidYaml(string $path): void
    {
        $raw = $this->extractFrontmatter($path);

        try {
            $parsed = Yaml::parse($raw);
        } catch (ParseException $e) {
            self::fail(sprintf(
                "%s の frontmatter が YAML として壊れています: %s\n".
                'description の値に「: 」（コロン+空白）や「 #」（空白+シャープ）を書くと壊れます。'.
                '全角「：」やダッシュ「 — 」に置き換えるか、値全体をダブルクォートで囲んでください。',
                $path, $e->getMessage()
            ));
        }

        $this->assertIsArray($parsed, $path.' の frontmatter が連想配列になっていません');
        $this->assertArrayHasKey('name', $parsed, $path.' に name がありません');
        $this->assertArrayHasKey('description', $parsed, $path.' に description がありません');
    }

    /**
     * description が途中で切り捨てられていないことを, 生の行長とパース後の長さの突合で検査する.
     */
    #[DataProvider(methodName: 'skillProvider')]
    public function testDescriptionIsNotTruncated(string $path): void
    {
        $raw = $this->extractFrontmatter($path);

        $rawLength = 0;
        foreach (explode("\n", $raw) as $line) {
            if (str_starts_with($line, 'description:')) {
                $rawLength = mb_strlen(trim(substr($line, strlen('description:'))));
                break;
            }
        }
        $this->assertGreaterThan(0, $rawLength, $path.' に description 行がありません');

        try {
            $parsed = Yaml::parse($raw);
        } catch (ParseException) {
            self::markTestSkipped($path.' は YAML として壊れています（testFrontmatterIsValidYaml が報告します）');
        }
        $parsedLength = mb_strlen((string) ($parsed['description'] ?? ''));

        $this->assertLessThanOrEqual(3, abs($rawLength - $parsedLength), sprintf(
            "%s の description が YAML パースで切り捨てられています（生 %d 字 → パース後 %d 字）。\n".
            '値の中の「 #」以降がコメント扱いになっている可能性があります。',
            $path, $rawLength, $parsedLength
        ));
    }

    private function extractFrontmatter(string $path): string
    {
        $content = file_get_contents($path);
        $this->assertNotFalse($content, $path.' を読み込めません');

        if (!preg_match("/\A---\n(.*?)\n---\n/s", $content, $m)) {
            self::fail($path.' に frontmatter（--- で囲まれた領域）がありません');
        }

        return $m[1];
    }
}
