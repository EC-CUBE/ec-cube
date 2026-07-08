# Command — コンソールコマンド／バッチ

`bin/console` から実行するコンソールコマンド（バッチ・cron 用途含む）の置き場所。
インストール・プロキシ生成・プラグイン管理・ダミーデータ生成・不要データ削除など、
Web 画面を介さない運用・保守処理を集約する。コマンドは「もう 1 つの入口」であり、
業務ロジックは Service／Repository へ委譲して薄く保つ。

- 📖 仕様（人間向け）: [README.html](./README.html)
- 🛠 実装規約（AI 向け）: [`command/SKILL.md`](../../../.claude/skills/command/SKILL.md)

## 主要ファイル

- `InstallerCommand.php` — `eccube:install`（初期インストール）
- `GenerateProxyCommand.php` — `eccube:generate:proxies`（エンティティプロキシ再生成）
- `DeleteCartsCommand.php` — `eccube:delete-carts`（トランザクション境界を持つバッチの手本）
- `GenerateDummyDataCommand.php` — `eccube:fixtures:generate`（オプション／バッチ flush の手本）
- `PluginEnableCommand.php` ほか `Plugin*Command.php` — プラグインのライフサイクル操作
