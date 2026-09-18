# customer-search シナリオ

`CustomerRepositoryGetQueryBuilderBySearchDataTest::setUp()` 向けの最小フィクスチャ.
本会員 (customer_status_id=2) の Customer を 4 件投入する.
email は `customer@example.com`, `customer1@example.com`,
`customer2@example.com`, `customer3@example.com` で固定 (テスト本体が
個別に参照するため).

## 実運用との乖離

- `dtb_customer.password` は固定の dummy 文字列. 認証は伴わない前提.
- 個別 Customer のプロパティ (`sex_id`, `pref_id` 等) はすべて同値.
  Sex 別 / Pref 別検索の assertion ではテスト側で個別に setter で書き換える.

## ID レンジ

**3001-3004** を使用 (他シナリオと分離).
