# AI コーディング規約（docs/rules）

このディレクトリは、AI コーディングエージェント向けの **レイヤ別コーディング規約** を
ベンダー中立な Markdown として管理する場所です。

## 設計方針

- **本文は 1 ソース**: 規約の実体はこの `docs/rules/*.md` だけに置く。GitHub 上でも素のまま読める。
- **常時読み込まない**: コンテキスト肥大を避けるため、必要なレイヤのときだけ参照する（オンデマンド）。
- **各ツールには薄い発火スタブ（Skill）**: Claude Code / Cursor / Codex / Antigravity には
  `SKILL.md` のスタブだけを置き、本文（このディレクトリ）を読み込ませる。

```
docs/rules/<layer>.md                ← 規約の本文（実体・1 ソース）
.claude/skills/<layer>/SKILL.md      ← Claude Code 用スタブ
.codex/skills/<layer>/SKILL.md       ← Codex CLI 用スタブ
.agents/skills/<layer>/SKILL.md      ← Google Antigravity 用スタブ
```

Cursor は `.claude/skills/` と `.codex/skills/` を互換読み込みするため、専用スタブは不要です。
Gemini CLI は Skill 非対応のため、`GEMINI.md` から本文を参照します。

## スタブの同期

各ツール用スタブは同一内容です。`.claude/skills/` を正本として、他ディレクトリへ同期します。

```bash
php tools/sync-ai-skills.php
```

> symlink は Windows 環境で壊れやすいため、OSS では採用していません（薄いスタブの複製で対応）。

## 規約の書き方

- 対象は **EC-CUBE 4.4（Symfony 7.4 / PHP 8.2+）の実コード**。古いバージョンの慣習を持ち込まない。
- 「推測」を載せない。必ず `src/Eccube/` の実装で裏取りしてから記述する。
- 各ファイルは「対象」「基本ルール」「実装パターン」「よくある間違い」「実行/確認方法」の構成を推奨。

## 規約一覧

| レイヤ | ドキュメント | 状態 |
|--------|--------------|------|
| PHPUnit テスト | [`phpunit.md`](./phpunit.md) | ✅ |
| Controller（責務分離・Fat化防止） | [`controller.md`](./controller.md) | ✅ |
| Service（責務分離・単一責任） | [`service.md`](./service.md) | ✅ |
| マイグレーション（スキーマ変更） | [`migration.md`](./migration.md) | ✅ |
| Entity | [`entity.md`](./entity.md) | ✅ |
| Repository | [`repository.md`](./repository.md) | ✅ |
| FormType | [`formtype.md`](./formtype.md) | ✅ |

> 規約は網羅を目的とせず、**必要になった観点を必要なときに**追加する方針。
> 新しい規約は上記と同じ構成（本文 `*.md` ＋ 各ツールの Skill スタブ）で足す。

## Skill の命名規則

リポジトリ内のスキルのため、冗長な `eccube-` 接頭辞は付けない。

- **レイヤ規約系（自動発火）**: トピック名 …… `controller` / `service` / `entity` / `phpunit` 等。
- **アクション系（人が明示実行）**: 動詞前置 …… `review-responsibility` 等。

> 留意: Cursor / Codex はグローバル `~/.claude/skills` 等も併読するため、ごく稀に名前が衝突しうる。
> 実害が出た場合のみ接頭辞の再導入を検討する。

## 責務分離・Fat 化防止について

Fat コントローラ / Fat サービスや責務分離の崩れは、本プロジェクトでは **CI で既存コードを落とす方式は採らず**、
次の「書く時」「実装直後」の 2 点で防ぐ方針（段階導入の第1段階。詳細は `controller.md` / `service.md`）:

- **書く時**: Skill `controller` / `service` が各規約を参照させ、薄い実装・単一責任を促す。
- **実装直後**: Skill `review-responsibility` が `tools/check-architecture.php` を実行し、全層の観点を可視化。

`tools/check-architecture.php` は依存追加なし・助言用（CI を落とさない。メソッド長／依存数／
Controller の persist・flush 直書き／Service の Controller 依存=レイヤ違反 を報告）。
将来、合意が取れれば PHPMD / Deptrac＋ベースラインで段階的に強化できる。
