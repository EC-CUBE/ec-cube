# AGENTS.md

このファイルは、AI コーディングエージェント（Claude Code / Cursor / Codex CLI / Google Antigravity / Gemini CLI 等）が
EC-CUBE 4.4 を扱う際に従う共通の入口であり、**ベンダー中立な単一の正典（single source of truth）**です。

**参照方向は一方向のみ**（循環参照を避けるため）:

- `CLAUDE.md` / `GEMINI.md` は、このファイルを参照する薄いポインタです（下流）。
- このファイルは `CLAUDE.md` / `GEMINI.md` を参照し返しません（上流）。
- レイヤ別規約は各 Skill 本文（`.claude/skills/<name>/SKILL.md`）が末端で、上流を参照し返しません。

## AI 向けドキュメント インデックス（定義ファイルと読み込み）

AI エージェント向けの情報は、この `AGENTS.md` を**正典（ハブ）**とし、周辺の定義ファイルはすべてここへ収斂します。所在の一覧は次の通り。

| ファイル | 役割 | 主な読み手 |
|---|---|---|
| `AGENTS.md`（本ファイル） | ベンダー中立の正典。規約・アーキテクチャ・Skill 索引の入口 | 全エージェント＋人間。`AGENTS.md` をネイティブに読むツール（Cursor / GitHub Copilot / Codex CLI / Google Antigravity 等）はこれを直接参照する |
| `CLAUDE.md` | 薄いポインタ（`@./AGENTS.md`）。Claude Code は `CLAUDE.md` をネイティブに読むため | Claude Code |
| `GEMINI.md` | 薄いポインタ。Gemini CLI は Skill 非対応のため索引経由で `SKILL.md` へ誘導 | Gemini CLI |
| `llms.txt` | 外部 LLM・クローラ向けの英語サマリ（llmstxt.org 準拠。公開 URL 前提） | LLM クローラ・外部 LLM |
| `.claude/skills/<name>/SKILL.md` | レイヤ別の詳細規約（末端）。`.codex/skills`・`.agents/skills` は symlink 共有 | Skill 対応ツール（詳細は「Skill の配置と各ツールの読み込み」節） |

### 定義ファイルを増やすときの原則

- **ポインタ定義ファイルは「`AGENTS.md` をネイティブに読まないツール」にだけ置く。** それ以外は正典を二重に指すだけの冗長ファイルになるため作らない。
  - 必要な例: Claude Code → `CLAUDE.md`、Gemini CLI → `GEMINI.md`（いずれも `AGENTS.md` をネイティブに読まない）。
  - 不要な例: **Cursor / GitHub Copilot / Codex CLI は `AGENTS.md` をネイティブに読む**ため、`.cursor/rules/*` や `.github/copilot-instructions.md` は追加しない。
- 新しい定義ファイルを足すときは、上のインデックス表にも 1 行追加し、所在を本節で一元管理する。
- 設計思想・アーキテクチャの詳細文書（`DESIGN.md` / `ARCHITECTURE.md`）は現状未整備。整備する場合も本ファイルはハブに留め、詳細はそれらへリンクして本節の表に追記する。

## プロジェクト概要

EC-CUBE は日本で広く使われる OSS の EC プラットフォームです。本ブランチ（4.4）は **Symfony 7.4 / PHP 8.2+** 上に構築されています。

- リポジトリ: https://github.com/EC-CUBE/ec-cube
- ドキュメント: https://doc4.ec-cube.net/
- ライセンス: GPL-2.0 / 商用デュアルライセンス

## 技術スタック

- **PHP**: 8.2 / 8.3 / 8.4 / 8.5
- **フレームワーク**: Symfony 7.4（フルスタック）
- **ORM**: Doctrine ORM 3.x / DBAL 4.x（マッピングは **PHP8 属性** `#[ORM\...]`）
- **テンプレート**: Twig 3.x
- **データベース**: PostgreSQL 13–18 または MySQL 8.4 LTS
- **フロントエンド**: Sass (SCSS) / esbuild / Bootstrap 5.3 / jQuery 4.x
- **テスト**: PHPUnit 11（`vendor/bin/phpunit` を直接実行）/ Playwright（E2E、`e2e/`）
  - ※ `symfony/phpunit-bridge` は依存にあるが、その `DeprecationErrorHandler`（`SYMFONY_DEPRECATIONS_HELPER`）は **PHPUnit 10 以上では無効**（bridge の `bootstrap.php` が早期 return する）。非推奨の検出は PHPUnit 11 ネイティブの `failOnDeprecation` で行う（`phpunit.xml.dist`）。
  - ※ `codeception/` は残置（レガシー）。CI の Codeception ジョブは無効化（`if: false`）されており、E2E は Playwright が正。
