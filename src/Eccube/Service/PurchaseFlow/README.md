# PurchaseFlow — 受注処理パイプライン

受注に関わる計算・検証・確定（送料/手数料/税/値引き/ポイント/在庫引当・採番）を、
コントローラや汎用サービスに散らさず 1 本のパイプラインに集約する EC-CUBE 受注処理の中核。
cart / shopping / order の 3 フローぶん存在する。

- 📖 仕様（人間向け）: [README.html](./README.html)
- 🛠 実装規約（AI 向け）: [`purchase-flow/SKILL.md`](../../../../.claude/skills/purchase-flow/SKILL.md)
- 📚 ドメイン詳細: https://doc4.ec-cube.net/spec_order

## 主要ファイル

- `PurchaseFlow.php` — パイプライン本体。7 種の拡張点を保持し `validate()` / `prepare()` / `commit()` / `rollback()` を実行
- `PurchaseContext.php` — 実行中コンテキスト（フロー種別・実行前の状態を保持）
- `ProcessResult.php` / `PurchaseFlowResult.php` — 各処理の結果（success/warn/error）とその集約
- `ItemValidator.php` / `ItemHolderValidator.php` / `ItemHolderPostValidator.php` — 検証系の基底（abstract）
- `ItemPreprocessor.php` / `ItemHolderPreprocessor.php` / `DiscountProcessor.php` / `PurchaseProcessor.php` — 前処理・値引き・確定の拡張点（interface）
- `Processor/` — 標準実装（`StockValidator` `TaxProcessor` `PointProcessor` `StockReduceProcessor` 等）
