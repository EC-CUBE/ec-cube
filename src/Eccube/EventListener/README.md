# EventListener — イベント購読による拡張

EC-CUBE の拡張は「コアを書き換えず、イベントを購読する」のが基本方針。
このディレクトリには、コア自身が Symfony の `EventDispatcher` に登録する
イベントサブスクライバ（`EventSubscriberInterface` 実装）が置かれ、
リクエスト／レスポンス／認証などライフサイクルの節目にフックして横断的な処理を行う。

- 📖 仕様（人間向け）: [README.html](./README.html)
- 🛠 実装規約（AI 向け）: [`event-subscriber/SKILL.md`](../../../.claude/skills/event-subscriber/SKILL.md)
- 📚 関連: イベント定義 [`../Event/`](../Event/)（`EccubeEvents` / `EventArgs` / `TemplateEvent`）・Doctrine イベント [`../Doctrine/EventSubscriber/`](../Doctrine/EventSubscriber/)

## 主要ファイル

- `TransactionListener.php` — リクエスト単位の DB トランザクション（正常時 commit・例外時 rollback）
- `TwigInitializeListener.php` — Twig グローバル変数の初期化（店舗情報・ログイン状態）
- `MaintenanceListener.php` — メンテナンスモード時のレスポンス差し替え
- `SecurityListener.php` / `LoginHistoryListener.php` — 認証成否にフック（ログイン履歴・ロック）
- `TwoFactorAuthListener.php` — 二段階認証が必要なコントローラの制御
