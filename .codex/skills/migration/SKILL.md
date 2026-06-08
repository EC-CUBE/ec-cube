---
name: migration
description: EC-CUBE 4.4 のデータベースマイグレーションを作成・編集するときの規約。「マイグレーションを作って」「カラムを追加して」「テーブルを変更して」「スキーマを変えたい」「Entityにフィールドを足したのでDBに反映したい」などと言われたとき、または app/DoctrineMigrations 配下を作成・編集するとき、Entity の属性を変更してスキーマに影響するときに使用する。
---

<!--
  このファイルは tools/sync-ai-skills.php により同期される Skill スタブです。
  正本は .claude/skills/ 配下。編集は必ず正本に対して行い、`php tools/sync-ai-skills.php` を実行してください。
  .codex/skills/・.agents/skills/ 配下は生成物のため直接編集しないこと。
-->

# EC-CUBE 4.4 マイグレーション規約

スキーマ変更・マイグレーション作成時は、必ず次のドキュメントを読み込み、その規約に従うこと。

**規約本文: [`docs/rules/migration.md`](../../../docs/rules/migration.md)**

要点（詳細は本文参照）:
- スキーマの源泉は **Entity の属性 `#[ORM\...]`**（新規インストールは `doctrine:schema:create` で生成）。
- ただし **OSS では既存環境へ届けるためマイグレーションを作成する**（「作らない」は enterprise 等の別運用。混同しない）。
  Entity 変更とマイグレーションは**同じ PR でセット**にする。
- 生成は `bin/console doctrine:migrations:diff` → 手で冪等化。
- 置き場所 `app/DoctrineMigrations/`、`final class VersionYYYYMMDDHHMMSS extends AbstractMigration`、ライセンスヘッダ必須。
- **`up()`/`down()` 両方を実装**し、`hasTable()`/`hasColumn()` で**冪等**にする。
- 初期・マスタデータは `src/Eccube/Resource/doctrine/import_csv/{ja,en}`。
