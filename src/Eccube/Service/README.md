# Service — 業務ロジックの置き場所

コントローラ（HTTP・画面）から切り離した業務ロジックの受け皿。税の算出・カート操作・受注ステータス遷移・
プラグイン導入・メール送信・CSV 入出力・PDF 出力などを、単一責任・HTTP 非依存で集約する。
受注に関わる計算・検証・確定は汎用 Service ではなく `PurchaseFlow/` が担う。

- 📖 仕様（人間向け）: [README.html](./README.html)
- 🛠 実装規約（AI 向け）: [`service/SKILL.md`](../../../.claude/skills/service/SKILL.md)
- 📚 関連: 受注処理は [`PurchaseFlow/`](./PurchaseFlow/README.html) ／ CSV は [`csv/SKILL.md`](../../../.claude/skills/csv/SKILL.md) ／ メールは [`mail/SKILL.md`](../../../.claude/skills/mail/SKILL.md)

## 主要ファイル

- `CartService.php` — カートの取得・商品追加/削除・保存（セッション/永続化カートの統合）
- `TaxRuleService.php` — 税額算出（`getTax()` / `calcTax()`）と税込価格・丸め
- `OrderHelper.php` — カートから購入処理中（`PROCESSING`）受注を組み立てる入口
- `OrderStateMachine.php` — 受注ステータス遷移（ポイント・在庫の確定/巻き戻しをイベントで実行）
- `PluginService.php` — プラグインの install/enable/disable/uninstall
- `PurchaseFlow/` — 受注処理パイプライン（別途 [README](./PurchaseFlow/README.html)）