- **静的解析**: PHPStan（`phpstan.neon.dist` で level 6）
- **コードスタイル**: PHP-CS-Fixer（PSR-12）

## ディレクトリ構成

```
src/Eccube/           # コアアプリケーション
  Controller/         # HTTP コントローラ（管理画面・フロント）
  Entity/             # Doctrine エンティティ（#[ORM\...] 属性でマッピング）
  Repository/         # Doctrine リポジトリ
  Service/            # ビジネスロジック
    PurchaseFlow/     # 受注処理パイプライン
  Form/               # FormType・拡張
  Event/              # イベントサブスクライバ
  EventListener/      # イベントリスナ
  Twig/               # Twig 拡張
  Plugin/             # プラグイン管理
  Command/            # コンソールコマンド
  Resource/
    doctrine/         # Doctrine 関連リソース（CSV インポート定義・マイグレーション）
    template/         # コアの Twig テンプレート
    config/           # サービス定義

app/
  Customize/          # プロジェクト固有のカスタマイズ（アップグレード安全）
    Controller/  Entity/  Form/Extension/  Repository/  Service/  Twig/  Resource/template/
  Plugin/             # インストール済みプラグイン
  config/eccube/      # アプリ設定（packages, routes, services）
  template/           # テンプレート上書き
  DoctrineMigrations/ # DB マイグレーション
  proxy/entity/       # 自動生成されるエンティティプロキシ

html/                 # 公開ドキュメントルート
  template/
    admin/assets/     # 管理画面アセット（CSS, JS, 画像）
    default/assets/   # 店頭アセット

tests/
  Eccube/Tests/       # PHPUnit テスト

e2e/                  # Playwright E2E（spec / Page Object / fixtures）
  tests/              # *.spec.ts（CI は e2e-test.yml のマトリクスで 1 ファイル = 1 シャード）
  pages/  models/  helpers/  fixtures/
```

## 開発コマンド

### インストール

```bash
# Docker（推奨）
docker compose -f docker-compose.yml -f docker-compose.pgsql.yml up -d

# Composer
composer create-project ec-cube/ec-cube ec-cube "4.4.x-dev" --keep-vcs
bin/console eccube:install
```

#### Web サーバーと CLI の権限を分離した環境

書き込み先を **レーン W**（リクエスト処理中に書く。Web サーバー所有: `var/runtime`・`var/sessions`・`var/log`・`html/upload/**`）と
**レーン S**（CLI へ移せる。CLI ユーザー所有で Web サーバーは読み取りのみ: `var/build`・`var/cache`・`app/template`・`html/user_data`・
`app/Plugin`・`app/keystore`・`vendor`・`.env` 等）に分け、Web サーバーへ最小限の書き込み権限しか与えずに運用できる。
**分離は任意で、既定は分離しない構成。** レーンの判断基準・実行ユーザーの選び方・`eccube:doctor:permissions` の読み方・
実行時にレーン S へ書かないための実装規約は Skill `eccube-permission-lanes`
（[`.claude/skills/eccube-permission-lanes/SKILL.md`](./.claude/skills/eccube-permission-lanes/SKILL.md)）。
本番のパーミッション設定・デプロイ・4.3 からの移行手順の正本は https://doc4.ec-cube.net/permission （ここには書かない）。

分離した状態を再現するには `docker-compose.permission-lanes.yml` を重ねる。

```bash
# --build は必須。 公開イメージ (ghcr) には dockerbuild/docker-php-entrypoint のレーン分離が
# 含まれないため、 pull されたイメージのままだと www-data がホストユーザーへリマップされ分離されない。
# DB は SQLite だと var/eccube.db を CLI から書けないため、 DB サーバーを重ねる。
docker compose -f docker-compose.yml -f docker-compose.dev.yml -f docker-compose.pgsql.yml \
  -f docker-compose.permission-lanes.yml up -d --build --wait
curl -s -o /dev/null http://127.0.0.1:8080/   # セッションを生成し Web サーバーの uid を判定可能にする
docker compose exec -u eccube ec-cube bin/console eccube:doctor:permissions
```

CLI の実行ユーザーは操作対象のレーンで決める（本番の `sudo -u www-data` に相当）。

```bash
docker compose exec -u eccube   ec-cube bin/console eccube:cache:build        # レーン S を触る操作
docker compose exec -u www-data ec-cube bin/console cache:pool:clear --all    # レーン W を触る操作
```

