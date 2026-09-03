# AgentCommerce Fulfillment — 配送方法の提示

エージェントへ提示する配送方法の選択肢を、EC-CUBE の配送マスタ（`Delivery`/`DeliveryFee`/`Payment`/`DeliveryDuration`）
から中立表現へ組み立てるサブドメイン。各選択肢は送料・配送日数・利用可能な支払方法を minor-unit（整数）で保持する。

- 📖 仕様（人間向け）: [README.html](./README.html)
- ⬆ 親: [AgentCommerce README](../README.md)

## 主要ファイル

- `StandardFulfillmentOptionMapper.php` — 配送マスタから選択肢を組み立てる標準実装
- `FulfillmentOptionMapperInterface.php` — 差し替え用 seam
- `FulfillmentOption.php` — 配送方法の選択肢（送料・配送日数・支払方法）
- `FulfillmentPaymentOption.php` — 配送方法に紐づく支払方法の選択肢（代引手数料等）
