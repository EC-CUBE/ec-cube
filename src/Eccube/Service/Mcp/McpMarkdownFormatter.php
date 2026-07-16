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

namespace Eccube\Service\Mcp;

/**
 * MCP ツールの返り値 (連想配列) を人間 / LLM が読みやすい Markdown へ整形する。
 *
 * 2 つの形を扱う:
 * - 一覧系 (`items` を持つ) → メタ情報の見出し行 + items の表
 * - 詳細系 (それ以外) → 見出し + 定義リスト (ネストした配列は小見出し + 表 / 箇条書き)
 */
final class McpMarkdownFormatter
{
    /**
     * @param array<string, mixed> $result
     */
    public function format(array $result): string
    {
        if (isset($result['items']) && \is_array($result['items'])) {
            return $this->formatList($result);
        }

        return $this->formatDetail($result);
    }

    /**
     * @param array<string, mixed> $result
     */
    private function formatList(array $result): string
    {
        /** @var list<mixed> $items */
        $items = array_values((array) $result['items']);

        // items 以外: スカラーはメタ情報 (total / limit / offset 等)、 非スカラーはセクションにする
        // (list 経路でも非スカラーの兄弟キーを脱落させない)。
        $meta = [];
        $sections = [];
        foreach ($result as $key => $value) {
            if ('items' === $key) {
                continue;
            }
            if ($this->isScalar($value)) {
                $meta[] = '**'.$key.'**: '.$this->scalarToString($value);
            } else {
                $sections[] = $this->renderSection((string) $key, $value);
            }
        }

        $blocks = [];
        if ([] !== $meta) {
            $blocks[] = implode(' ・ ', $meta);
        }
        $blocks[] = [] === $items ? '該当なし' : $this->renderTable($items);
        foreach ($sections as $section) {
            $blocks[] = $section;
        }

        return implode("\n\n", $blocks);
    }

    /**
     * @param array<string, mixed> $result
     */
    private function formatDetail(array $result): string
    {
        $scalarLines = [];
        $sections = [];

        foreach ($result as $key => $value) {
            if ($this->isScalar($value)) {
                $scalarLines[] = '- **'.$key.'**: '.$this->scalarToString($value);
            } else {
                $sections[] = $this->renderSection((string) $key, $value);
            }
        }

        $blocks = [];
        if ([] !== $scalarLines) {
            $blocks[] = implode("\n", $scalarLines);
        }
        foreach ($sections as $section) {
            $blocks[] = $section;
        }

        return implode("\n\n", $blocks);
    }

    /**
     * ネストした値を小見出し付きセクションにする: オブジェクト配列 → 表、 単一オブジェクト → 箇条書き、 空 → (なし)。
     */
    private function renderSection(string $key, mixed $value): string
    {
        $array = (array) $value;
        if ([] === $array) {
            return '### '.$key."\n".'(なし)';
        }
        if ($this->isListOfObjects($array)) {
            return '### '.$key."\n".$this->renderTable(array_values($array));
        }

        return '### '.$key."\n".$this->renderKeyValueList($array);
    }

    /**
     * オブジェクトの list を Markdown 表にする。 列は全行キーの和集合 (行ごとにキーが異なっても列落ちさせない)。
     *
     * @param list<mixed> $rows
     */
    private function renderTable(array $rows): string
    {
        $first = $rows[0];
        if (!\is_array($first)) {
            // スカラーの list はそのまま箇条書き
            return implode("\n", array_map(fn ($v): string => '- '.$this->cellToString($v), $rows));
        }

        // 列は全行キーの和集合 (行ごとにキーが異なっても列落ちさせない)。
        $columnSet = [];
        foreach ($rows as $row) {
            if (\is_array($row)) {
                foreach (array_keys($row) as $column) {
                    $columnSet[$column] = true;
                }
            }
        }
        $columns = array_keys($columnSet);
        $header = '| '.implode(' | ', $columns).' |';
        $separator = '| '.implode(' | ', array_fill(0, \count($columns), '---')).' |';

        $body = [];
        foreach ($rows as $row) {
            $cells = [];
            foreach ($columns as $column) {
                $cells[] = $this->cellToString(\is_array($row) ? ($row[$column] ?? null) : null);
            }
            $body[] = '| '.implode(' | ', $cells).' |';
        }

        return implode("\n", array_merge([$header, $separator], $body));
    }

    /**
     * @param array<string, mixed> $data
     */
    private function renderKeyValueList(array $data): string
    {
        $lines = [];
        foreach ($data as $key => $value) {
            $lines[] = '- **'.$key.'**: '.$this->cellToString($value);
        }

        return implode("\n", $lines);
    }

    /**
     * 表のセル 1 個を 1 行文字列にする。 パイプと改行は表を壊すのでエスケープ / 空白化する。
     */
    private function cellToString(mixed $value): string
    {
        if ($this->isScalar($value)) {
            return $this->escapePipe($this->scalarToString($value));
        }

        $array = (array) $value;
        // {min, max} は価格 / 在庫の集約なので "min – max" 表記にする。 在庫は unlimited フラグを持つ。
        // min / max がスカラーのときだけレンジ扱いし、 そうでなければ下の JSON 経路に落とす。
        if (\array_key_exists('min', $array) && \array_key_exists('max', $array)
            && $this->isScalar($array['min']) && $this->isScalar($array['max'])) {
            $unlimited = !empty($array['unlimited']);
            if (null === $array['min'] && null === $array['max']) {
                return $unlimited ? '無制限' : '';
            }
            $range = $this->scalarToString($array['min']).' – '.$this->scalarToString($array['max']);

            return $this->escapePipe($unlimited ? $range.' (一部無制限)' : $range);
        }

        // それ以外のネストは 1 行 JSON でコンパクトに。 エンコード失敗は空セルに化けさせず可視化する。
        $json = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return $this->escapePipe(false === $json ? '(encode error)' : $json);
    }

    private function isScalar(mixed $value): bool
    {
        return null === $value || \is_scalar($value);
    }

    private function scalarToString(mixed $value): string
    {
        if (null === $value) {
            return '';
        }
        if (\is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        return (string) $value;
    }

    /**
     * 連想でない (0..n の list) かつ全要素が配列なら「オブジェクトの list」とみなす。
     *
     * @param array<array-key, mixed> $array
     */
    private function isListOfObjects(array $array): bool
    {
        if (!array_is_list($array)) {
            return false;
        }
        foreach ($array as $element) {
            if (!\is_array($element)) {
                return false;
            }
        }

        return true;
    }

    private function escapePipe(string $value): string
    {
        return str_replace(['|', "\n"], ['\\|', ' '], $value);
    }
}
