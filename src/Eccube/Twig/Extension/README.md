# Twig/Extension — Twig 拡張

Twig テンプレートから使える独自のフィルタ・関数・テストを定義するディレクトリ。
価格表示 `{{ value|price }}`・日付整形 `{{ value|date_format }}`・商品取得 `{{ product(id) }}` など、
テンプレート頻出の処理を PHP 側に集約する。`AbstractExtension` を継承し `autoconfigure` で自動登録される。

- 📖 仕様（人間向け）: [README.html](./README.html)
- 🛠 実装規約（AI 向け）: [`twig-template/SKILL.md`](../../../../.claude/skills/twig-template/SKILL.md)

## 主要ファイル

- `EccubeExtension.php` — コアで最も使う汎用拡張（`price` / `date_format` / `ellipsis` / `product()` / `has_errors()` 等）
- `TaxExtension.php` — 軽減税率対象かの判定 `is_reduced_tax_rate()`
- `CsrfExtension.php` — CSRF トークン出力 `csrf_token_for_anchor()`（`is_safe` の付与例）
- `TemplateEventExtension.php` — テンプレートイベント差し込みの基盤（NodeVisitor）。プラグイン/カスタマイズの拡張点
- `RepositoryExtension.php` — テンプレートからリポジトリを参照する `repository()`
