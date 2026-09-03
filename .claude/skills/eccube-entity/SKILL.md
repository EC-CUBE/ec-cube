---
name: eccube-entity
description: EC-CUBE 4.4 の Doctrine エンティティを実装・改修するときの規約。「エンティティを作って」「テーブルを追加して」「Entityにフィールドを足して」「リレーションを定義して」「マスタを追加して」などと言われたとき、または src/Eccube/Entity・app/Customize/Entity 配下を作成・編集するときに使用する。
---

# Entity 規約（EC-CUBE 4.4）

**対象**: `src/Eccube/Entity/**/*.php`, `app/Customize/Entity/**/*.php`
**前提**: Doctrine ORM 3.x / PHP 8.2+（マッピングは **PHP8 属性**）

## 基本ルール

- 名前空間 `Eccube\Entity`（マスタは `Eccube\Entity\Master`）。
- 通常エンティティは `Eccube\Entity\AbstractEntity` を、**マスタ系は `AbstractMasterEntity`** を継承する。
- **マッピングは PHP8 属性 `#[ORM\...]`**（XML・アノテーションは使わない）。
- PHP ファイル先頭に EC-CUBE ライセンスヘッダを付ける。
- プロパティ・getter / setter に型宣言を付ける。setter は `$this` を返す（fluent）。
  nullable は DB カラムの nullable と一致させる。

## エンティティの骨格

**`if (!class_exists(X::class)) { ... }` のクラスラッパは書かない。** プラグイン/カスタマイズによる
trait 追加は、プロキシ生成（`app/proxy/entity` / `bin/console eccube:generate:proxies`）が担う。
ラッパはその前段で使われていた名残で、`src/Eccube/Entity` からも `app/Customize/Entity` からも
撤去済み（現在 0 件）。`EntityProxyService` はプロキシ生成時にこのブロックを除去する側に回っている。

```php
namespace Eccube\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Repository\ExampleRepository;

#[ORM\Table(name: 'dtb_example')]
#[ORM\Index(columns: ['create_date'], name: 'dtb_example_create_date_idx')]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: ExampleRepository::class)]
class Example extends AbstractEntity
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER, options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    // getId() の戻り値が ?int（nullable）なのは、IDENTITY 採番のため
    // 永続化（flush）されるまで ID が未採番（null）だから。意図的な nullable で、
    // 「まだ DB に保存されていない新規エンティティ」を表現できる。
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
```

## 命名・その他

- テーブル名: データ系は `dtb_`、マスタ系は `mtb_`、プラグインは `plg_` を前置する。
- リレーションは `#[ORM\OneToMany]` / `#[ORM\ManyToOne]` 等の属性で定義。コレクションは
  コンストラクタで `ArrayCollection` を初期化する。
- ライフサイクルコールバックは `#[ORM\PrePersist]` / `#[ORM\PreUpdate]` 等（クラスに `#[ORM\HasLifecycleCallbacks]` が必要）。

## エンティティに置く「状態ロジック」と、外に出す「処理」の線引き

「業務ロジックをエンティティに書きすぎない」は雑な言い方で、実際には**何を置いてよいか**の区別が重要。

- **置いてよい（自身の状態から導く計算・判定）**: そのエンティティ自身のプロパティだけから決まる値や状態。
  - 例: `OrderItem::getTotalPrice()`（単価 × 数量）、`Order` の各種金額の合算、
    `Customer` の表示名の組み立て、ステータス定数の判定メソッドなど。
  - 副作用を持たず、外部（Repository・EntityManager・他サービス）に依存しない純粋な計算/判定はエンティティの責務。
  - **金額プロパティ（`Types::DECIMAL`）は Doctrine ORM 3.x で `?string`**（getter は `string` 戻り、setter も `string` 引数）。
    `Order` の `total` / `subtotal` / `payment_total` 等（`src/Eccube/Entity/Order.php`）が実例。
    計算は **float で四則演算せず `bcmath`**（`bcadd` / `bcmul` / `bccomp` 等、スケール 2）で行う。
    実例: `OrderItem::getTotalPrice()` = `bcmul($this->getPriceIncTax(), $this->getQuantity(), 2)`（`src/Eccube/Entity/OrderItem.php`）。
- **外に出す（副作用・横断・採番を伴う「処理」）**: 永続化や複数エンティティ・外部リソースを巻き込む処理。
  - **在庫引当・注文番号の採番・ポイント付与・値引き適用などの受注処理は PurchaseFlow（Skill `eccube-service` 参照）パイプラインへ**。
  - DB アクセス（クエリ）は Repository、トランザクションを伴う業務操作は Service へ（Skill `eccube-service`）。

## スキーマ変更時

- **スキーマの源泉は Entity の属性（`#[ORM\...]`）**。カラムの追加・変更は属性を編集するだけでよい。
  新規インストールは `doctrine:schema:create`、既存環境へのアップデートは `doctrine:schema:update --force` が
  属性から差分を反映する（**単純なカラム追加に ALTER マイグレーションは不要**）。
- マイグレーションを書くのは、`schema:update` で扱えないもの（**マスタ/初期データの INSERT、型変更・リネーム等の構造変更**）に限る。
  → 詳細は Skill `eccube-migration` に従う。

## カスタマイズ（app/Customize）

- 既存エンティティへのフィールド追加は **コアを書き換えず trait で行い**、`app/Customize/Entity/` に置く。
- 反映には `bin/console eccube:generate:proxies` でプロキシを再生成する。

## よくある間違い

- ❌ XML / アノテーションでマッピング → ✅ PHP8 属性
- ❌ カラムを足したので ALTER マイグレーションを書く → ✅ 属性を足すだけ（`schema:update` が反映）。マイグレーションは INSERT・型変更等に限る
- ❌ 在庫引当・採番・ポイント付与などの受注処理をエンティティに書く → ✅ PurchaseFlow / Service へ。エンティティは自身の状態から導く計算/判定まで
- ❌ プロパティ/戻り値の型宣言省略 → ✅ 型を付け、PHPStan level 6 を通す
- ❌ 金額 getter の戻り値を int/float 扱い → ✅ DECIMAL は `?string`（getter は `string`）。型宣言・代入も合わせる
- ❌ 金額を float で四則演算（丸め誤差）→ ✅ `bcmath`（`bcadd` / `bcmul` / `bccomp`、スケール 2）で計算する
- ❌ `create_date` / `update_date` を自前の `#[ORM\PrePersist]` でセット → ✅ コアの `SaveEventSubscriber` が setter を検出して自動セットするので二重実装になる
- ❌ プロパティだけ `?T = null` にしてカラム属性は据え置く → ✅ `nullable: true` が無ければ DB は NOT NULL。上位層で補完する設計は、その経路を通らない永続化で INSERT が落ちる
- ❌ 他エンティティへの関連で親削除時の挙動を未決定 → ✅ FK は既定で削除を止めるので、未指定だと親の削除が FK 違反で失敗する。`onDelete` を指定するか Service 側で後始末する（コアは大半が後者）