- 分離した構成は `APP_ENV=prod` 固定（dev はリクエスト処理中にコンテナを再生成するため 500 になる）。
  アクセスを受ける前に `eccube:cache:build` と `eccube:keystore:generate` を実行しておく。
- `cache:clear --no-warmup` は使わない（コンパイル済みコンテナが再生成されず Web サーバーが 500 になる）。
  `eccube:plugin:*` の後は `eccube:cache:build` を別途実行する。
- 分離すると、レーン S へ書き込む管理画面の機能（プラグイン導入・有効化・無効化・アップデート・削除、
  ページ/ブロック/メールテンプレート編集、CSS/JS 編集、ファイル管理、セキュリティ管理、テンプレート選択・追加）は
  動作しなくなる。`ECCUBE_RESTRICT_FILE_UPLOAD=1` を設定すると、これらの画面は**読み取り専用**になり、現在の内容を
  表示したまま保存・削除だけを無効化して代替の CLI コマンドを案内する（対応表は `app/config/eccube/packages/eccube.yaml` の
  `eccube_restrict_file_upload_urls`）。未設定（既定）なら従来どおり、画面は動作し保存時に書き込みエラーになる。
  テンプレートのアップロードは CLI 未整備。
- 関連する環境変数: `ECCUBE_UMASK`（8 進数。既定は空で OS / PHP-FPM の既定に従う。`0000` で 4.3 以前と同じ
  0777 / 0666）、`ECCUBE_CLI_LOG_TO_FILE=0`（`var/log` はレーン W のため CLI のログをコンソールへ寄せる）、
  `ECCUBE_MAINTENANCE_FILE_PATH`（既定はプロジェクトルート直下。分離時は `var/runtime` 配下へ）、
  `ECCUBE_KEYSTORE_STRICT_PERMISSIONS=1`（鍵を 0700 / 0600 にする。既定は Web サーバーが読めるよう 0755 / 0644）、
  `ECCUBE_PERMISSION_LANES=1`（Docker イメージのエントリポイントがユーザーを分けるためのスイッチ）。
  `docker-compose.permission-lanes.yml` では設定済み。
- 既定モードと分離モードを切り替えるときは `docker compose ... down -v` でレーン W のボリュームを作り直す
  （切り替え前の `www-data` の uid で作成されたディレクトリが残り、切り替え後の Web サーバーから書き込めなくなる）。
- この構成は CI でも起動して検証する（`.github/workflows/permission-lanes-test.yml`。Playwright は
  `permission-lanes-tests` project で `e2e/tests/permission-lanes.spec.ts` だけを実行し、HTTPS `4430` を使う）。

分離した構成でレーン S を書き換える導線は下記の CLI。`apply` / `put` は upsert で冪等。いずれも `--dry-run` / `--format=json` に対応し、
`--body=-` で標準入力から本文を読み込む。

| 対象 | サブコマンド | 書き込み先 |
|---|---|---|
| `bin/console eccube:page:*` | `list` / `show` / `apply` / `remove` | `dtb_page` + `app/template/**` |
| `bin/console eccube:block:*` | `list` / `show` / `apply` / `remove` | `dtb_block` + `app/template/**` |
| `bin/console eccube:mail-template:*` | `list` / `show` / `apply` / `remove` | `dtb_mail_template` + `app/template/**` |
| `bin/console eccube:asset:*` | `show` / `apply` | `html/user_data/assets/{css,js}/customize.*` |
| `bin/console eccube:user-data:*` | `list` / `show` / `put` / `remove` | `html/user_data/**` |
| `bin/console eccube:env:*` | `get` / `set` | `.env` |
| `bin/console eccube:keystore:*` | `list` / `show` / `generate` | `app/keystore/**` |
| `bin/console eccube:contents:*` | `export` / `import` | `app/contents/*.yaml`（書き出し）／上記の DB とファイル（取り込み） |
| `bin/console eccube:plugin:*` | `install` / `enable` / `disable` / `uninstall` / `update` / `schema-update` | `app/Plugin` / `app/proxy` / `vendor` |

```bash
bin/console eccube:page:list
bin/console eccube:page:show --route=guide > guide.twig
cat guide.twig | bin/console eccube:page:apply --route=guide --name=ご利用ガイド --body=-

cat customize.css | bin/console eccube:asset:apply --type=css --body=-
cat logo.png | bin/console eccube:user-data:put --path=assets/img/logo.png --body=-
bin/console eccube:env:set ECCUBE_TEMPLATE_CODE=default
bin/console eccube:keystore:generate      # 未生成の鍵だけを作る（冪等）
```

