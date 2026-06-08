---
name: service
description: EC-CUBE 4.4 の Service を実装・改修するときの責務分離規約。「サービスを作って」「ロジックをサービスに切り出して」「このサービスを直して」「コントローラから業務処理を抽出して」などと言われたとき、または src/Eccube/Service・app/Customize/Service 配下を作成・編集するときに使用する。業務ロジックの受け皿を単一責任・HTTP非依存に保つための規約。
---

<!--
  このファイルは tools/sync-ai-skills.php により同期される Skill スタブです。
  正本は .claude/skills/ 配下。編集は必ず正本に対して行い、`php tools/sync-ai-skills.php` を実行してください。
  .codex/skills/・.agents/skills/ 配下は生成物のため直接編集しないこと。
-->

# EC-CUBE 4.4 Service 実装規約（責務分離）

Service を実装・改修する際は、必ず次のドキュメントを読み込み、その規約に従うこと。

**規約本文: [`docs/rules/service.md`](../../../docs/rules/service.md)**

要点（詳細は本文参照）:
- Service はコントローラから抽出した**業務ロジックの受け皿**。金額・在庫・送料・税の算出、ステータス遷移などを置く。
- `EntityManager` の `persist()` / `flush()` は **Service に置いてよい**（むしろ正しい置き場所）。
- **Controller への依存は禁止**（レイヤ違反）。依存の向きは Controller → Service → Repository の一方向。
- HTTP の知識（`Request` / `Response`）を持ち込まない。必要な値だけを引数で受け取る。
- 単一責任を保つ。目安: 1 メソッド約 50 行 / コンストラクタ依存約 7 個を超えたら分割を検討。

実装・改修後は、Skill `review-responsibility` で責務分離を点検すること。
