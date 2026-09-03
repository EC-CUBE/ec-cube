# DependencyInjection — DI 拡張とコンテナ組み立て

EC-CUBE を Symfony DI コンテナに載せるための拡張ポイント。設定読み込み・プラグイン有効/無効判定・
サービスのタグ自動配線といった、コンテナのブート／コンパイル処理を担う。
実行時のリクエスト処理ではなく、コンテナのビルドフェーズで動くコードが中心。

- 📖 仕様（人間向け）: [README.html](./README.html)
- 🔗 関連: [Attribute](../Attribute/README.md) ・ [PurchaseFlow](../Service/PurchaseFlow/README.md)

## 主要ファイル

- `EccubeExtension.php` — バンドル拡張。`prepend()` で `/admin`・`/mypage` の access_control を**動的生成**し、DB を直接読んでプラグインの有効/無効を判定
- `Configuration.php` — `eccube` 設定スキーマ（`rate_limiter` セクション）
- `Compiler/PurchaseFlowPass.php` — 受注処理 3 フローへ Processor/Validator を配線（YAML タグ＋属性の 2 経路）
- `Compiler/PluginPass.php` — 無効プラグインのサービスタグをクリアして拡張機構を無効化
- `Compiler/AutoConfigurationTagPass.php` — doctrine.event_subscriber 等のタグを自動付与
- `Facade/` — DI 外の静的コンテキスト向け singleton ファサード（`LoggerFacade` / `TranslatorFacade`）
