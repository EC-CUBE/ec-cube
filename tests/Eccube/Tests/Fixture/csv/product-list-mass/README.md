# product-list-mass シナリオ

`ProductControllerTest::setUp()` の `createProducts(10)` を CSV ベースに置き換える
最小フィクスチャ. 検索結果件数の母数を確保するためだけの「数」を担保することが
目的で、個別の Product / ProductClass の中身は使用しない.

## 投入内容

| テーブル | 行数 | id レンジ | 備考 |
|---|---|---|---|
| `dtb_product` | 10 | 6001-6010 | 名前は `ProductCsv-1` 〜 `ProductCsv-10` |
| `dtb_product_image` | 30 | 7001-7030 | 各 Product につき 3 件 (`dummy-N-j.jpg`) |
| `dtb_product_class` | 40 | 8001-8040 | 各 Product につき visible × 3 + デフォルト規格 × 1 |
| `dtb_product_stock` | 40 | 9001-9040 | 各 ProductClass につき 1 件 (`stock=100`) |

## Phase (a) からの変更点

Phase (a) では `createProducts(10)` を呼び、ORM 経由ではないが Faker / Doctrine
メタデータ参照を伴う bulk INSERT を毎テスト前に実行していた. Phase (b) では
INSERT 行を CSV に固定し、Faker / Doctrine 経由のオーバーヘッドを排除する.

## 実運用との乖離

検索結果件数を増やすことのみが目的のため、実運用データとは以下の点で乖離する:

- `dtb_product_class.class_category_id1` / `class_category_id2` / `delivery_duration_id`
  を **NULL** に固定 (Generator 版ではランダムに既存マスタを参照していた)
- `dtb_product_class.price01` / `currency_code` / `point_rate` を NULL に固定
- 価格は `price02=1000`, 在庫は `stock=100` の固定値
- `dtb_product_image.file_name` は `dummy-N-j.jpg` の固定パターン

検索結果件数のみを使う `ProductControllerTest::setUp()` 用途では問題ない.
個別の ProductClass / Tag / Category 関連を検証する用途には**使えない**.

## ID レンジ

他シナリオと衝突しないように:

- product-list-mass: dtb_product 6001-6010 / image 7001-7030 / class 8001-8040 / stock 9001-9040
- 他シナリオの ID レンジ:
  - order-search: 1001-1040
  - customer-list: 2001-2010
  - customer-search: 3001-3004
  - order-search-multi-status: 4001-4002 / 5001-5012
