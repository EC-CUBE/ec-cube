---
name: controller
description: EC-CUBE 4.4 のコントローラを実装・改修するときの責務分離規約。「コントローラを作って」「アクションを追加して」「このコントローラを直して」「ルーティングを追加して」などと言われたとき、または src/Eccube/Controller・app/Customize/Controller 配下を作成・編集するときに使用する。Fat コントローラを避け業務ロジックを Service へ寄せるための規約。
---

<!--
  このファイルは tools/sync-ai-skills.php により同期される Skill スタブです。
  正本は .claude/skills/ 配下。編集は必ず正本に対して行い、`php tools/sync-ai-skills.php` を実行してください。
  .codex/skills/・.agents/skills/ 配下は生成物のため直接編集しないこと。
-->

# EC-CUBE 4.4 コントローラ実装規約（責務分離）

コントローラを実装・改修する際は、必ず次のドキュメントを読み込み、その規約に従うこと。

**規約本文: [`docs/rules/controller.md`](../../../docs/rules/controller.md)**

要点（詳細は本文参照）:
- コントローラの責務は「HTTP の入出力変換」のみ: リクエスト受領 → フォーム処理 → **Service/Repository へ委譲** → レスポンス組み立て。
- **業務ロジック（金額・在庫・送料・ポイント計算、複数 Repository をまたぐ処理、外部連携、メール送信）は Service へ出す**（Service 側の規約は `docs/rules/service.md`）。
  受注の計算・検証・確定は **PurchaseFlow** へ（在庫引当・採番・ポイント付与・値引き）。
- アクション内での `$em->persist()` / `$em->flush()` の業務的な直書きは避け、Service にまとめる。
- ルーティングは `#[Route]` 属性、依存はコンストラクタインジェクション（`private readonly`、インターフェース型ヒント）。
- Fat 化は**質的シグナル**（業務計算の混入・persist/flush 直書き・コピペ重複）で判断。
  行数・依存数の計測は `tools/check-architecture.php`、整形/型/`@Route`→`#[Route]` 変換は rector・phpstan・php-cs-fixer に委ねる。

実装・改修後は、Skill `review-responsibility` で責務分離を点検すること。
