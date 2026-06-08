---
name: entity
description: EC-CUBE 4.4 の Doctrine エンティティを実装・改修するときの規約。「エンティティを作って」「テーブルを追加して」「Entityにフィールドを足して」「リレーションを定義して」「マスタを追加して」などと言われたとき、または src/Eccube/Entity・app/Customize/Entity 配下を作成・編集するときに使用する。
---

<!--
  このファイルは tools/sync-ai-skills.php により同期される Skill スタブです。
  正本は .claude/skills/ 配下。編集は必ず正本に対して行い、`php tools/sync-ai-skills.php` を実行してください。
  .codex/skills/・.agents/skills/ 配下は生成物のため直接編集しないこと。
-->

# EC-CUBE 4.4 Entity 実装規約

エンティティを実装・改修する際は、必ず次のドキュメントを読み込み、その規約に従うこと。

**規約本文: [`docs/rules/entity.md`](../../../docs/rules/entity.md)**

要点（詳細は本文参照）:
- `Eccube\Entity\AbstractEntity`（マスタは `AbstractMasterEntity`）を継承。マッピングは **PHP8 属性 `#[ORM\...]`**。
- コアのエンティティは `if (!class_exists(X::class)) { ... }` で囲い、プロキシ拡張に対応する。
- setter は `$this` を返す。nullable は DB カラムと一致。テーブル名は `dtb_`/`mtb_`/`plg_`。
- **属性を変更したらマイグレーションを同一 PR で追加**（[`docs/rules/migration.md`](../../../docs/rules/migration.md)）。
- 既存エンティティへのフィールド追加は trait で `app/Customize/Entity/` に置き、`eccube:generate:proxies` で反映。
- ライセンスヘッダ・型宣言必須。
