---
name: review-responsibility
description: EC-CUBE 4.4 で実装・改修したコードを実装直後に自己レビューする全層チェックリスト。「責務分離を確認して」「実装後のレビューをして」「Fatコントローラ/Fatサービスになってないか見て」「レイヤ違反がないか確認して」「認可/CSRF/XSSの抜けを確認して」「リファクタの観点を出して」などと言われたとき、またはコントローラ/サービス/フォーム/テンプレート等の実装・改修が一区切りついた直後に使用する。責務分離・セキュリティ・レイヤ違反を横断的に点検する。
---

# 実装直後の自己レビュー（全層チェックリスト）

実装・改修が一区切りついたら、変更差分を次の観点で横断的に点検する。
**これは助言であり、必ずしも全件修正を要求しない**（既存コードの一括修正は求めない）。

> 行数・依存数などの数値で線を引かず、**質的シグナル**で判断する。
> 整形・型・アノテーション変換は `rector --dry-run` / `phpstan analyse src` / `php-cs-fixer fix` に委ね、レビューは下記の観点に集中する（QA 実行方法は AGENTS.md「開発コマンド」を参照）。

## 進め方

1. 変更したファイルが**どの層に属すか**を洗い出す。
2. 各層に対応する Skill の「よくある間違い」を**正典として読み込み**、差分を照合する（詳細は再記述せず、各 Skill を参照する）。
3. 層をまたぐ観点（下記「層境界」）を最後に確認する。
4. 指摘は「**新規・改修分**」を優先。具体的な是正案（どの処理をどこへ出すか）を添える。

## 層ごとの観点（対応 Skill の「よくある間違い」を参照）

### 責務分離（コントローラ / サービス） — Skill [`controller`](../controller/SKILL.md) / [`service`](../service/SKILL.md)
- 業務ロジック（金額・送料・ポイント計算、複数 Repository 横断、外部連携、メール送信）がコントローラに残っていないか → **Service へ抽出**。
- 受注の計算・検証・確定（在庫引当・採番・ポイント付与・値引き）が **PurchaseFlow 外**に書かれていないか → 該当 Processor/Validator へ。
- コントローラ内に `$em->persist()` / `$em->flush()` の**業務的な直書き**がないか → Service へ。
- Service が `Request`/`Response` に依存していないか → レイヤ違反（依存は Controller → Service → Repository の一方向）。
- 同一処理のコピペが複数箇所にないか → 共通 Service メソッドへ。

### セキュリティ — Skill [`security`](../security/SKILL.md)
- 新規の管理アクションが **`%eccube_admin_route%` 配下**に置かれているか（firewall 保護下か）。
- GET 以外の状態変更（更新・削除・Ajax）で **CSRF が検証**されているか（フォーム経由 or `$this->isTokenValid()`）。
- フロントで `{id}` から取得したリソースに**所有権チェック**があるか（**IDOR**）。
- パスワード変更・退会など重要操作が `IS_AUTHENTICATED_FULLY` で守られているか。

### Twig 拡張・テンプレート — Skill [`twig-template`](../twig-template/SKILL.md)
- ユーザー入力・DB 値を `|raw` で出力していないか（**反射型/蓄積型 XSS**）。JS 文脈で `|escape('js')` を使っているか。
- **蓄積型（stored）XSS**: DB に保存したユーザー入力（レビュー・コメント・氏名等）を表示する箇所で `|raw` していないか／入力時のサニタイズに頼って出力エスケープを省いていないか（保存値は常に未信頼として出力時にエスケープ）。
- `is_safe => ['html']` を付けた関数内で外部入力を未エスケープ連結していないか。
- テンプレート上書きのパス・名前空間（`@admin` 等）が正しいか。

### イベント — Skill [`event-subscriber`](../event-subscriber/SKILL.md)
- リスナー/サブスクライバに**業務ロジックが偏っていないか**（重い処理は Service へ委譲、リスナーは薄く）。
- `getSubscribedEvents()` が `static` か、イベント名に `EccubeEvents` 定数を使っているか。

### Entity — Skill [`entity`](../entity/SKILL.md)
- 金額（DECIMAL）を int/float 扱いしていないか（`?string`／bcmath）。`create_date`/`update_date` を自前 PrePersist で二重実装していないか。
- 他エンティティ（特にコアの `Product`/`Customer` 等）への関連で、親削除時の挙動（`onDelete` or Service/disable での後始末）を決めているか（未決定だと退会・商品削除を FK で止める）。

### Repository — Skill [`repository`](../repository/SKILL.md)
- 生 SQL 連結でなく QueryBuilder＋`setParameter` か。画面表示の一覧・関連取得が無制限になっていないか（ページング/上限）。

### プラグイン — Skill [`plugin`](../plugin/SKILL.md)
- エンティティトレイトに `#[EntityExtension]` を付け、**proxy 再生成**を意識しているか。
- プロジェクト固有の改変を不要にプラグイン化していないか（`app/Customize` との使い分け）。

## 層境界（横断観点・per-layer では拾えないもの）

- コントローラ/サービスが**未エスケープのユーザー入力を Twig に渡し**、テンプレート側で `|raw` 出力していないか（security × twig）。
- フォーム未経由の入力（Ajax/API）が**バリデーションと CSRF の両方**を通っているか（controller × formtype × security）。
- イベントリスナー内で**認可・所有権チェックを迂回**していないか（event × security）。

## 提案のまとめ方

- **新規・改修したコードの指摘**を優先して提示する。
- 既存（未変更）コードの問題は、無理に直さず「将来のリファクタ候補」として軽く触れるに留める。
- 各指摘に「どの処理を、どこ（どの Service/Processor、どのエスケープ）へ」の具体案を添える。
