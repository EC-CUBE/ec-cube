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

use Doctrine\Common\Collections\ArrayCollection;
use Eccube\Service\Mcp\AllowListResolver;
use Eccube\Service\Mcp\EntityArraySerializer;
use PHPUnit\Framework\TestCase;

/**
 * `EntityArraySerializer` の挙動をユニットで検証する。 DB 不要。
 *
 * ダミー Entity (`SerializerDummy*`) を使い、 スカラ / DateTime / Collection / 関連 Entity /
 * 深さ制限 / 循環参照 / allow_list 未登録 の各経路を確認する。
 */
final class EntityArraySerializerTest extends TestCase
{
    public function testEmptyWhenNoAllowList(): void
    {
        $serializer = new EntityArraySerializer(new AllowListResolver([]));

        $this->assertSame([], $serializer->toArray(new SerializerDummyEntity()));
    }

    public function testScalarPropertiesPassThrough(): void
    {
        $serializer = $this->serializerWith([
            SerializerDummyEntity::class => ['id', 'name', 'active'],
        ]);

        $entity = new SerializerDummyEntity();
        $entity->id = 42;
        $entity->name = 'foo';
        $entity->active = true;

        $this->assertSame(
            ['id' => 42, 'name' => 'foo', 'active' => true],
            $serializer->toArray($entity),
        );
    }

    public function testDateTimeFormattedAsAtom(): void
    {
        $serializer = $this->serializerWith([SerializerDummyEntity::class => ['createDate']]);
        $entity = new SerializerDummyEntity();
        $entity->createDate = new \DateTime('2026-06-04T12:34:56+09:00');

        $this->assertSame(
            ['createDate' => '2026-06-04T12:34:56+09:00'],
            $serializer->toArray($entity),
        );
    }

    public function testRelatedEntityRecurses(): void
    {
        $serializer = $this->serializerWith([
            SerializerDummyEntity::class => ['id', 'related'],
            SerializerDummyRelated::class => ['code'],
        ]);

        $entity = new SerializerDummyEntity();
        $entity->id = 1;
        $entity->related = new SerializerDummyRelated();
        $entity->related->code = 'X-001';

        $this->assertSame(
            ['id' => 1, 'related' => ['code' => 'X-001']],
            $serializer->toArray($entity),
        );
    }

    public function testCollectionExpanded(): void
    {
        $serializer = $this->serializerWith([
            SerializerDummyEntity::class => ['id', 'items'],
            SerializerDummyRelated::class => ['code'],
        ]);

        $a = new SerializerDummyRelated();
        $a->code = 'A';
        $b = new SerializerDummyRelated();
        $b->code = 'B';

        $entity = new SerializerDummyEntity();
        $entity->id = 1;
        $entity->items = new ArrayCollection([$a, $b]);

        $this->assertSame(
            ['id' => 1, 'items' => [['code' => 'A'], ['code' => 'B']]],
            $serializer->toArray($entity),
        );
    }

    public function testMaxDepthSummarizesDeepRelations(): void
    {
        $serializer = $this->serializerWith([
            SerializerDummyEntity::class => ['related'],
            SerializerDummyRelated::class => ['nested'],
            SerializerDummyNested::class => ['inner'],
            SerializerDummyInner::class => ['id', 'name'],
        ]);

        $inner = new SerializerDummyInner();
        $inner->id = 5;
        $inner->name = 'deep';
        $nested = new SerializerDummyNested();
        $nested->inner = $inner;
        $related = new SerializerDummyRelated();
        $related->nested = $nested;
        $entity = new SerializerDummyEntity();
        $entity->related = $related;

        // maxDepth = 2 → entity(d0) → related(d1) → nested(d2) → inner(d3) は要約 (id のみ)
        $result = $serializer->toArray($entity, maxDepth: 2);

        $this->assertSame(
            ['related' => ['nested' => ['inner' => ['id' => 5]]]],
            $result,
        );
    }

    public function testCircularReferenceSummarized(): void
    {
        $serializer = $this->serializerWith([
            SerializerDummyEntity::class => ['id', 'related'],
            SerializerDummyRelated::class => ['back'],
        ]);

        $entity = new SerializerDummyEntity();
        $entity->id = 7;
        $related = new SerializerDummyRelated();
        $entity->related = $related;
        $related->back = $entity;

        $result = $serializer->toArray($entity);

        // 循環: entity → related → back == entity (visited) → 要約 (id のみ)
        $this->assertSame(
            ['id' => 7, 'related' => ['back' => ['id' => 7]]],
            $result,
        );
    }

    public function testUnknownEntityYieldsEmptyArray(): void
    {
        $serializer = $this->serializerWith([
            SerializerDummyEntity::class => ['related'],
            // SerializerDummyRelated は allow_list 未登録
        ]);
        $entity = new SerializerDummyEntity();
        $entity->related = new SerializerDummyRelated();
        $entity->related->code = 'leaked?';

        $result = $serializer->toArray($entity);

        // related は allow_list 未登録 → 空配列で返る (= プロパティが漏れない)
        $this->assertSame(['related' => []], $result);
    }

    /**
     * @param array<string, list<string>> $allowMap
     */
    private function serializerWith(array $allowMap): EntityArraySerializer
    {
        return new EntityArraySerializer(new AllowListResolver([new FakeAllowList($allowMap)]));
    }
}

/** @internal テスト用ダミー */
final class SerializerDummyEntity
{
    public ?int $id = null;
    public ?string $name = null;
    public ?bool $active = null;
    public ?\DateTime $createDate = null;
    public ?SerializerDummyRelated $related = null;

    /** @var ArrayCollection<int, SerializerDummyRelated>|null */
    public ?ArrayCollection $items = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}

/** @internal */
final class SerializerDummyRelated
{
    public ?string $code = null;
    public ?SerializerDummyNested $nested = null;
    public ?SerializerDummyEntity $back = null;

    public function getId(): ?int
    {
        return null === $this->code ? null : crc32($this->code);
    }
}

/** @internal */
final class SerializerDummyNested
{
    public ?SerializerDummyInner $inner = null;
}

/** @internal */
final class SerializerDummyInner
{
    public ?int $id = null;
    public ?string $name = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
