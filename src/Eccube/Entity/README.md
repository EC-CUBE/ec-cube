# Entity — Doctrine エンティティ

EC-CUBE のデータ構造を表す Doctrine エンティティ。会員・商品・受注・カート等の業務データを、
PHP8 属性 `#[ORM\...]` でマッピングしたクラスとして持つ。**スキーマの源泉はこの属性**。

- 📖 仕様（人間向け）: [README.html](./README.html)
- 🛠 実装規約（AI 向け）: [`entity/SKILL.md`](../../../.claude/skills/entity/SKILL.md)
- 📚 ドメイン詳細: https://doc4.ec-cube.net/spec_order

## 主要ファイル

- `AbstractEntity.php` — 全データ系エンティティの基底
- `Master/AbstractMasterEntity.php` — マスタ系（`mtb_*`）の基底
- `Customer.php` / `Member.php` — 会員・管理者ユーザー
- `Product.php` / `ProductClass.php` — 商品と規格（在庫・価格は `ProductClass` 単位）
- `Order.php` / `OrderItem.php` / `Shipping.php` — 受注・明細・出荷（STI、`discriminator_type` で型区別）
