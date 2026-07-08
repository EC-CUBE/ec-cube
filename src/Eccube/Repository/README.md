# Repository — データアクセス

エンティティの取得・保存・削除を担う Doctrine リポジトリ。DB への問い合わせ（検索・一覧の絞り込み）を
コントローラやサービスに散らさず、`AbstractRepository<T>` を継承した各リポジトリに集約する。
**責務はデータアクセスのみ**（業務ロジックは Service / PurchaseFlow へ）。

- 📖 仕様（人間向け）: [README.html](./README.html)
- 🛠 実装規約（AI 向け）: [`repository/SKILL.md`](../../../.claude/skills/repository/SKILL.md)
- 📚 ドメイン詳細: https://doc4.ec-cube.net/spec_order

## 主要ファイル

- `AbstractRepository.php` — 全リポジトリの基底。`save()` / `delete()` を提供
- `ProductRepository.php` — 商品検索（`getQueryBuilderBySearchData` / `...ForAdmin`）
- `OrderRepository.php` — 受注検索・ステータス変更・会員別受注一覧
- `CustomerRepository.php` — 会員検索・認証まわりの取得
- `Master/` — マスタ系（`mtb_*`）リポジトリ
