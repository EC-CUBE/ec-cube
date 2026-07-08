# app/DoctrineMigrations — マイグレーション

コア本体の DB マイグレーション（`VersionYYYYMMDDHHMMSS.php`、名前空間 `DoctrineMigrations`）を置く場所。
**スキーマの源泉は Entity 属性（`#[ORM\...]`）であってマイグレーションではない**ため、
単純なカラム追加は不要（`schema:update` が反映）。書くのは INSERT（マスタ/初期データ）と型変更等に限る。

- 📖 仕様（人間向け）: [README.html](./README.html)
- 🛠 実装規約（AI 向け）: [`migration/SKILL.md`](../../.claude/skills/migration/SKILL.md)

## 主要ファイル / 実例

- `Version20240312170000.php` — `dtb_block` に「新着商品」ブロックを INSERT（CSV 追記とセット運用の実例）
- `Version20220603074035.php` — `mtb_csv_type` のマスタ追加（INSERT マイグレーション）
- `Version20230515023836.php` — `dtb_mail_template` へのレコード投入
- `Version20260316234241.php` — 構造変更の例（`hasColumn` ガード付きで `ALTER ... ADD`）
- 命名は `VersionYYYYMMDDHHMMSS.php`。`up()`/`down()` を対で実装し、存在チェックで冪等にする。

## 注意

- 生成は `doctrine:migrations:generate`（空雛形＋手書き）。`diff` は使わない。
- プラグインのマイグレーションはここではなく各プラグインの `DoctrineMigrations/`（`migration_{code}` テーブル）。
