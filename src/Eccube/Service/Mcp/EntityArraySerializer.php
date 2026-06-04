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

use Doctrine\Common\Collections\Collection;

/**
 * Doctrine Entity を `AllowListResolver` の公開プロパティリストに従って配列化する。
 *
 * - 出力フィールドは allow_list に列挙された項目のみ (未列挙は構造上含まれない)
 * - スカラはそのまま、`\DateTimeInterface` は ISO 8601、Collection / Entity は再帰
 * - 深さ上限 (既定 2) を超えた関連は `id` のみの要約に縮退
 * - 循環参照は `spl_object_id` のセットで検知し要約に切り替える
 *
 * 「allow_list 未登録の Entity」が現れた場合の出力は空配列 (= 全プロパティ非公開)。
 * 設計の最小権限の既定 (Api44 未配線時は何も公開しない) と一致する挙動。
 */
final readonly class EntityArraySerializer
{
    public const DEFAULT_MAX_DEPTH = 2;

    public function __construct(
        private AllowListResolver $allowListResolver,
    ) {
    }

    /**
     * Entity を allow_list に従って `array<string, mixed>` に変換する。
     *
     * @return array<string, mixed>
     */
    public function toArray(object $entity, int $maxDepth = self::DEFAULT_MAX_DEPTH): array
    {
        $visited = [];

        return $this->convertEntity($entity, 0, $maxDepth, $visited);
    }

    /**
     * 複数 Entity をまとめて変換する。
     *
     * @param iterable<object> $entities
     *
     * @return list<array<string, mixed>>
     */
    public function toArrayList(iterable $entities, int $maxDepth = self::DEFAULT_MAX_DEPTH): array
    {
        $result = [];
        foreach ($entities as $entity) {
            $result[] = $this->toArray($entity, $maxDepth);
        }

        return $result;
    }

    /**
     * @param array<int, bool> $visited spl_object_id をキーにした訪問済みセット
     *
     * @return array<string, mixed>
     */
    private function convertEntity(object $entity, int $depth, int $maxDepth, array &$visited): array
    {
        $oid = spl_object_id($entity);
        if (isset($visited[$oid])) {
            // 循環: 要約のみ返す。 深さ判定より優先する。
            return $this->summarize($entity);
        }
        $visited[$oid] = true;

        $allowedProps = $this->allowListResolver->getAllowedProperties($entity::class);
        $result = [];
        foreach ($allowedProps as $prop) {
            $value = $this->readProperty($entity, $prop);
            $result[$prop] = $this->convertValue($value, $depth + 1, $maxDepth, $visited);
        }

        unset($visited[$oid]);

        return $result;
    }

    /**
     * @param array<int, bool> $visited
     */
    private function convertValue(mixed $value, int $depth, int $maxDepth, array &$visited): mixed
    {
        if (null === $value || \is_scalar($value)) {
            return $value;
        }
        if ($value instanceof \DateTimeInterface) {
            return $value->format(\DateTimeInterface::ATOM);
        }
        if ($value instanceof Collection || (\is_iterable($value) && !\is_array($value))) {
            if ($depth > $maxDepth) {
                return $this->summarizeMany($value);
            }
            $items = [];
            foreach ($value as $element) {
                $items[] = $this->convertValue($element, $depth, $maxDepth, $visited);
            }

            return $items;
        }
        if (\is_array($value)) {
            $items = [];
            foreach ($value as $k => $v) {
                $items[$k] = $this->convertValue($v, $depth, $maxDepth, $visited);
            }

            return $items;
        }
        if (\is_object($value)) {
            if ($depth > $maxDepth) {
                return $this->summarize($value);
            }

            return $this->convertEntity($value, $depth, $maxDepth, $visited);
        }

        return null;
    }

    /**
     * 深さ超過 / 循環時の要約: `id` を出せれば出す、それだけ。
     *
     * @return array<string, mixed>
     */
    private function summarize(object $entity): array
    {
        if (method_exists($entity, 'getId')) {
            try {
                $id = $entity->getId();
                if (null !== $id) {
                    return ['id' => $id];
                }
            } catch (\Throwable) {
                // 何らかの理由で id を取れない場合は空要約。
            }
        }

        return [];
    }

    /**
     * @param iterable<mixed> $collection
     *
     * @return list<array<string, mixed>>
     */
    private function summarizeMany(iterable $collection): array
    {
        $items = [];
        foreach ($collection as $element) {
            if (\is_object($element)) {
                $items[] = $this->summarize($element);
            }
        }

        return $items;
    }

    /**
     * `name01` → `getName01()`、 `order_no` → `getOrderNo()`、 `OrderItems` → `getOrderItems()` の規則で値を取得する。
     *
     * getter が見つからない場合は `is*` を試し、 最後に Reflection でプロパティを直接読む。
     */
    private function readProperty(object $entity, string $propertyName): mixed
    {
        foreach ([$this->buildAccessor('get', $propertyName), $this->buildAccessor('is', $propertyName)] as $method) {
            if (method_exists($entity, $method) && \is_callable([$entity, $method])) {
                try {
                    return $entity->{$method}();
                } catch (\Throwable) {
                    return null;
                }
            }
        }

        try {
            $reflection = new \ReflectionObject($entity);
            if ($reflection->hasProperty($propertyName)) {
                $prop = $reflection->getProperty($propertyName);
                if ($prop->isInitialized($entity)) {
                    return $prop->getValue($entity);
                }
            }
        } catch (\Throwable) {
            // ignore
        }

        return null;
    }

    private function buildAccessor(string $prefix, string $propertyName): string
    {
        $parts = explode('_', $propertyName);

        return $prefix.implode('', array_map(ucfirst(...), $parts));
    }
}
