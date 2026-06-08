# AI コーディング規約（docs/rules）

このディレクトリは、AI コーディングエージェント向けの **レイヤ別コーディング規約** を
ベンダー中立な Markdown として管理する場所です。

## 設計方針

- **本文は 1 ソース**: 規約の実体はこの `docs/rules/*.md` だけに置く。GitHub 上でも素のまま読める。
- **常時読み込まない**: コンテキスト肥大を避けるため、必要なレイヤのときだけ参照する（オンデマンド）。
- **各ツールには薄い発火スタブ（Skill）**: Claude Code / Cursor / Codex / Antigravity には
  `SKILL.md` のスタブだけを置き、本文（このディレクトリ）を読み込ませる。

```
docs/rules/<layer>.md                      ← 規約の本文（実体・1 ソース）
.claude/skills/eccube-<layer>/SKILL.md     ← Claude Code 用スタブ
.codex/skills/eccube-<layer>/SKILL.md      ← Codex CLI 用スタブ
.agents/skills/eccube-<layer>/SKILL.md     ← Google Antigravity 用スタブ
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
| Entity | `entity.md` | 予定 |
| Repository | `repository.md` | 予定 |
| Controller | `controller.md` | 予定 |
| FormType | `formtype.md` | 予定 |
| Service | `service.md` | 予定 |
| Twig / CSS / JS | `twig.md` / `css.md` / `js.md` | 予定 |
| YAML 設定 | `yaml.md` | 予定 |
