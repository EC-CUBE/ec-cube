# Plugin 基盤（コア）

プラグインの**ライフサイクル基盤**を提供する階層。実体は `AbstractPluginManager.php` のみで、
各プラグインの `PluginManager` はこれを継承する。プラグイン本体は置かず（本体は `app/Plugin/{PluginCode}/`）、
install/enable/disable/update/uninstall の共通処理と `migration()` ヘルパを持つ。

- 📖 仕様（人間向け）: [README.html](./README.html)
- 🛠 実装規約（AI 向け）: [`plugin/SKILL.md`](../../../.claude/skills/plugin/SKILL.md)
- 📦 プラグイン本体の置き場: [`app/Plugin/`](../../../app/Plugin/README.md)

## 主要ファイル

- `AbstractPluginManager.php` — 全 `PluginManager` の基底。5 つのライフサイクルメソッド（すべて no-op 既定）と、
  プラグイン専用マイグレーションを回す `migration()`（既定で `app/Plugin/{code}/DoctrineMigrations`、管理テーブルは `migration_{code}`）

## コア機構本体（別階層）

ライフサイクルを駆動する実処理はこの階層ではなく次にある。

- `src/Eccube/Service/PluginService.php` — install/enable/disable/uninstall の中核
- `src/Eccube/Service/Composer/` — Composer 連携（依存取得・削除）
- `src/Eccube/Command/Plugin*Command.php` — `eccube:plugin:generate|install|enable|disable|update|uninstall`