ページ・ブロック・メールテンプレートの入力値の検証は管理画面と同じ FormType を通すため、
重複チェックや twig の構文チェックも同じものが効く。
`apply` / `remove` は build ディレクトリへ書き込めない場合、本処理を完了させたうえで終了コード `3` と
`eccube:cache:build` の案内を返す（`3` = 完了したが手動操作が必要。`eccube:plugin:*` / `eccube:cache:build` / `eccube:env:set` も同じ）。

`html/` はドキュメントルートのため、`eccube:user-data:put` は管理画面のファイル管理と同じ
ファイル名・拡張子の検証（`eccube_file_uploadable_extensions`）を通す。`.php` 等は配置できない。
`eccube:env:set` は書き込み後に `eccube:cache:build` を**別プロセスで**実行する
（同一プロセスでは起動時に読み込んだ古い `.env` が焼き込まれるため）。
`.env.local.php` があるなど変更が実行時に反映されない場合は、書き込んだうえで終了コード `3` を返す。

#### コンテンツを Git で管理する（`eccube:contents:*`）

**テンプレートはファイル、コンテンツ定義は DB** に分かれており、Git に残せるのは前者だけである。
`eccube:contents:export` / `import` は、この**残らない側（DB）**だけを yaml で入出力する。

```bash
bin/console eccube:contents:export                     # 既定は app/contents/ へ
bin/console eccube:contents:import --dry-run           # 差分だけ表示
bin/console eccube:contents:import                     # 反映
bin/console eccube:contents:import --prune --dry-run   # 削除対象の確認
```

```
app/contents/
  manifest.yaml   pages.yaml   blocks.yaml   mail_templates.yaml   layouts.yaml
```

- **テンプレートの本文は書き出さない。** `src/Eccube/Resource/template/**` を直接カスタマイズし
  `git merge` で upstream の修正（脆弱性パッチを含む）を取り込む運用が一般的で、本文をアーカイブへ
  複製すると二重管理になり merge で解決できなくなる。`app/template/{theme}` は twig の探索で
  `src/Eccube/Resource/template/default` より**優先される**ため、内容が同じ写しを置くと
  upstream のテンプレート修正が画面へ反映されなくなる
- 同じ理由で、`*ContentService::save()` は**本文が変わらない限りテンプレートを書き出さない**。
  管理画面でコアページのメタ情報だけを変更しても `app/template` に写しはできない。
  ただしテンプレートをどこからも解決できない場合は必ず書き出す（書き出さないと DB のレコードだけが
  残り、画面が「Unable to find template」で落ちる）
- 本文を指定しない新規登録は、配置先に既にあるテンプレートを使う。`app/template/user_data/foo.twig`
  をコミットして `pages.yaml` に 1 行足せば、`import` がそのページを作る
- レイアウトは `dtb_layout.id` が環境ごとに変わるため**名前で参照する**。名前が重複していると
  export がエラーになるので一意にする
- `--prune` はアーカイブに無いものを削除する（既定は無効）。削除できるのはユーザーが作成したページ、
  削除可能なブロック・メールテンプレート、どのページからも参照されていないレイアウトだけ
- `html/user_data` は `customize.css` / `customize.js` 以外が `.gitignore` で除外されているため
  既定では扱わない。リポジトリ丸ごと管理する構成では `--include=user_data` を指定する

### テスト

```bash
vendor/bin/phpunit                                                      # 全テスト
vendor/bin/phpunit tests/Eccube/Tests/Web/ShoppingControllerTest.php    # 単一ファイル
vendor/bin/phpunit --filter testCompleteWithLogin                       # フィルタ
```

E2E（Playwright、`e2e/` 配下で実行）:

```bash
cd e2e && npm ci
# project は front-tests / admin-tests / plugin-tests。spec ファイル名でフィルタ
npx playwright test --project=setup --project=front-tests front-product.spec.ts
```

### 静的解析

```bash
vendor/bin/phpstan analyse src   # phpstan.neon.dist の設定（level 6）で解析
```

### コードスタイル

```bash
vendor/bin/php-cs-fixer fix --dry-run --diff   # 違反チェック
vendor/bin/php-cs-fixer fix                     # 自動修正
```

### アセットビルド

```bash
npm ci
npm run build

# Docker 環境
docker compose -f docker-compose.yml -f docker-compose.dev.yml -f docker-compose.nodejs.yml run --rm -T nodejs npm ci
docker compose -f docker-compose.yml -f docker-compose.dev.yml -f docker-compose.nodejs.yml run --rm -T nodejs npm run build
```

