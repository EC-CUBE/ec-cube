# AgentCommerce Discovery — 機能発見

エージェントが店舗の対応機能を発見するための UCP discovery profile（`/.well-known/ucp`）を組み立てるサブドメイン。
profile には対応サービス・決済ハンドラ・署名鍵（JWK）が含まれる。
core は `payment_handlers` をカラム化せず、tagged service で寄与された内容のみを収集する。

- 📖 仕様（人間向け）: [README.html](./README.html)
- ⬆ 親: [AgentCommerce README](../README.md)
- 📚 プロトコル仕様: [UCP profile.json](https://github.com/Universal-Commerce-Protocol/ucp/blob/main/schemas/profile.json)

## 主要ファイル

- `UcpProfileBuilder.php` — UCP discovery profile ドキュメントの組み立て（endpoint は UrlGenerator で動的生成）
- `PaymentHandlerRegistryInterface.php` — `payment_handlers`（reverse-domain キー）を寄与する口
- `EmptyPaymentHandlerRegistry.php` — 既定実装。寄与が無ければ空オブジェクト `{}` を返す
