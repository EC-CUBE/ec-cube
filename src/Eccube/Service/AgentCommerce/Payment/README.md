# AgentCommerce Payment — 決済ハンドラ

エージェント注文の決済ハンドラの型と、支払方法の割当・解決を担うサブドメイン。
エージェントは画面を持たず discovery で広告された決済ハンドラを `handler_id` で指定する。
core は具象決済実装を持たず、決済プラグインがタグ付きサービスでハンドラを寄与する（型のみ core が保持）。

- 📖 仕様（人間向け）: [README.html](./README.html)
- ⬆ 親: [AgentCommerce README](../README.md)

## 主要ファイル

- `AgentCheckoutPaymentHandlerInterface.php` — 決済ハンドラの共通基底（プロトコル固有 IF が派生）
- `AgentCheckoutPaymentHandlerRegistry.php` — タグ付きハンドラの集約と Order からの解決
- `AgentPaymentMethodResolverInterface.php`（`DefaultAgentPaymentMethodResolver.php`）— `Payment` 割当リゾルバ（sort_no 機械採用を避ける）
- `PaymentOutcome.php` / `PaymentOutcomeStatus.php` — 決済結果の中立 DTO とステータス（追加認証/escalation を中間状態として表す）
