# Admin/Order — 管理画面の受注管理

管理画面から受注（`Order`）を操作するコントローラ群。受注の一覧・検索・登録・編集、
出荷（`Shipping`）の編集・出荷通知、受注メール送信、出荷 CSV 取込を担う。
受注の登録・編集は店頭購入と同じ受注処理パイプライン（PurchaseFlow）を `order` フローで実行する。

- 📖 仕様（人間向け）: [README.html](./README.html)
- 🛠 実装規約（AI 向け）: [`controller/SKILL.md`](../../../../../.claude/skills/controller/SKILL.md)
- 📚 ドメイン詳細: https://doc4.ec-cube.net/spec_order

## 主要ファイル

- `OrderController.php` — 受注一覧・検索・一括削除・CSV/PDF 出力・出荷ステータス／伝票番号更新
- `EditController.php` — 受注の新規登録・編集。PurchaseFlow の `validate`/`prepare`/`commit` を管理操作から実行
- `ShippingController.php` — 出荷編集と出荷完了通知メールのプレビュー／送信
- `MailController.php` — 受注に対する任意メールの作成・送信（`MailHistory` に記録）
- `CsvImportController.php` — 出荷情報 CSV の取込とテンプレート配布（`AbstractCsvImportController` 継承）
