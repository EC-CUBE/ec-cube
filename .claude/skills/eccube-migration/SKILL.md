---
name: eccube-migration
description: EC-CUBE 4.4 のデータベースマイグレーションを作成・編集するときの規約。「マイグレーションを作って」「マスタデータ/初期データを投入したい」「カラムの型を変えたい/リネームしたい」「スキーマを変えたい」などと言われたとき、またはマイグレーションを作成・編集するとき（コア・プラグインのいずれでも）に使用する。注意 — 単純なカラム追加は Entity 属性＋schema:update で反映されるためマイグレーション不要（その判断にも本 Skill を参照）。
---

# マイグレーション規約（EC-CUBE 4.4）

**対象**: `app/DoctrineMigrations/Version*.php`
**前提**: Doctrine Migrations Bundle / Doctrine ORM 3.x

> 重要: **スキーマの源泉は Entity の属性（`#[ORM\...]`）であり、マイグレーションではない**。
> EC-CUBE 4 系のアップデートは 2 段構えで、**単純なカラム追加・変更は `schema:update` が自動反映する**。
> マイグレーションを書くのは、`schema:update` で扱えないもの（マスタ/初期データ投入、型変更・リネーム等）に限る。

## スキーマ管理の全体像（2 段構え）

EC-CUBE のアップデートは次の 2 ステップで構成される（doc4 アップデート手順）:

```bash
# (1) Entity 属性 #[ORM\Column] から導けるカラム追加・変更を自動反映
bin/console doctrine:schema:update --force --dump-sql

# (2) schema:update で扱えないもの（マスタ/初期データの INSERT、型変更・リネーム等）を適用
bin/console doctrine:migrations:migrate --no-interaction
```

ここから導かれる原則:

- **スキーマの源泉は Entity の属性 `#[ORM\...]`**。新規インストールは `doctrine:schema:create`、
  既存環境へのアップデートは `doctrine:schema:update --force` が Entity メタデータからスキーマを反映する。
- **単純なカラム追加・変更にマイグレーション（`ALTER TABLE ... ADD` 等）は不要**。
  Entity 属性を足せば、新規は `schema:create`、既存は `schema:update` が拾う。
  → 例: PR #4912（Google アナリティクス機能）は `BaseInfo` にカラムを追加したが、
    ALTER マイグレーションは作らず `schema:update` に委ねている。
- **マイグレーションを書くのは次の場合に限る**:
  1. **マスタ/初期データの INSERT**（`mtb_*` のレコード、`dtb_block` / `dtb_mail_template` / `dtb_csv` 等への初期レコード投入）。
  2. **`schema:update` が安全に扱えない構造変更**（カラムの**型変更・リネーム**、データ移行を伴う変更、
     一意制約の後付けなど、データの保全や変換が必要なもの）。

> リポジトリの実体: `app/DoctrineMigrations/` は大半が `INSERT INTO`（マスタ/初期データ投入）であり、
> 純粋な `ALTER ... ADD COLUMN` はごく少数（例外的なケース）。「カラムを足したらマイグレーションを書く」は誤り。

## マスタ/初期データの追加は「CSV ＋ マイグレーション」の両方が必要

ここが間違えやすい。**CSV を足すだけでは既存環境に届かない**。両方をセットで行う:

| 用途 | 置き場所 | 反映タイミング |
|---|---|---|
| **新規インストール**用の定義 | `src/Eccube/Resource/doctrine/import_csv/{ja,en}/*.csv` に行を追加 | `eccube:fixtures:load`（**インストール時のみ**実行） |
| **既存環境**への配布 | `app/DoctrineMigrations/` に **INSERT のマイグレーション** | `doctrine:migrations:migrate`（アップデート時に実行） |

- `import_csv` を読む `eccube:fixtures:load` は**インストール時にしか走らない**（composer の `auto-scripts` にも含まれない）。
  そのため、**CSV に行を足しても既にインストール済みの環境の DB には入らない**。既存環境へは INSERT マイグレーションで届ける。
