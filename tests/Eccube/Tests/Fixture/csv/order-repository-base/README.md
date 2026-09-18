# order-repository-base シナリオ

`OrderRepositoryTest::setUp()` の `createProduct + createCustomer + createOrder`
を CSV ベースに置き換える最小フィクスチャ. 70 テストの setUp で毎回走っていた
Generator 経由のチェーン (Product + Delivery + Order + Shipping + OrderItem 等)
を 4 テーブル INSERT に圧縮する.

## 投入内容

| テーブル | 行数 | id | 備考 |
|---|---|---|---|
| `dtb_customer` | 1 | 10001 | `email=order-repository-base@example.com` |
| `dtb_order` | 1 | 10001 | `order_no=order-repository-base-1` |
| `dtb_shipping` | 1 | 10001 | `order_id=10001` (1 件 Shipping) |
| `dtb_order_item` | 1 | 10001 | `order_id=10001`, 商品アイテム 1 件 (`product_name=商品-1`) |

## 実運用との乖離

setUp で参照する `$this->Customer` / `$this->Order` の最小要件のみを満たす:

- `dtb_order_item.product_id` / `product_class_id` を **NULL** に固定 (Product 自体は
  CSV で投入しないため. 検索系テストでは product との外部結合を強制しない限り問題なし)
- `dtb_order_item` は商品アイテム 1 件のみ (Generator 版では送料・手数料・値引きを
  含む 4 件を生成). `getOrderItems()[0]` のみ参照するテストでは差異が出ない
- `payment_method` を `'Test'` 固定. Faker による文字列生成を回避
- `creator_id` を NULL (`dtb_product.csv` 等と同様, Member id を特定すると
  MySQL/PostgreSQL で FK 違反になるため)

検索条件や Order/Shipping エンティティを mutate するテストには影響しない.
ProductClass / Tag / Category 関連を要求するテストでは createProduct を併用すること.

## ID レンジ

他シナリオと衝突しないように:

- order-repository-base: dtb_customer 10001 / dtb_order 10001 / dtb_shipping 10001 / dtb_order_item 10001
- 他シナリオの ID レンジ:
  - order-search: 1001-1040
  - customer-list: 2001-2010
  - customer-search: 3001-3004
  - order-search-multi-status: 4001-4002 / 5001-5012
  - product-list-mass: 6001-6010 / 7001-7030 / 8001-8040 / 9001-9040
