# order-search-multi-status シナリオ

`OrderRepositoryGetQueryBuilderBySearchDataAdminTest::setUp()` 向けの
最小フィクスチャ. **異なる OrderStatus / OrderDate を持つ 3 件の Order**
を投入する.

| Order | id | customer_id | order_status_id | order_no | order_date |
|---|---|---|---|---|---|
| `$Order` | 5001 | 4001 | 8 (PROCESSING) | `order-multi-status-1` | NULL |
| `$Order1` | 5002 | 4001 | 1 (NEW) | `order-multi-status-2` | 2026-05-21 |
| `$Order2` | 5003 | 4002 | 1 (NEW) | `order-multi-status-3` | 2026-05-21 |

## Phase (a) からの変更点

Phase (a) では `createOrders([$c, $c, $c2])` で 3 件作成後、setUp 内で
`$Order1->setOrderStatus($NewStatus)->setOrderDate(new \DateTime())` のように
個別 setter を呼んでいた. Phase (b) では CSV 内に `order_status_id` と
`order_date` を直接持たせるため、setter 呼び出しが不要になる.

## 実運用との乖離

- `dtb_order_item.product_id` / `product_class_id` を NULL
- `dtb_shipping.delivery_id` を NULL
- 金額・在庫はダミー値
- 詳細は `tests/Eccube/Tests/Fixture/csv/order-search/README.md` を参照

## ID レンジ

- Customer: **4001-4002**
- Order / Shipping / OrderItem: **5001-5012**

他シナリオと分離.
