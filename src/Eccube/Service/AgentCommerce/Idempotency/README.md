# AgentCommerce Idempotency — 冪等性

状態変更操作（complete 等）の二重実行を防ぐサブドメイン。`Idempotency-Key` ヘッダの初回はハンドラを実行して結果を保存し、
同一キーの再送は保存済みレスポンスをリプレイする（副作用の再実行なし）。
`(idempotency_key, subject)` の DB 一意制約で直列化し、マルチインスタンスでも共有 DB だけで越境の二重実行を防ぐ。

- 📖 仕様（人間向け）: [README.html](./README.html)
- ⬆ 親: [AgentCommerce README](../README.md)

## 主要ファイル

- `AgentCheckoutIdempotencyStore.php` — Idempotency-Key 処理（`dtb_agent_checkout_idempotency` へ保存・再送でリプレイ）

競合時（内容不一致の再利用・処理進行中）は `Exception/IdempotencyConflictException`（HTTP 409 相当）を投げる。
