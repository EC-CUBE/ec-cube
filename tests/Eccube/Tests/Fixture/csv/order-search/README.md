# order-search シナリオ

`OrderControllerTest::setUp()` 向けの最小フィクスチャ.
Customer 10 件 + Order 10 件 + Shipping 10 件 + OrderItem 40 件 (商品 / 送料 / 手数料 / 値引き 各 10 件).

## 実運用との乖離 (重要)

このフィクスチャは **テストを通すための最小データ** であり、実運用の受注データとは
以下の点で意図的に乖離している:

- `dtb_order_item.product_id` / `product_class_id` を **すべて NULL** にしている.
  実運用では商品明細行 (`order_item_type_id = 1`) は必ず Product / ProductClass を
  参照するが、`OrderControllerTest` の assertion は件数・order_no・company_name 検索
  のみで商品参照を要求しないため、Product / Delivery を生成しない簡素化を採用.
- `dtb_shipping.delivery_id` も NULL. 同上の理由で Delivery を生成しない.
- 金額 (`price` / `subtotal` / `total` 等) はすべて 0 / 1000 等のダミー値.
  PurchaseFlow による再計算を行っていない.
- `dtb_customer.password` は `dummy_password_hash_for_csv_fixture` という固定文字列.
  認証を伴うテストではない前提.

商品参照や金額計算に依存するテストでは本シナリオを使えない. その場合は別シナリオを
新規作成するか、`Generator::createOrders()` (bulk API) を利用すること.

## ロード順

`definition.yml` で FK 依存に沿った順序を定義: customer → order → shipping → order_item.

## ID レンジ

既存の `dtb_*` 行と衝突しないよう **1001-1040** を使用. テスト終了時には DAMA
DoctrineTestBundle により自動 rollback される.
