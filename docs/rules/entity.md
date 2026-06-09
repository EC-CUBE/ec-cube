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

## プロキシ拡張に対応するクラスラッパ

コアのエンティティは、プラグイン/カスタマイズによる trait 追加（プロキシ生成）に対応するため
`if (!class_exists(X::class)) { ... }` で囲う。

```php
namespace Eccube\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Repository\ExampleRepository;

if (!class_exists(Example::class)) {
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
}
```

## 命名・その他

- テーブル名: データ系は `dtb_`、マスタ系は `mtb_`、プラグインは `plg_` を前置する。
- リレーションは `#[ORM\OneToMany]` / `#[ORM\ManyToOne]` 等の属性で定義。コレクションは
  コンストラクタで `ArrayCollection` を初期化する。
- ライフサイクルコールバックは `#[ORM\PrePersist]` / `#[ORM\PreUpdate]` 等（クラスに `#[ORM\HasLifecycleCallbacks]` が必要）。
- 業務ロジックはエンティティに書きすぎない（計算・永続化の取りまとめは Service／[`service.md`](./service.md)）。

## スキーマ変更時

- **エンティティ属性を変更したら、既存環境へ届けるためマイグレーションを追加する**（同一 PR）。
  → [`migration.md`](./migration.md) に従う。新規インストールは属性から `schema:create` で生成される。

## カスタマイズ（app/Customize）

- 既存エンティティへのフィールド追加は **コアを書き換えず trait で行い**、`app/Customize/Entity/` に置く。
- 反映には `bin/console eccube:generate:proxies` でプロキシを再生成する。

## よくある間違い

- ❌ XML / アノテーションでマッピング → ✅ PHP8 属性
- ❌ マイグレーションを作らずに属性だけ変更 → ✅ 変更と同一 PR でマイグレーション追加
- ❌ `class_exists` ラッパなしでコアエンティティを定義 → ✅ プロキシ拡張に対応するラッパで囲う
- ❌ プロパティ/戻り値の型宣言省略 → ✅ 型を付け、PHPStan level 6 を通す
