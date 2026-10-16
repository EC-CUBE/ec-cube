# AgentCommerce CheckoutSession — チェックアウト

エージェントチェックアウトの見積〜確定（complete）を、プロトコル非依存の中立表現で束ねるサブドメイン。
金額再計算・在庫引当はルート直下の `AgentCheckoutPurchaseFlowAdapter` が通常購入と同一の shopping flow で行い、
本サブドメインはその入出力と確定（中断→再開する状態機械）の段取りを担う。

- 📖 仕様（人間向け）: [README.html](./README.html)
- ⬆ 親: [AgentCommerce README](../README.md)
- 📚 受注処理の詳細: [PurchaseFlow README](../../PurchaseFlow/README.md)

## 主要ファイル

- `AgentCheckoutCompletionService.php` — complete を「中断→再開」する状態機械として実行するオーケストレータ
- `AgentCheckoutRequest.php` / `AgentCheckoutLineItem.php` / `AgentCheckoutAddress.php` — 見積要求の中立 DTO
- `AgentCheckoutResult.php` / `AgentCheckoutCompletionResult.php` — 見積/確定・complete の中立結果
- `AgentCheckoutMessage.php` / `AgentCheckoutMessageLevel.php` — ビジネス系メッセージ（HTTP 200 + messages[]）
- `CustomerResolverInterface.php`（`GuestCustomerResolver.php`）— 会員解決 seam（標準はゲスト固定）