### キャッシュ / データベース

キャッシュは 3 つのディレクトリに分かれる。`var/build/{env}`（コンパイル済みコンテナ・ルーティング・
メタデータ・prod の twig）と `var/cache/{env}`（翻訳カタログ・htmlpurifier）は CLI が生成し、
リクエスト処理中は読み取りのみ。`var/runtime/{env}`（cache pool・mcp-sessions・事前コンパイル漏れの
twig のフォールバック等）はリクエスト処理中に生成される。prod は `var/build/{env}/twig` を読み取り専用で
優先するため、**prod でテンプレートを変えたら `eccube:cache:build`**（dev は `auto_reload` で自動反映）。
権限を分離した環境での使い分けは上記「権限を分離した環境」と Skill `eccube-permission-lanes`。

```bash
bin/console eccube:cache:build   # var/build を再生成（テンプレートの事前コンパイルを含む）。デプロイ後・プラグイン操作後にも実行する
bin/console cache:pool:clear --all   # 実行時キャッシュ（cache pool）を削除
bin/console cache:clear          # 従来どおり全体を削除（build と cache の双方に書き込み権限が必要。--no-warmup は付けない）

# スキーマは Entity 属性が源泉。アップデートは 2 段構え:
bin/console doctrine:schema:update --dump-sql        # 属性差分の SQL プレビュー
bin/console doctrine:schema:update --force           # 属性から導けるカラム追加・変更を反映
bin/console doctrine:migrations:migrate              # INSERT・型変更等（schema:update で扱えない分）を適用
# マイグレーションが必要なときは空の雛形を生成して手書きする（diff は使わない）:
bin/console doctrine:migrations:generate
```

> 単純なカラム追加にマイグレーション（ALTER）は不要。詳細は Skill `eccube-migration`（[`.claude/skills/eccube-migration/SKILL.md`](./.claude/skills/eccube-migration/SKILL.md)）。

## アーキテクチャ

### PurchaseFlow（受注処理パイプライン）

`src/Eccube/Service/PurchaseFlow/` にあるコアの受注処理エンジン。以下のパイプラインで注文を処理します。

1. **ItemPreprocessor / ItemHolderPreprocessor**: 明細の準備（送料・手数料の計算）
2. **ItemValidator / ItemHolderValidator**: 明細の検証（在庫・販売制限・合計金額）
3. **ItemHolderPostValidator**: 全処理後の最終検証
4. **PurchaseProcessor**: 購入実行（在庫引当・ポイント付与・注文番号採番）
5. **DiscountProcessor**: 値引き適用

設定は `app/config/eccube/packages/purchaseflow.yaml`。

### イベントシステム

EC-CUBE は Symfony の EventDispatcher を拡張してカスタマイズを実現します。

- **テンプレートイベント**: 特定のテンプレート位置にコンテンツを差し込む（`Event/EccubeEvents.php`）
- **コントローライベント**: ライフサイクル中のリクエスト/レスポンスを変更
- **エンティティイベント**: Doctrine ライフサイクルコールバック

### プラグインシステム

プラグインは `app/Plugin/{PluginCode}/` に自己完結したパッケージとして配置されます。

- `PluginManager.php` で install/uninstall/enable/disable のライフサイクルを処理
- エンティティ・コントローラ・フォーム・テンプレート・イベントサブスクライバを追加可能
- メタデータはプラグイン内の `composer.json` で定義

### app/Customize によるカスタマイズ

プロジェクト固有のコードはコア改変ではなく `app/Customize/` に置き、コアアップグレードの影響を避けます。

- **エンティティ拡張**: Doctrine トレイトで既存エンティティにフィールド追加
- **フォーム拡張**: Symfony FormTypeExtension で既存フォームにフィールド追加
- **テンプレート上書き**: `app/template/` に置いてコアテンプレートを上書き
- **サービス上書き**: サービスデコレーション or コンパイラパス

### エンティティプロキシ

`app/proxy/entity/` のプロキシ機構を使用します。プラグインやカスタマイズがトレイトを追加すると、
プロキシジェネレータが拡張エンティティを生成します。再生成は `bin/console eccube:generate:proxies`。

## コーディング規約（共通）

- PSR-12 に従う（PHP-CS-Fixer で強制）。PHP ファイル先頭の EC-CUBE ライセンスヘッダは必須。
- 引数・戻り値に PHP 型宣言を付ける（PHPStan level 6 を通す）。
- **エンティティは PHP8 属性 `#[ORM\...]` でマッピング**（XML マッピングは使用しない）。
- コントローラは `Eccube\Controller\AbstractController` を継承。
- FormType は `Symfony\Component\Form\AbstractType` を継承。
- リポジトリは `Eccube\Repository\AbstractRepository` を継承。
- **ルーティングは `#[Route]` 属性**（`Symfony\Component\Routing\Attribute\Route`）。
- テンプレートは `.twig`。管理画面は `Resource/template/admin/`、店頭は `Resource/template/default/`。

