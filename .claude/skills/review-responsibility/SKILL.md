---
name: review-responsibility
description: EC-CUBE 4.4 で実装・改修したコードの責務分離をチェックする。「責務分離を確認して」「Fatコントローラ/Fatサービスになってないか見て」「実装後のレビューをして」「リファクタの観点を出して」「レイヤ違反がないか確認して」などと言われたとき、またはコントローラ/サービスの実装・改修が一区切りついた直後に使用する。業務ロジックの偏りやレイヤ違反を点検する。
---

<!--
  このファイルは tools/sync-ai-skills.php により同期される Skill スタブです。
  正本は .claude/skills/ 配下。編集は必ず正本に対して行い、`php tools/sync-ai-skills.php` を実行してください。
  .codex/skills/・.agents/skills/ 配下は生成物のため直接編集しないこと。
-->

# 責務分離レビュー（実装直後の自己チェック）

コントローラ/サービスの実装・改修が一区切りついたら、次の手順で責務分離を点検する。
**これは助言であり、必ずしも全件修正を要求するものではない**（既存コードの一括修正は求めない）。

## 手順

### 1. 機械的な観点の可視化（依存追加なし）

変更した Controller / Service を検査スクリプトにかける:

```bash
php tools/check-architecture.php --changed
```

各レイヤについて次を報告する（**数値の線引きはツールに任せ、レビューは質的判断に集中する**）:
- 共通: メソッド長・コンストラクタ依存数（計測値の可視化。閾値超過＝即修正ではなく、見直しのきっかけ）
- Controller: `persist`/`flush` の直書き（業務的な永続化は Service へ）
- Service: Controller への依存（レイヤ違反。依存は Controller → Service → Repository の一方向）

特定ファイルは `php tools/check-architecture.php <path>`。
整形・型・アノテーション変換は `vendor/bin/rector process --dry-run` / `phpstan analyse src` / `php-cs-fixer fix` で別途担保する。

### 2. 規約に照らした目視レビュー

変更差分について、対応する規約を読み込んで確認する:
- コントローラ: [`docs/rules/controller.md`](../../../docs/rules/controller.md)
- サービス: [`docs/rules/service.md`](../../../docs/rules/service.md)

確認観点（質的シグナル）:
- 業務ロジック（金額・送料・ポイント計算、複数 Repository 横断、外部連携、メール送信）が
  コントローラに残っていないか → あれば **Service への抽出**を提案。
- 受注の計算・検証・確定（在庫引当・採番・ポイント付与・値引き）が **PurchaseFlow 外**に書かれていないか
  → 該当する Processor/Validator への移動を提案。
- Service が Controller / HTTP（`Request`/`Response`）に依存していないか → レイヤ違反は是正を提案。
- 同じ処理が複数箇所にコピペされていないか → 共通の Service メソッドへ。
- 1 つのクラス/メソッドに無関係な責務が同居していないか（行数の多寡そのものではなく、関心事の数で見る）。

### 3. 提案のまとめ方

- **新規・改修したコードの指摘**を優先して提示する。
- 既存（変更していない）コードの Fat は、無理に直さず「将来のリファクタ候補」として軽く触れるに留める。
- 各指摘に「どの処理を、どの Service の何というメソッドへ出すか」の具体案を添える。
