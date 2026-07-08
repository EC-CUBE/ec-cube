# app/Customize — プロジェクト固有カスタマイズ

コア（`src/Eccube/`）を直接書き換えず、プロジェクト固有の改変を拡張・上書きで行う場所。
PSR-4 で `Customize\` = `app/Customize/` にマッピングされ、autowire / autoconfigure 済み。
着脱・再配布できる機能は `app/Plugin/` を使う（こちらは 1 回限りの改変向け）。

- 📖 仕様（人間向け）: [README.html](./README.html)
- 🛠 実装規約（AI 向け）: [`customize/SKILL.md`](../../.claude/skills/customize/SKILL.md)
- 🧩 着脱式の機能: [`app/Plugin/`](../Plugin/README.md)

## 主要ディレクトリ

- `Controller/` — `#[Route]` を持つコントローラの追加（`customize_controllers` が属性走査）
- `Entity/` — コアエンティティ拡張トレイト（`#[EntityExtension(対象::class)]` ＋ `#[ORM\Column]`。追加のみ）
- `Resource/config/` — サービス定義等
- `Resource/locale/` — 翻訳
- テンプレート上書きは `app/Customize/` ではなく `app/template/`（コアと同じ相対パスに同名ファイル）

## 注意

- 「アップグレード安全」はパッチ更新まで。override はコアのパッチ再適用が要るため最小限に。
- カラム追加にマイグレーションは不要（属性＋`schema:update`）。型変更・データ投入は `app/DoctrineMigrations/`。