## コーディング規約（レイヤ別・オンデマンド）

レイヤ別の詳細規約は各 **Skill**（`.claude/skills/<name>/SKILL.md`）が本文を直接持ちます。
frontmatter の `description` がトリガ条件で、該当レイヤを触るときだけ発火・参照されます（常時読み込まない）。
本文は純 Markdown なので GitHub でもそのまま読めます。

| レイヤ / 観点 | 規約 Skill（本文） | Skill 名 |
|--------|------------------|----------|
| PHPUnit テスト | [`.claude/skills/eccube-phpunit/SKILL.md`](./.claude/skills/eccube-phpunit/SKILL.md) | `eccube-phpunit` |
| E2E（Playwright・spec 作成 / flaky 対策） | [`.claude/skills/eccube-e2e/SKILL.md`](./.claude/skills/eccube-e2e/SKILL.md) | `eccube-e2e` |
| コントローラ（責務分離・Fat化防止） | [`.claude/skills/eccube-controller/SKILL.md`](./.claude/skills/eccube-controller/SKILL.md) | `eccube-controller` |
| サービス（責務分離・単一責任） | [`.claude/skills/eccube-service/SKILL.md`](./.claude/skills/eccube-service/SKILL.md) | `eccube-service` |
| マイグレーション（スキーマ変更） | [`.claude/skills/eccube-migration/SKILL.md`](./.claude/skills/eccube-migration/SKILL.md) | `eccube-migration` |
| Entity（Doctrine エンティティ） | [`.claude/skills/eccube-entity/SKILL.md`](./.claude/skills/eccube-entity/SKILL.md) | `eccube-entity` |
| Repository（データアクセス） | [`.claude/skills/eccube-repository/SKILL.md`](./.claude/skills/eccube-repository/SKILL.md) | `eccube-repository` |
| FormType（フォーム） | [`.claude/skills/eccube-formtype/SKILL.md`](./.claude/skills/eccube-formtype/SKILL.md) | `eccube-formtype` |
| セキュリティ（認証・認可・CSRF・IDOR） | [`.claude/skills/eccube-security/SKILL.md`](./.claude/skills/eccube-security/SKILL.md) | `eccube-security` |
| Twig 拡張・テンプレート（XSS・上書き） | [`.claude/skills/eccube-twig-template/SKILL.md`](./.claude/skills/eccube-twig-template/SKILL.md) | `eccube-twig-template` |
| イベント（Subscriber・テンプレート/Doctrine イベント） | [`.claude/skills/eccube-event-subscriber/SKILL.md`](./.claude/skills/eccube-event-subscriber/SKILL.md) | `eccube-event-subscriber` |
| プラグイン（ライフサイクル・配置・拡張） | [`.claude/skills/eccube-plugin/SKILL.md`](./.claude/skills/eccube-plugin/SKILL.md) | `eccube-plugin` |
| 受注処理（PurchaseFlow の Processor/Validator） | [`.claude/skills/eccube-purchase-flow/SKILL.md`](./.claude/skills/eccube-purchase-flow/SKILL.md) | `eccube-purchase-flow` |
| メール（MailService・テンプレート・MailHistory） | [`.claude/skills/eccube-mail/SKILL.md`](./.claude/skills/eccube-mail/SKILL.md) | `eccube-mail` |
| カスタマイズ（app/Customize での拡張・上書き・デコレーション） | [`.claude/skills/eccube-customize/SKILL.md`](./.claude/skills/eccube-customize/SKILL.md) | `eccube-customize` |
| CSV 入出力（CsvImport/Export・CSV 定義） | [`.claude/skills/eccube-csv/SKILL.md`](./.claude/skills/eccube-csv/SKILL.md) | `eccube-csv` |
| コンソールコマンド（Symfony Console・バッチ） | [`.claude/skills/eccube-command/SKILL.md`](./.claude/skills/eccube-command/SKILL.md) | `eccube-command` |
| 権限レーン（Web サーバー / CLI の書き込み分離・`eccube:doctor:permissions`・実行ユーザー） | [`.claude/skills/eccube-permission-lanes/SKILL.md`](./.claude/skills/eccube-permission-lanes/SKILL.md) | `eccube-permission-lanes` |
| 責務分離レビュー（実装直後の自己チェック・全層） | [`.claude/skills/eccube-review-responsibility/SKILL.md`](./.claude/skills/eccube-review-responsibility/SKILL.md) | `eccube-review-responsibility` |

