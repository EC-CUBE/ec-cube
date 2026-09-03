# Controller — HTTP 入出力層

EC-CUBE の HTTP リクエストを受け取りレスポンスを返す入口。URL（ルーティング）ごとに 1 アクションが対応し、
店頭（フロント）と管理画面の画面・API を提供する。業務ロジックは自分で持たず、
Service や受注処理パイプライン（PurchaseFlow）へ委譲して薄く保つ。

- 📖 仕様（人間向け）: [README.html](./README.html)
- 🛠 実装規約（AI 向け）: [`controller/SKILL.md`](../../../.claude/skills/controller/SKILL.md)

## 主要ファイル

- `AbstractController.php` — 全コントローラの基底。フラッシュメッセージ（`addSuccess`/`addError` 等）・CSRF 検証（`isTokenValid()`）・`forwardToRoute()` を提供
- `AbstractShoppingController.php` — 購入系の基底。`executePurchaseFlow()` で受注処理パイプラインへ検証・計算を委譲
- `CartController.php` / `ShoppingController.php` — 店頭のカート・購入手続き
- `ProductController.php` / `EntryController.php` — 商品閲覧・会員登録
- `Admin/` — 管理画面コントローラ群（受注・商品・会員・設定などを機能単位で分割）／`Mypage/` — 会員向けマイページ
