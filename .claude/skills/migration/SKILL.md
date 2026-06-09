---
name: migration
description: EC-CUBE 4.4 のデータベースマイグレーションを作成・編集するときの規約。「マイグレーションを作って」「マスタデータ/初期データを投入したい」「カラムの型を変えたい/リネームしたい」「スキーマを変えたい」などと言われたとき、または app/DoctrineMigrations 配下を作成・編集するときに使用する。注意: 単純なカラム追加は Entity 属性＋schema:update で反映されるためマイグレーション不要（その判断にも本 Skill を参照）。
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
- **スキーマの源泉は Entity の属性 `#[ORM\...]`**。アップデートは 2 段構え:
  `doctrine:schema:update --force`（属性差分を反映）→ `doctrine:migrations:migrate`。
- **単純なカラム追加・変更にマイグレーション（ALTER）は不要**（`schema:update` が拾う）。
  マイグレーションを書くのは **マスタ/初期データの INSERT** と、**`schema:update` で扱えない構造変更（型変更・リネーム等）** に限る。
- 生成は `doctrine:migrations:diff`（差分から ALTER 自動生成）ではなく
  **`doctrine:migrations:generate`（空の雛形を手書き）** を使う。
- 置き場所 `app/DoctrineMigrations/`、`final class VersionYYYYMMDDHHMMSS extends AbstractMigration`、ライセンスヘッダ必須。
- **`up()`/`down()` 両方を実装**し、存在チェック（`hasTable()`/`hasColumn()`・`SELECT COUNT(*)` 等）で**冪等**にする。
- 初期・マスタデータの**定義**は `src/Eccube/Resource/doctrine/import_csv/{ja,en}`（既存環境への投入はマイグレーションの INSERT）。
