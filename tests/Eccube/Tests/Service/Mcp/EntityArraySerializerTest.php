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
use Doctrine\Persistence\Proxy;
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

    public function testSummaryOmitsIdWhenNotAllowed(): void
    {
        // 深さ超過の要約でも「allow_list のみ公開」を守る。 getId があっても allow_list に 'id' が
        // 無い関連 Entity は、 縮退経路で内部 ID を露出させない。
        $serializer = $this->serializerWith([
            SerializerDummyEntity::class => ['related'],
            SerializerDummyRelated::class => ['nested'],
            SerializerDummyNested::class => ['inner'],
            SerializerDummyInner::class => ['name'], // 'id' を意図的に外す (getId は存在する)
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

        // maxDepth=2 → inner(d3) は要約。 allow_list に 'id' が無いので空要約になる。
        $result = $serializer->toArray($entity, maxDepth: 2);

        $this->assertSame(
            ['related' => ['nested' => ['inner' => []]]],
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

    public function testResolvesEntityClassThroughDoctrineProxy(): void
    {
        // 実機での `Status: []` バグの再現テスト:
        // Doctrine が Lazy Proxy (Proxies\__CG__\... の自動生成 class) を返した時に、
        // allow_list が proxy class 名で引かれて未登録扱いになり空配列が返る問題。
        // 修正後は親クラス (= 実 entity FQCN) で lookup されるため正しく展開される。
        $serializer = $this->serializerWith([
            SerializerProxiableEntity::class => ['id', 'name'],
        ]);

        $proxy = new class extends SerializerProxiableEntity implements Proxy {
            #[\Override]
            public function __load(): void
            {
            }

            #[\Override]
            public function __isInitialized(): bool
            {
                return true;
            }
        };
        $proxy->id = 99;
        $proxy->name = 'proxied';

        $result = $serializer->toArray($proxy);

        $this->assertSame(['id' => 99, 'name' => 'proxied'], $result);
    }

    public function testDefaultMaxDepthOneSummarizesSiblingInnerRelations(): void
    {
        // デフォルト maxDepth=1 の検証。 root の直下 (depth 1) までは展開、 さらにその子 (depth 2)
        // は要約に縮退。 get_product_stock などで sibling Entity の中身が大量に重複表示される
        // ノイズを抑止するための仕様変更 (旧デフォルト 2 → 1)。
        $serializer = $this->serializerWith([
            SerializerDummyEntity::class => ['id', 'related'],
            SerializerDummyRelated::class => ['code', 'nested'],
            SerializerDummyNested::class => ['inner'],
            SerializerDummyInner::class => ['id'],
        ]);

        $entity = new SerializerDummyEntity();
        $entity->id = 1;
        $entity->related = new SerializerDummyRelated();
        $entity->related->code = 'A';
        $entity->related->nested = new SerializerDummyNested();
        $entity->related->nested->inner = new SerializerDummyInner();
        $entity->related->nested->inner->id = 100;

        $result = $serializer->toArray($entity); // 引数省略 = DEFAULT_MAX_DEPTH (1)

        // related (depth 1) は full、 nested (depth 2) は要約 (SerializerDummyNested に getId 無し → 空 [])
        $this->assertSame(
            ['id' => 1, 'related' => ['code' => 'A', 'nested' => []]],
            $result,
        );
    }

    public function testSummaryReturnsOnlyListedScalars(): void
    {
        $serializer = $this->serializerWith([
            SerializerDummyEntity::class => ['id', 'name', 'active'],
        ]);
        $entity = new SerializerDummyEntity();
        $entity->id = 1;
        $entity->name = 'foo';
        $entity->active = true;

        // active は allow_list にあるがサマリ定義に無い → 出力されない
        $this->assertSame(
            ['id' => 1, 'name' => 'foo'],
            $serializer->toSummary($entity, ['id', 'name']),
        );
    }

    public function testSummarySkipsFieldsNotInAllowList(): void
    {
        // fail-closed: allow_list に無い項目はサマリ定義に入れても出力されない
        $serializer = $this->serializerWith([
            SerializerDummyEntity::class => ['id'], // name は allow_list 外
        ]);
        $entity = new SerializerDummyEntity();
        $entity->id = 1;
        $entity->name = 'secret';

        $this->assertSame(['id' => 1], $serializer->toSummary($entity, ['id', 'name']));
    }

    public function testSummaryDoesNotExpandCollections(): void
    {
        $serializer = $this->serializerWith([
            SerializerDummyEntity::class => ['id', 'items'],
            SerializerDummyRelated::class => ['code'],
        ]);
        $related = new SerializerDummyRelated();
        $related->code = 'A';
        $entity = new SerializerDummyEntity();
        $entity->id = 1;
        $entity->items = new ArrayCollection([$related]);

        // items は allow_list 許可だが Collection なので展開されず null になる
        $this->assertSame(['id' => 1, 'items' => null], $serializer->toSummary($entity, ['id', 'items']));
    }

    public function testSummaryResolvesDottedRelationField(): void
    {
        $serializer = $this->serializerWith([
            SerializerDummyEntity::class => ['id', 'related'],
            SerializerDummyRelated::class => ['code'],
        ]);
        $entity = new SerializerDummyEntity();
        $entity->id = 1;
        $entity->related = new SerializerDummyRelated();
        $entity->related->code = 'X-1';

        $this->assertSame(
            ['id' => 1, 'related' => ['code' => 'X-1']],
            $serializer->toSummary($entity, ['id', 'related.code']),
        );
    }

    public function testSummaryDottedSkippedWhenSubFieldNotAllowed(): void
    {
        // 親で related は許可だが、 子 Entity で code が許可されていない → ドット path をスキップ (security)
        $serializer = $this->serializerWith([
            SerializerDummyEntity::class => ['id', 'related'],
            SerializerDummyRelated::class => [],
        ]);
        $entity = new SerializerDummyEntity();
        $entity->id = 1;
        $entity->related = new SerializerDummyRelated();
        $entity->related->code = 'X-1';

        $this->assertSame(['id' => 1], $serializer->toSummary($entity, ['id', 'related.code']));
    }

    public function testSummaryDottedKeepsNullKeyWhenRelationAbsent(): void
    {
        // relation は許可されているが値が無い (データ状態) → security スキップと区別し、 キーは null で残す
        $serializer = $this->serializerWith([
            SerializerDummyEntity::class => ['id', 'related'],
            SerializerDummyRelated::class => ['code'],
        ]);
        $entity = new SerializerDummyEntity();
        $entity->id = 1;
        $entity->related = null;

        $this->assertSame(['id' => 1, 'related' => null], $serializer->toSummary($entity, ['id', 'related.code']));
    }

    public function testSummaryFormatsDateTimeAsAtom(): void
    {
        $serializer = $this->serializerWith([SerializerDummyEntity::class => ['createDate']]);
        $entity = new SerializerDummyEntity();
        $entity->createDate = new \DateTime('2026-06-04T12:34:56+09:00');

        $this->assertSame(
            ['createDate' => '2026-06-04T12:34:56+09:00'],
            $serializer->toSummary($entity, ['createDate']),
        );
    }

    public function testSummarizeRelationsCollapsesRootCollectionToIds(): void
    {
        $serializer = $this->serializerWith([
            SerializerDummyEntity::class => ['id', 'items'],
            SerializerDummyRelated::class => ['id', 'code'],
        ]);
        $a = new SerializerDummyRelated();
        $a->code = 'A';
        $b = new SerializerDummyRelated();
        $b->code = 'B';
        $entity = new SerializerDummyEntity();
        $entity->id = 1;
        $entity->items = new ArrayCollection([$a, $b]);

        // 通常は items を full 展開するが、 summarizeRelations 指定で各要素 id のみに縮退する
        $this->assertSame(
            ['id' => 1, 'items' => [['id' => crc32('A')], ['id' => crc32('B')]]],
            $serializer->toArray($entity, summarizeRelations: ['items']),
        );
    }

    public function testSummarizeRelationsCollapsesRootSingleRelation(): void
    {
        $serializer = $this->serializerWith([
            SerializerDummyEntity::class => ['id', 'related'],
            SerializerDummyRelated::class => ['id', 'code'],
        ]);
        $entity = new SerializerDummyEntity();
        $entity->id = 1;
        $entity->related = new SerializerDummyRelated();
        $entity->related->code = 'X';

        // full なら related.code まで出るが、 縮退指定で {id} のみになる
        $this->assertSame(
            ['id' => 1, 'related' => ['id' => crc32('X')]],
            $serializer->toArray($entity, summarizeRelations: ['related']),
        );
    }

    public function testSummarizeRelationsOnlyAffectsListedRelations(): void
    {
        // 指定していない関連は従来どおり full 展開される
        $serializer = $this->serializerWith([
            SerializerDummyEntity::class => ['id', 'related'],
            SerializerDummyRelated::class => ['id', 'code'],
        ]);
        $entity = new SerializerDummyEntity();
        $entity->id = 1;
        $entity->related = new SerializerDummyRelated();
        $entity->related->code = 'X';

        $this->assertSame(
            ['id' => 1, 'related' => ['id' => crc32('X'), 'code' => 'X']],
            $serializer->toArray($entity, summarizeRelations: ['items']),
        );
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

/** @internal Doctrine Proxy 互換テスト用 (non-final で extend 可) */
class SerializerProxiableEntity
{
    public ?int $id = null;
    public ?string $name = null;

    public function getId(): ?int
    {
        return $this->id;
    }
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
