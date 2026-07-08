# Form/Type — フォーム型

会員登録・お問い合わせ・購入手続き（フロント）や商品・受注・会員の編集/検索（管理画面）で使う
入力フォームを、`AbstractType` を継承した「1 型 = 1 クラス」として定義するディレクトリ。
汎用フィールド型（住所・金額・マスタ選択）を組み合わせて複雑なフォームを組み立てる。

- 📖 仕様（人間向け）: [README.html](./README.html)
- 🛠 実装規約（AI 向け）: [`formtype/SKILL.md`](../../../../.claude/skills/formtype/SKILL.md)

## 主要ファイル

- `PriceType.php` — 金額入力。通貨・桁区切り・上下限を EC-CUBE 設定から自動適用（`MoneyType` を親に持つ）
- `AddressType.php` — 都道府県 + 住所1 + 住所2 をまとめた住所入力（`required` に応じ `NotBlank` を後付け）
- `MasterType.php` — マスタ（`mtb_*`）を選択肢にするドロップダウンの基底（`EntityType` ベース）。`Master/` 配下が個別実装
- `Front/EntryType.php` — 会員登録フォーム。フロント系フォームの代表例
- `Admin/ProductType.php` / `Admin/OrderType.php` — 管理画面の商品・受注編集フォーム
