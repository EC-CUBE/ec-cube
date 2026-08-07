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
 * Api44 (`ec-cube/api44`) の allow_list (`eccube.api.allow_list` タグ) を集めて、
 * 「Entity FQCN → 公開プロパティ名のリスト」へ正規化するヘルパ。
 *
 * MCP の Tool 群は本クラス経由でフィールド名を取得し、`EntityArraySerializer` が値を変換する。
 * Api44 が未インストール / 無効化されている場合は allow_list が 0 件、すなわち
 * 全 Entity が「公開なし」となり、Tool 出力は空になる (安全側挙動)。
 *
 * Api44 の `Plugin\Api44\GraphQL\AllowList` クラスは `$allows` を private で保持しているため、
 * 現状は ReflectionObject で読み取っている。 api44 側に `getAllowed()` 系の公開 API を
 * 追加する PR を将来投げる予定 (設計 §5 / §7.3 参照)。
 */
final class AllowListResolver
{
    /**
     * 集約済みの「Entity FQCN → 公開プロパティ名のリスト」。
     *
     * @var array<string, list<string>>
     */
    private array $merged = [];

    /**
     * @param iterable<object> $allowLists `eccube.api.allow_list` タグ付きサービスの一覧
     */
    public function __construct(iterable $allowLists)
    {
        foreach ($allowLists as $allowList) {
            $this->mergeFrom($allowList);
        }
    }

    /**
     * 指定 Entity に対して公開可能なプロパティ名の一覧を返す。
     *
     * Api44 が未配線 / 当該 Entity が allow_list に登録されていない場合は空配列を返す。
     *
     * @return list<string>
     */
    public function getAllowedProperties(string $entityFqcn): array
    {
        return $this->merged[$entityFqcn] ?? [];
    }

    /**
     * 単一プロパティが公開可能かを判定する。
     */
    public function isAllowed(string $entityFqcn, string $propertyName): bool
    {
        return \in_array($propertyName, $this->getAllowedProperties($entityFqcn), true);
    }

    /**
     * allow_list 1 件分を `$merged` に追記する。 同一 FQCN は集約 (union)。
     */
    private function mergeFrom(object $allowList): void
    {
        $raw = $this->extractAllows($allowList);
        foreach ($raw as $fqcn => $props) {
            if (!\is_string($fqcn) || !\is_array($props)) {
                continue;
            }
            $existing = $this->merged[$fqcn] ?? [];
            /** @var list<string> $merged */
            $merged = array_values(array_unique([...$existing, ...array_filter($props, is_string(...))]));
            $this->merged[$fqcn] = $merged;
        }
    }

    /**
     * Api44 の `Plugin\Api44\GraphQL\AllowList::$allows` (private) を抽出する。
     *
     * 元のサービス定義 (services.yaml) では `ArrayObject` を引数 1 個で受けて生成され、
     * Api44 の Compiler Pass がクラスを `AllowList` に書き換える。 取り出した値は
     * 通常 `array` だが、`ArrayObject` のままになる経路にも備える。
     *
     * 戻り値の型は意図的に `array<mixed, mixed>` まで緩め、 値側の型検証は `mergeFrom()`
     * に委ねる (allow_list は外部設定由来でデータ形が必ずしも保証されないため)。
     *
     * @return array<mixed, mixed>
     */
    private function extractAllows(object $allowList): array
    {
        $reflection = new \ReflectionObject($allowList);
        if (!$reflection->hasProperty('allows')) {
            return [];
        }
        $prop = $reflection->getProperty('allows');
        $value = $prop->getValue($allowList);

        if (\is_array($value)) {
            return $value;
        }
        if ($value instanceof \ArrayObject) {
            return $value->getArrayCopy();
        }

        return [];
    }
}
