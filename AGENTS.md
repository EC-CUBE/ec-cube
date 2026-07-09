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
- **フロントエンド**: Sass (SCSS) / webpack / Bootstrap 5.3 / jQuery 4.x
- **テスト**: PHPUnit 11（`symfony/phpunit-bridge` 経由）/ Playwright（E2E、`e2e/`）
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

### テスト

```bash
bin/phpunit                                                      # 全テスト
bin/phpunit tests/Eccube/Tests/Web/ShoppingControllerTest.php    # 単一ファイル
bin/phpunit --filter testCompleteWithLogin                       # フィルタ
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

```bash
bin/console cache:clear
bin/console cache:warmup

# スキーマは Entity 属性が源泉。アップデートは 2 段構え:
bin/console doctrine:schema:update --dump-sql        # 属性差分の SQL プレビュー
bin/console doctrine:schema:update --force           # 属性から導けるカラム追加・変更を反映
bin/console doctrine:migrations:migrate              # INSERT・型変更等（schema:update で扱えない分）を適用
# マイグレーションが必要なときは空の雛形を生成して手書きする（diff は使わない）:
bin/console doctrine:migrations:generate
```

> 単純なカラム追加にマイグレーション（ALTER）は不要。詳細は Skill `migration`（[`.claude/skills/migration/SKILL.md`](./.claude/skills/migration/SKILL.md)）。

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
| PHPUnit テスト | [`.claude/skills/phpunit/SKILL.md`](./.claude/skills/phpunit/SKILL.md) | `phpunit` |
| E2E（Playwright・spec 作成 / flaky 対策） | [`.claude/skills/e2e/SKILL.md`](./.claude/skills/e2e/SKILL.md) | `e2e` |
| コントローラ（責務分離・Fat化防止） | [`.claude/skills/controller/SKILL.md`](./.claude/skills/controller/SKILL.md) | `controller` |
| サービス（責務分離・単一責任） | [`.claude/skills/service/SKILL.md`](./.claude/skills/service/SKILL.md) | `service` |
| マイグレーション（スキーマ変更） | [`.claude/skills/migration/SKILL.md`](./.claude/skills/migration/SKILL.md) | `migration` |
| Entity（Doctrine エンティティ） | [`.claude/skills/entity/SKILL.md`](./.claude/skills/entity/SKILL.md) | `entity` |
| Repository（データアクセス） | [`.claude/skills/repository/SKILL.md`](./.claude/skills/repository/SKILL.md) | `repository` |
| FormType（フォーム） | [`.claude/skills/formtype/SKILL.md`](./.claude/skills/formtype/SKILL.md) | `formtype` |
| セキュリティ（認証・認可・CSRF・IDOR） | [`.claude/skills/security/SKILL.md`](./.claude/skills/security/SKILL.md) | `security` |
| Twig 拡張・テンプレート（XSS・上書き） | [`.claude/skills/twig-template/SKILL.md`](./.claude/skills/twig-template/SKILL.md) | `twig-template` |
| イベント（Subscriber・テンプレート/Doctrine イベント） | [`.claude/skills/event-subscriber/SKILL.md`](./.claude/skills/event-subscriber/SKILL.md) | `event-subscriber` |
| プラグイン（ライフサイクル・配置・拡張） | [`.claude/skills/plugin/SKILL.md`](./.claude/skills/plugin/SKILL.md) | `plugin` |
| 受注処理（PurchaseFlow の Processor/Validator） | [`.claude/skills/purchase-flow/SKILL.md`](./.claude/skills/purchase-flow/SKILL.md) | `purchase-flow` |
| メール（MailService・テンプレート・MailHistory） | [`.claude/skills/mail/SKILL.md`](./.claude/skills/mail/SKILL.md) | `mail` |
| カスタマイズ（app/Customize での拡張・上書き・デコレーション） | [`.claude/skills/customize/SKILL.md`](./.claude/skills/customize/SKILL.md) | `customize` |
| CSV 入出力（CsvImport/Export・CSV 定義） | [`.claude/skills/csv/SKILL.md`](./.claude/skills/csv/SKILL.md) | `csv` |
| コンソールコマンド（Symfony Console・バッチ） | [`.claude/skills/command/SKILL.md`](./.claude/skills/command/SKILL.md) | `command` |
| 責務分離レビュー（実装直後の自己チェック・全層） | [`.claude/skills/review-responsibility/SKILL.md`](./.claude/skills/review-responsibility/SKILL.md) | `review-responsibility` |

> 規約は必要になった時点で `.claude/skills/<name>/SKILL.md` を 1 ファイル追加して足す（`.codex`/`.agents` は symlink で自動共有）。
> 各ファイルは frontmatter（`name` / `description`）＋本文の順で書き、本文は「対象／基本ルール／実装パターン／よくある間違い／実行・確認方法」の構成を推奨する（推測を載せず、必ず `src/Eccube/` の実コードで裏取りする）。

**Skill 命名規則**: リポジトリ内のスキルのため `eccube-` 接頭辞は付けない。
自動発火するレイヤ規約系はトピック名（`controller` / `service` / `phpunit`）、
人が明示的に実行するアクション系は動詞前置（`review-responsibility`）とする。

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
  `src/Eccube/Service/PurchaseFlow/` のパイプラインが担う（Skill `service` 参照）。
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
