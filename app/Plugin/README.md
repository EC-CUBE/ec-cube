# app/Plugin — プラグイン設置先

インストール済みプラグインが置かれる場所。1 プラグイン = `app/Plugin/{PluginCode}/` の 1 ディレクトリで、
PSR-4 で `Plugin\{PluginCode}\` にマッピングされる。中身は環境依存（インストール状況次第）で、
`.gitkeep` を除き `.gitignore` 済み。素の状態では空。

- 📖 仕様（人間向け）: [README.html](./README.html)
- 🛠 実装規約（AI 向け）: [`plugin/SKILL.md`](../../.claude/skills/plugin/SKILL.md)
- 🧩 ライフサイクル基盤（コア）: [`src/Eccube/Plugin/`](../../src/Eccube/Plugin/README.md)

## ここに置かれるもの

- `{PluginCode}/composer.json` — 必須。`version` と `extra.code` が必須
- `{PluginCode}/PluginManager.php` — 任意。ライフサイクル処理（`AbstractPluginManager` 継承）
- `{PluginCode}/DoctrineMigrations/Version*.php` — プラグイン専用マイグレーション（`migration_{code}` テーブルで管理）
- `{PluginCode}/Controller/` `Entity/` `Form/Extension/` `Resource/template/` — 各拡張点

## 注意

- 実運用プラグインはコミットされない（環境依存・gitignore 済み）。リポジトリ内の `HogePlugin` 等は **PHPUnit のテスト用サンプル**。
- 雛形は手書きせず `bin/console eccube:plugin:generate <name> <code> <ver>` で生成する。
