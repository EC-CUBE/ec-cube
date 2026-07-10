# Form — フォーム

会員登録・お問い合わせ・購入手続き（フロント）や商品・受注・会員の編集/検索（管理画面）で使う
フォーム機能一式を置くレイヤ。フォーム型本体（`Type/`）に加え、既存フォームへの横断拡張・独自バリデーション・
送信値の前処理・値変換も同居する。

- 📖 仕様（人間向け）: [README.html](./README.html)
- 🛠 実装規約（AI 向け）: [`formtype/SKILL.md`](../../../.claude/skills/formtype/SKILL.md)

## サブディレクトリ

- `Type/` — フォーム型本体（1 型 = 1 クラス）。用途別に `Admin/` `Front/` `Shopping/` `Master/` `Install/`
- `Extension/` — 既存 FormType への横断拡張（`AbstractTypeExtension`）。例: `HTMLPurifierTextTypeExtension` / `DoctrineOrmExtension`（`#[FormAppend]` 自動追加）
- `Validator/` — フォーム用の独自バリデーション（`Constraint` ＋ `ConstraintValidator`）。`Email` / `PasswordBlocklist` / `TwigLint`
- `EventListener/` — 送信値の前処理（`PRE_SUBMIT`）。かな変換・ハイフン除去・HTML 無害化。※カーネルの `src/Eccube/EventListener/` とは別
- `DataTransformer/` — フォーム値とモデルの相互変換。`EntityToIdTransformer`（ID ⇔ Entity）
