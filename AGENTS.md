# AGENTS.md

このファイルは、AI コーディングエージェント（Claude Code / Cursor / Codex CLI / Google Antigravity / Gemini CLI 等）が
EC-CUBE 4.4 を扱う際に従う共通の入口であり、**ベンダー中立な単一の正典（single source of truth）**です。

**参照方向は一方向のみ**（循環参照を避けるため）:

- `CLAUDE.md` / `GEMINI.md` は、このファイルを参照する薄いポインタです（下流）。
- このファイルは `CLAUDE.md` / `GEMINI.md` を参照し返しません（上流）。
- レイヤ別規約 `docs/rules/*.md` は末端で、上流を参照し返しません。

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
- **テスト**: PHPUnit 11（`symfony/phpunit-bridge` 経由）/ Codeception 5（E2E）
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
bin/console doctrine:schema:update --dump-sql   # SQL のプレビュー
bin/console doctrine:migrations:diff            # マイグレーション生成
bin/console doctrine:migrations:migrate         # マイグレーション実行
```

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

レイヤ別の詳細規約は **常時読み込まず**、必要なときだけ参照します。本文は `docs/rules/` に
ベンダー中立な Markdown として 1 ソースで管理し、各 AI ツールには「Skill」として発火スタブを置いています。

| レイヤ / 観点 | 規約ドキュメント | Skill 名 |
|--------|------------------|----------|
| PHPUnit テスト | [`docs/rules/phpunit.md`](./docs/rules/phpunit.md) | `phpunit` |
| コントローラ（責務分離・Fat化防止） | [`docs/rules/controller.md`](./docs/rules/controller.md) | `controller` |
| サービス（責務分離・単一責任） | [`docs/rules/service.md`](./docs/rules/service.md) | `service` |
| マイグレーション（スキーマ変更） | [`docs/rules/migration.md`](./docs/rules/migration.md) | `migration` |
| Entity（Doctrine エンティティ） | [`docs/rules/entity.md`](./docs/rules/entity.md) | `entity` |
| Repository（データアクセス） | [`docs/rules/repository.md`](./docs/rules/repository.md) | `repository` |
| FormType（フォーム） | [`docs/rules/formtype.md`](./docs/rules/formtype.md) | `formtype` |
| 責務分離レビュー（実装直後の自己チェック・全層） | [`docs/rules/controller.md`](./docs/rules/controller.md) ／ [`service.md`](./docs/rules/service.md) | `review-responsibility` |

> 規約は必要になった時点で同じ構成（`docs/rules/*.md` ＋ Skill スタブ）で追加する。
> 設計方針・Skill 命名規則は [`docs/rules/README.md`](./docs/rules/README.md) を参照してください。

**Skill 命名規則**: リポジトリ内のスキルのため `eccube-` 接頭辞は付けない。
自動発火するレイヤ規約系はトピック名（`controller` / `service` / `phpunit`）、
人が明示的に実行するアクション系は動詞前置（`review-responsibility`）とする。

## 主要エンティティ

- `Customer` — 会員
- `Product` / `ProductClass` — 商品とその規格（サイズ・カラー）
- `Order` / `OrderItem` — 受注と明細
- `Shipping` — 出荷情報（1 受注に複数可）
- `Cart` / `CartItem` — カート
- `Member` — 管理者ユーザー
- `Plugin` — インストール済みプラグインのメタデータ
- `BaseInfo` — 店舗設定（店名・住所・税設定）

## Skill の配置と各ツールの読み込み

Skill（`SKILL.md`）の形式は Claude Code / Cursor / Codex / Antigravity で共通です。
ただしツールごとに読み込むディレクトリが異なるため、同じスタブを複数の場所に配置しています。

| ツール | 読み込むディレクトリ |
|--------|----------------------|
| Claude Code | `.claude/skills/` |
| Cursor | `.cursor/skills/` ＋ 互換で `.claude/skills/` `.codex/skills/` も読む。root `AGENTS.md` も自動ロード |
| Codex CLI | `.codex/skills/` ＋ root `AGENTS.md` |
| Google Antigravity | `.agents/skills/` ＋ `AGENTS.md` |
| Gemini CLI | Skill 非対応。`GEMINI.md` 経由で本ファイルと `docs/rules/` を参照 |

各 Skill の本文は薄いスタブで、実体である `docs/rules/*.md` を読み込んで適用します。
スタブは `.claude/skills/` を正本とし、`php tools/sync-ai-skills.php` で `.codex/` `.agents/` へ同期します
（symlink は Windows で壊れやすいため不使用）。

## 基本原則

- **推測実装の禁止**: 「〜のはず」で書かず、必ず既存コード・ドキュメントで裏取りする。
- **規約準拠**: 該当レイヤの規約（`docs/rules/`）に従う。
- **静的解析**: 実装後は `vendor/bin/phpstan analyse src`（level 6）を通す。
- **コードスタイル**: `vendor/bin/php-cs-fixer fix` で PSR-12 に整える。ライセンスヘッダ必須。
- **app/Customize 優先**: プロジェクト固有のカスタマイズはコア改変ではなく `app/Customize/` で行う。
