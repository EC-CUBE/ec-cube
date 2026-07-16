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

use Mcp\Schema\Tool;

/**
 * MCP ツールの inputSchema (JSON Schema の PHP 配列) を読むための read-model。
 *
 * sdk は引数を持たないツールの `properties` を空配列でなく `\stdClass` で表現する等の癖があり、
 * JSON Schema を生配列のまま各所で掘ると型注釈が実態と乖離する。 本 VO が正規化と
 * 「nullable を外した基底型」 の解釈を 1 箇所に閉じ込め、 利用側 (CLI コマンド) から
 * JSON Schema の知識を排除する。
 */
final readonly class ToolInputSchema
{
    /** @var array<string, mixed> */
    private array $properties;

    /** @var list<string> */
    private array $required;

    public function __construct(Tool $tool)
    {
        // sdk は引数なしツールの properties を空配列でなく \stdClass に正規化する (Tool::normalizeSchemaProperties)。
        // inputSchema の PHPDoc は properties: array と宣言するが実体は array|\stdClass なので、 その型で受けて配列化する。
        /** @var array<string, mixed>|\stdClass $rawProperties */
        $rawProperties = $tool->inputSchema['properties'] ?? [];
        $this->properties = \is_array($rawProperties) ? $rawProperties : [];

        // required は上記の正規化対象外で常に配列 (SDK は required の型を変換しない)。
        $rawRequired = $tool->inputSchema['required'] ?? [];
        $this->required = array_values(array_map(strval(...), $rawRequired));
    }

    /**
     * @return list<string>
     */
    public function propertyNames(): array
    {
        return array_keys($this->properties);
    }

    /**
     * @return list<string>
     */
    public function requiredNames(): array
    {
        return $this->required;
    }

    public function isArray(string $name): bool
    {
        return 'array' === $this->baseType($name);
    }

    /**
     * プロパティの基底型 (nullable を外した型。 例: ['null','integer'] → 'integer')。
     */
    public function baseType(string $name): string
    {
        return $this->normalizeType($this->spec($name)['type'] ?? 'string');
    }

    /**
     * 配列プロパティの要素型 (items.type)。 未指定なら 'string'。
     */
    public function elementType(string $name): string
    {
        $items = $this->spec($name)['items'] ?? [];

        return $this->normalizeType(\is_array($items) ? ($items['type'] ?? 'string') : 'string');
    }

    public function description(string $name): string
    {
        return (string) ($this->spec($name)['description'] ?? '');
    }

    /**
     * 1 プロパティのスキーマ断片を配列として得る。 SDK は各プロパティを JSON オブジェクト (=配列) で
     * 渡すが型を過信せず、 非配列は空スキーマ扱いにして掘り先 (['type'] 等) の TypeError を防ぐ。
     *
     * @return array<string, mixed>
     */
    private function spec(string $name): array
    {
        $spec = $this->properties[$name] ?? null;

        return \is_array($spec) ? $spec : [];
    }

    private function normalizeType(mixed $type): string
    {
        if (\is_array($type)) {
            foreach ($type as $candidate) {
                if ('null' !== $candidate) {
                    return (string) $candidate;
                }
            }

            return 'string';
        }

        return \is_string($type) ? $type : 'string';
    }
}
