# マイグレーション規約（EC-CUBE 4.4）

**対象**: `app/DoctrineMigrations/Version*.php`
**前提**: Doctrine Migrations Bundle / Doctrine ORM 3.x

> 重要: enterprise 等の一部運用では「マイグレーションを作らない」とされるが、
> **OSS の EC-CUBE 4.4 ではスキーマ変更時にマイグレーションを作成する**。両者を混同しないこと。

## スキーマ管理の全体像

- **スキーマの源泉は Entity の属性（`#[ORM\...]`）**。新規インストールは
  `doctrine:schema:create`（`SchemaTool::createSchema()`）が Entity メタデータからテーブルを生成する。
- **既存インストールへ変更を届けるため、列・テーブルの追加/変更時はマイグレーションを追加する**。
  Entity 属性の変更とマイグレーションは**同じ PR でセット**にする（例: 列追加の機能 PR に
  `dtb_base_info` への `ALTER TABLE ... ADD ...` を行う Version ファイルが同梱される）。
- **初期データ・マスタデータ**は `src/Eccube/Resource/doctrine/import_csv/{ja,en}` に定義する。

## 生成手順

```bash
# Entity を変更した後、差分からマイグレーションの雛形を生成
bin/console doctrine:migrations:diff

# 生成されたファイルを手で冪等化・整形（下記ルール参照）
# 適用
bin/console doctrine:migrations:migrate
```

## マイグレーションファイルの作法

- 置き場所: `app/DoctrineMigrations/`（設定は `app/config/eccube/packages/doctrine_migrations.yaml`）。
- 名前空間 `DoctrineMigrations`、クラス `final class VersionYYYYMMDDHHMMSS extends AbstractMigration`。
- PHP ファイル先頭に EC-CUBE ライセンスヘッダを付ける。
- **`up()` と `down()` の両方**を実装する（`down()` で確実に巻き戻せること）。
- **冪等にする**: 何度流れても安全なよう、`$schema->hasTable()` / `$table->hasColumn()` で
  存在を確認してから `addSql()` する。既に適用済みなら早期 return。

```php
namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260316234241 extends AbstractMigration
{
    public const NAME = 'dtb_base_info';

    public function up(Schema $schema): void
    {
        if (!$schema->hasTable(self::NAME)) {
            return;
        }
        $table = $schema->getTable(self::NAME);
        if ($table->hasColumn('option_guest_purchase')) {
            return;   // 適用済みなら何もしない（冪等）
        }
        $this->addSql('ALTER TABLE dtb_base_info ADD option_guest_purchase BOOLEAN NOT NULL DEFAULT true');
    }

    public function down(Schema $schema): void
    {
        if (!$schema->hasTable(self::NAME)) {
            return;
        }
        $table = $schema->getTable(self::NAME);
        if (!$table->hasColumn('option_guest_purchase')) {
            return;
        }
        $this->addSql('ALTER TABLE dtb_base_info DROP COLUMN option_guest_purchase');
    }
}
```

- DB 依存の SQL は、対応 DB（PostgreSQL / MySQL）双方で動くことを意識する。
- プラグインのマイグレーションは各プラグインの `DoctrineMigrations/` に置く（コアの `app/DoctrineMigrations/` とは別）。

## よくある間違い

- ❌ Entity 属性だけ変更してマイグレーション未作成 → 既存環境に列が増えない。✅ 変更と同じ PR でマイグレーションを追加。
- ❌ マイグレーションでテーブルを"新規定義"してスキーマの源泉にする → ✅ 源泉は Entity 属性。マイグレーションは差分適用。
- ❌ `hasColumn` 等のガードなしで `ADD COLUMN` → 再実行や環境差で失敗。✅ 冪等ガードを入れる。
- ❌ `down()` 未実装 → ロールバック不能。✅ `up()`/`down()` を対で実装。
