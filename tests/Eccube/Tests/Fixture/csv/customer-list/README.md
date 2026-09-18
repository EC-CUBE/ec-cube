# customer-list シナリオ

`CustomerControllerTest::setUp()` 向けの最小フィクスチャ.
本会員 (customer_status_id=2) の Customer を 10 件投入する.

## 実運用との乖離 (重要)

- `dtb_customer.password` は `dummy_password_hash_for_csv_fixture` という固定文字列.
  認証を伴うテストではない前提.
- `kana01` / `kana02` / `birth` 等の値は固定値.
- `secret_key` は CSV 用に固定 (UNIQUE 制約のみ満たす目的).

会員登録ロジックの assertion を伴うテストでは本シナリオを使えない.
その場合は別シナリオを新規作成するか、`Generator::createCustomers()` (bulk API)
を利用すること.

## ID レンジ

既存の `dtb_customer` 行と衝突しないよう **2001-2010** を使用.
他シナリオとは別レンジで分離.