- 逆にマイグレーションの INSERT だけでは、新規インストール時の初期データに反映されない（新規は CSV から投入されるため）。
- **したがって、マスタ/初期データを追加するときは CSV 追記と INSERT マイグレーションを同一 PR で両方行う**。

> 実例（現役の運用）: `Version20240312170000`（2024-03）は `dtb_block` に
> 「新着商品（自動取得）」ブロックを INSERT すると同時に、同じコミットで `dtb_block.csv` にも同じ行を追加している。
> 同様に `Version20220603074035`（`mtb_csv_type` のマスタ追加）や `Version20230515023836`（`dtb_mail_template`）など、
> **2022〜2024 年にわたり継続して INSERT マイグレーション＋CSV の組で運用されている**。

## 生成手順

`doctrine:migrations:diff`（Entity 差分から `ALTER` を自動生成）は**使わない**。
単純なカラム差分まで ALTER として吐き出してしまい、上記の原則（カラム追加は `schema:update` に委ねる）と矛盾するため。
**空の雛形を生成し、必要な SQL だけを手で書く**:

```bash
# 空のマイグレーション雛形を生成
bin/console doctrine:migrations:generate

# 生成されたファイルに、INSERT や型変更などの必要な SQL を手で記述・冪等化（下記ルール参照）
# 適用
bin/console doctrine:migrations:migrate
```

## マイグレーションファイルの作法

- 置き場所: `app/DoctrineMigrations/`（設定は `app/config/eccube/packages/doctrine_migrations.yaml`）。
- 名前空間 `DoctrineMigrations`、クラス `final class VersionYYYYMMDDHHMMSS extends AbstractMigration`。
- PHP ファイル先頭に EC-CUBE ライセンスヘッダを付ける。
- **`up()` と `down()` の両方**を実装する（`down()` で確実に巻き戻せること）。
- **冪等にする**: 何度流れても安全なよう、存在を確認してから `addSql()` する。既に適用済みなら早期 return。

```php
namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * マスタ/初期データを既存環境へ投入する例。
 * （カラム追加そのものは Entity 属性＋schema:update が反映するため、ここでは扱わない）
 */
final class Version20240101000000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // 重複投入を避けるため、存在チェックしてから INSERT する（冪等）
        $count = $this->connection->fetchOne(
            "SELECT COUNT(*) FROM mtb_sale_type WHERE id = 3"
        );
        if ($count > 0) {
            return;
        }
        $this->addSql("INSERT INTO mtb_sale_type (id, name, sort_no, discriminator_type) VALUES (3, '定期購入', 3, 'saletype')");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM mtb_sale_type WHERE id = 3");
    }
}
```

- 構造変更（型変更・リネーム等）を行う場合は、`$schema->hasTable()` / `$table->hasColumn()` で
  存在を確認してから `ALTER` を発行し、冪等性とロールバック（`down()`）を担保する。
- DB 依存の SQL は、対応 DB（PostgreSQL / MySQL）双方で動くことを意識する。
- プラグインのマイグレーションは各プラグインの `DoctrineMigrations/` に置く（コアの `app/DoctrineMigrations/` とは別）。

## よくある間違い

- ❌ カラムを足したので `ALTER TABLE ... ADD COLUMN` のマイグレーションを書く
  → ✅ Entity 属性を足すだけ。新規は `schema:create`、既存は `schema:update --force` が反映する。
- ❌ `doctrine:migrations:diff` で Entity 差分から ALTER を自動生成する
  → ✅ `doctrine:migrations:generate` で空の雛形を作り、必要な SQL（INSERT・型変更等）だけ手で書く。
- ❌ マイグレーションでテーブルを"新規定義"してスキーマの源泉にする → ✅ 源泉は Entity 属性。
- ❌ INSERT・構造変更でガードなし → 再実行や環境差で失敗。✅ 存在チェックで冪等にする。
- ❌ `down()` 未実装 → ロールバック不能。✅ `up()`/`down()` を対で実装。