> 規約は必要になった時点で `.claude/skills/eccube-<name>/SKILL.md` を 1 ファイル追加して足す（`.codex`/`.agents` は symlink で自動共有）。
> 各ファイルは frontmatter（`name` / `description`）＋本文の順で書き、本文は「対象／基本ルール／実装パターン／よくある間違い／実行・確認方法」の構成を推奨する（推測を載せず、必ず `src/Eccube/` の実コードで裏取りする）。

**Skill 命名規則**: **`eccube-` 接頭辞を必ず付ける**（`eccube-controller` / `eccube-service` / `eccube-phpunit`）。
接頭辞の後ろは、自動発火するレイヤ規約系はトピック名、
人が明示的に実行するアクション系は動詞前置（`eccube-review-responsibility`）とする。

接頭辞を付ける理由は、**AI ツールの組み込みスラッシュコマンド・組み込み Skill との名前衝突を避ける**ため。
`plugin` は Claude Code 組み込みの `/plugin`（プラグイン管理 UI）と完全一致し、
本リポジトリを開いている間は組み込みコマンドへ到達できなくなっていた（#6978）。
`entity` `mail` `service` `command` のような汎用語は将来同じ問題を起こすため、
1 件ずつ例外対応せず全 Skill を `eccube-` 名前空間に入れる。接頭辞だけで衝突回避と
ピッカーでの一括絞り込みは足りるので、`-dev` のような接尾辞は付けない。

**「よくある間違い」を書き足すときの歯止め**: 検証やレビューで得た知見を追記していくと、
このセクションは放置すると際限なく伸び、個別事例が一般則の顔で並ぶ。次の 3 点を守る。

- **一般化テスト**: 固有のメソッド名・列名・テーブル名を消しても項目が成立するか確認する。
  成立しないものは Skill に書かない（そのレイヤ全体に効く規約ではなく、特定の調査結果である）。
  成立するなら例示を削って一般則だけ残す。固有名を残すと、無関係な箇所へ誤って適用される。
- **上限**: 1 Skill あたり 10 項・1 項 120 字程度に収める。超えたら**追記ではなく既存項への統合か削除**を選ぶ。
- **頻度順**: 踏まれやすいものを上に置く。読み手の注意は前方に効くため、頻度順でないリストは下位が実質死ぬ。

この歯止めは**追記するときに適用する**。本規則の導入時点で超過していた Skill
（項数 2 件・字数 8 件）は統合・短縮済みで、現在はすべて 10 項以内に収まっている。
超過の有無は次で確認できる。

```bash
for f in .claude/skills/eccube-*/SKILL.md; do
  sed -n '/よくある間違い/,/^## /p' "$f" | grep -E '^- ' \
    | awk -v s="$(basename "$(dirname "$f")")" '{n++; if (length>m) m=length} END {if (n) printf "%-28s 項数=%-3s 最長=%s\n", s, n, m}'
done
```

## 主要エンティティ

- `Customer` — 会員
- `Product` / `ProductClass` — 商品とその規格（サイズ・カラー）。在庫・価格は `ProductClass` 単位で持つ。
- `Order` / `OrderItem` — 受注と明細
- `Shipping` — 出荷単位。**1 受注に複数あり得る**（複数配送先・お届け日違い等）。
- `Cart` / `CartItem` — カート
- `Member` — 管理者ユーザー
- `Plugin` — インストール済みプラグインのメタデータ
- `BaseInfo` — 店舗設定（店名・住所・税設定）

### ドメイン用語（ツールでは読み取れない知識）

- **販売種別（SaleType）** — 注文を**決済単位に分割するための区分**。販売種別が異なる商品は別の `Order` として扱われる
  （通常購入と定期購入を分ける、等）。`mtb_sale_type` で定義。
- **OrderItem の明細種別（OrderItemType）** — 明細行の種類: **商品 / 送料 / 手数料 / 値引き** 等。
  受注金額はこれらの明細の合算で構成される。送料・手数料・値引きも `OrderItem` の 1 行として表現される点に注意。
- **Shipping（出荷）** — 1 受注に複数あり得る出荷単位。送料は Shipping 単位で計算される。
- **受注処理（PurchaseFlow）** — 在庫引当・採番・ポイント付与・値引きは
  `src/Eccube/Service/PurchaseFlow/` のパイプラインが担う（Skill `eccube-service` 参照）。
