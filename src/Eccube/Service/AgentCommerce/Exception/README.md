# AgentCommerce Exception — 例外とエラー分類

エージェントチェックアウトのプロトコル系エラー（不正要求・処理不能＝HTTP 4xx/5xx + Error）を分類・表現するサブドメイン。
在庫切れ・販売停止等のビジネス系エラー（HTTP 200 + messages[]）は例外ではなく
[CheckoutSession](../CheckoutSession/README.md) の `AgentCheckoutMessage` で表現する（混同しない）。

- 📖 仕様（人間向け）: [README.html](./README.html)
- ⬆ 親: [AgentCommerce README](../README.md)

## 主要ファイル

- `AgentCheckoutErrorCode.php` — プロトコル系エラーのコード分類 enum
- `AgentCheckoutException.php` — プロトコル系エラーの例外（不正要求・処理不能）
- `IdempotencyConflictException.php` — Idempotency-Key の競合（HTTP 409 相当）

HTTP ステータスや messages[] への変換はプロトコル層（#6776 / #6574）が担う。
