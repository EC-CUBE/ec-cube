---
name: contributing
description: EC-CUBE 本体へ Pull Request を出す / CI を通すための手順とチェック。「PRを出したい」「コントリビュートしたい」「CIを通したい」「CIが落ちた原因を直したい」「push前に確認して」などと言われたとき、または PR を作成・更新する直前に使用する。base ブランチ・ライセンスヘッダ・CI ゲートのローカル再現・PR テンプレ・著作権ポリシーをまとめる。
---

# コントリビューション規約 — PR を通すための手順（EC-CUBE 4.4）

**対象**: EC-CUBE 本体（`EC-CUBE/ec-cube`）への Pull Request 作成・更新の全般
**前提**: PHP 8.2+ / Symfony 7.4。各種ツールの基本コマンドは `AGENTS.md`「開発コマンド」を参照。

> 目的: 「実装は正しいのに CI で落ちる / レビューで差し戻される」を減らす。
> push 前に **CI と同じゲートをローカルで再現**し、規約・体裁・テストを揃えてから PR を出す。

## 基本ルール

- **base ブランチは `4.4`**（リポジトリのデフォルトブランチ＝現行リリースライン。PR の向き先もここ）。フォークの作業ブランチから `4.4` 宛に PR する。
- **マイナーバージョンの互換性を壊さない**。既存機能の仕様変更・フックポイントやパラメータの削除/型変更・Service 公開関数のシグネチャ変更・CSV 等の入出力フォーマット変更は **原則取り込まれない**（PR テンプレートのチェックリスト参照）。
- **新規 PHP ファイルにはライセンスヘッダ必須**（PHP-CS-Fixer の `header_comment` で強制。欠落は CI で落ちる）:
  ```php
  <?php

  /*
   * This file is part of EC-CUBE
   *
   * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
   *
   * http://www.ec-cube.co.jp/
   *
   * For the full copyright and license information, please view the LICENSE
   * file that was distributed with this source code.
   */
  ```
- **型宣言を付ける**（PHPStan level 6 を通す）。レイヤ別の実装規約は各 Skill（`controller` / `service` / `entity` / `repository` / `formtype` / `migration` / `phpunit` …）に従う。
- **機能追加・修正にはテストを伴わせる**（PHPUnit。テストの書き方は Skill `phpunit`）。
- **著作権ポリシーへの同意**: コミット＝`.github/CONTRIBUTING.md` のコピーライトポリシー（GPL ＋ 株式会社イーシーキューブへの著作権移転）に同意したものとみなされる。第三者の権利物・職務著作物の無断コミットは禁止。

## PR を出すまでの手順

1. `4.4` から作業ブランチを切る。
2. レイヤ別 Skill に従って実装し、新規ファイルにライセンスヘッダを付ける。
3. テストを書く / 既存テストを壊さないことを確認する。
4. **下記の CI ゲートをローカルで再現**し、すべて通す（変更ファイルに絞ってよい）。
5. 実装直後の自己レビュー（Skill `review-responsibility`）で責務分離・セキュリティ・レイヤ違反を点検。
6. PR テンプレートの各節（概要 / 方針 / 補足 / テスト / 互換性チェックリスト）を埋めて PR を作成。

## CI ゲート（push 前にローカルで再現する）

PR では以下が GitHub Actions で走る。**同じものを手元で先に通す**こと（実体は各ワークフロー `.github/workflows/*.yml`）。

| ゲート | CI が実行するコマンド | ローカルでの確認 |
|---|---|---|
| コードスタイル（`php-cs-fixer.yml`） | `php vendor/bin/php-cs-fixer fix --diff --dry-run --allow-risky=yes` | `vendor/bin/php-cs-fixer fix`（自動修正） |
| 静的解析（`phpstan.yml`） | `vendor/bin/phpstan analyze src/ --error-format=github` | `vendor/bin/phpstan analyse src`（level 6） |
| リファクタ規約（`rector.yml`） | `vendor/bin/rector process --dry-run --ansi --config=rector.php` | `vendor/bin/rector process`（差分適用） |
| ユニットテスト（`unit-test.yml`） | `vendor/bin/phpunit`（一部グループは分割実行） | 変更に関係するテストを `vendor/bin/phpunit <path>` |

- このほか **E2E（`e2e-test.yml`）・プラグインテスト（`plugin-test.yml`）・セキュリティスキャン（zaproxy/vaddy）** が走る。重いので CI に任せてよいが、落ちたら該当ジョブのログを読む。
- **rector は関門になりやすい**（PHP/Symfony/Doctrine の機械的な現代化を強制）。`--dry-run` で出た差分は基本そのまま適用する。

## よくある間違い

- ❌ `master` や旧バージョンブランチ宛に PR → ✅ base は `4.4`
- ❌ 新規 PHP ファイルにライセンスヘッダ無し → ✅ 上記ヘッダを先頭に付ける（cs-fixer が落とす）
- ❌ cs-fixer / phpstan / rector をローカルで回さず push → ✅ push 前に 4 ゲートを通す
- ❌ 機能追加なのにテスト無し / 既存テストを壊す → ✅ テストを伴わせ、関連テストを実行
- ❌ マイナー互換を壊す変更（既存シグネチャ・フック・CSV 仕様の変更）を含める → ✅ 互換チェックリストを確認し、壊す場合は別途相談
- ❌ PR テンプレートの節を空のまま提出 → ✅ 概要・方針・テスト範囲・互換性チェックを埋める
- ❌ `@deprecated` な public API・定数の削除を `src/` と PHPUnit の grep だけで「呼び出し元なし」と判定 → ✅ `e2e/`（Playwright の globalSetup が実行する `setup-fixtures.php`）と `codeception/`（VAddy スキャンが `codecept -g vaddy` を実行）も走査対象に含め、全ツリー `git grep` で 0 件を確認する

## 実行・確認方法

各ツールの基本コマンドは `AGENTS.md`「開発コマンド」を参照。

```bash
# push 前の一括チェック（変更ファイルに絞ってもよい）
vendor/bin/php-cs-fixer fix --dry-run --diff
vendor/bin/phpstan analyse src
vendor/bin/rector process --dry-run --config=rector.php
vendor/bin/phpunit <変更に関係するテスト>
```

- CI が落ちたら、まず該当ジョブのログで「どのゲート・どのファイル・どのルール」かを特定し、ローカルで同じコマンドを再現して直す。
- レビュー（CodeRabbit 等）の指摘は、規約に沿うものは取り込み、判断が要るものは PR コメントで議論する。

---

実装そのものの規約は各レイヤ Skill に従い、push 前に Skill `review-responsibility` で自己点検すること。