- **受注ステータス（OrderStatus, `mtb_order_status`）** — `NEW`(新規受付/確定) / `PROCESSING`(購入処理中) /
  `PENDING`(決済処理中) / `PAID`(入金済) / `DELIVERED`(発送済) / `CANCEL`(取消) / `RETURNED`(返品) / `IN_PROGRESS`(対応中)。
  **注意: `PROCESSING`・`PENDING` は「確定前の仮受注」**。カート確定の入口で `OrderHelper` が受注を `PROCESSING` で作り、
  購入完了で `NEW` に遷移する。売上集計や受注一覧はこれらを除外する（`OrderStatusFilter`）。
  「`Order` が存在する＝確定済み注文」と誤解しないこと。
- **ProductClass と「規格なし商品」** — 在庫・価格は `Product` ではなく `ProductClass` 単位で持つ。
  **規格（サイズ・カラー）を持たない商品も、内部的に `ProductClass` を 1 つ持つ**（`Product::hasProductClass()` で規格の有無を判定）。
- **単一テーブル継承（STI）と `discriminator_type`** — マスタ系（`mtb_*`）や `dtb_block` 等は STI を使い、
  `discriminator_type` 列で型を区別する。**INSERT 時はこの列の指定が必須**（例: `mtb_sale_type` は `'saletype'`、`dtb_block` は `'block'`）。
- **Payment（支払方法） / Delivery（配送業者）** — 受注に紐づく基本マスタ。利用可能な組み合わせは販売種別（SaleType）に依存する。
- **バージョン体系** — `4` = 製品ライン（「EC-CUBE 4」）／ **`4.x`（例 4.3→4.4）= メジャー更新**（Symfony のメジャー更新・PHP 要件引き上げ等の**破壊的変更を伴う**。例: 4.3=Symfony 6.4 → 4.4=Symfony 7.4）／ `4.x.y`（例 4.3.1）= マイナー ／ `-pN`（例 4.3.1-p1）= パッチ（脆弱性・セキュリティ）。
  **含意**: `4.3→4.4` は「マイナー」ではなく**メジャー**。`@deprecated` な public API / interface / Entity ゲッタの撤去は、こうしたメジャーの節目でなら実施可能な破壊的変更であり、「次の 5.0 まで技術的に不可」ではない。4.0.3 以来の `@deprecated` が複数メジャーを跨いで残っているのは技術制約ではなく**互換維持ポリシー・優先度の判断**。負債やレビューで「BC ロック＝修正不可」と機械的に断じないこと。

## Skill の配置と各ツールの読み込み

Skill（`SKILL.md`）の形式は Claude Code / Cursor / Codex / Antigravity で共通です。
ただしツールごとに読み込むディレクトリが異なるため、正本 `.claude/skills/` を各ツールのディレクトリへ symlink で共有しています。

| ツール | 読み込むディレクトリ |
|--------|----------------------|
| Claude Code | `.claude/skills/`（正本） |
| Cursor | `.cursor/skills/` ＋ 互換で `.claude/skills/` `.codex/skills/` も読む。root `AGENTS.md` も自動ロード |
| Codex CLI | `.codex/skills/`（→ `.claude/skills` への symlink）＋ root `AGENTS.md` |
| Google Antigravity | `.agents/skills/`（→ `.claude/skills` への symlink）＋ `AGENTS.md` |
| Gemini CLI | Skill 非対応。`GEMINI.md` 経由で本ファイルと `.claude/skills/*/SKILL.md` を参照 |

各 Skill の本文は `.claude/skills/<name>/SKILL.md` が直接保持します（薄いスタブと本文を分ける二層は廃止）。
`.codex/skills` `.agents/skills` は `.claude/skills` への **symlink**（`../.claude/skills`）で、正本は常に 1 つ。
コピー同期スクリプトや同期 CI は不要です（Windows で symlink を扱うには git の symlink サポート＝`core.symlinks=true` や Developer Mode が前提）。

## 基本原則

- **推測実装の禁止**: 「〜のはず」で書かず、必ず既存コード・ドキュメントで裏取りする。
- **規約準拠**: 該当レイヤの Skill（`.claude/skills/`）に従う。
- **静的解析**: 実装後は `vendor/bin/phpstan analyse src`（level 6）を通す。
- **コードスタイル**: `vendor/bin/php-cs-fixer fix` で PSR-12 に整える。ライセンスヘッダ必須。
- **app/Customize 優先**: プロジェクト固有のカスタマイズはコア改変ではなく `app/Customize/` で行う。
