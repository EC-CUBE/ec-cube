# Attribute — EC-CUBE 独自の PHP 属性

クラス・プロパティ・メソッドに「印（マーカー）」を付けるための PHP8 属性群。
属性自体はロジックを持たず、付けられた印を CompilerPass・EventListener・拡張サービスが
Reflection で拾って挙動を変える。受注フロー登録・エンティティ/フォーム拡張・アクセス制限の 3 系統。

- 📖 仕様（人間向け）: [README.html](./README.html)
- 🔗 関連: [PurchaseFlow](../Service/PurchaseFlow/README.md) ・ [DependencyInjection](../DependencyInjection/README.md)

## 主要ファイル

- `CartFlow.php` / `ShoppingFlow.php` / `OrderFlow.php` — 受注処理パイプラインへの登録マーカー（`PurchaseFlowPass` が拾う）
- `EntityExtension.php` — トレイトを既存エンティティへ合成する対象を指定（`IS_REPEATABLE`。`EntityProxyService` が拾う）
- `FormAppend.php` — 拡張プロパティを既存フォームへ差し込む設定（`DoctrineOrmExtension` が拾う）
- `ForwardOnly.php` — コントローラアクションを forward 専用にし直接アクセスを拒否（`ForwardOnlyListener` が拾う）
