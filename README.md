# EC-CUBE 4.4

[![Unit test for EC-CUBE](https://github.com/EC-CUBE/ec-cube/actions/workflows/unit-test.yml/badge.svg?branch=4.4)](https://github.com/EC-CUBE/ec-cube/actions/workflows/unit-test.yml)
[![E2E test for EC-CUBE](https://github.com/EC-CUBE/ec-cube/actions/workflows/e2e-test.yml/badge.svg?branch=4.4)](https://github.com/EC-CUBE/ec-cube/actions/workflows/e2e-test.yml)
[![Plugin test for EC-CUBE](https://github.com/EC-CUBE/ec-cube/actions/workflows/plugin-test.yml/badge.svg?branch=4.4)](https://github.com/EC-CUBE/ec-cube/actions/workflows/plugin-test.yml)
[![PHPStan](https://github.com/EC-CUBE/ec-cube/actions/workflows/phpstan.yml/badge.svg?branch=4.4)](https://github.com/EC-CUBE/ec-cube/actions/workflows/phpstan.yml)
[![codecov](https://codecov.io/gh/EC-CUBE/ec-cube/branch/4.4/graph/badge.svg?token=BhnPjjvfwd)](https://codecov.io/gh/EC-CUBE/ec-cube)

[![Slack](https://img.shields.io/badge/slack-join%5fchat-brightgreen.svg?style=flat)](https://join.slack.com/t/ec-cube/shared_invite/enQtNDA1MDYzNDQxMTIzLTY5MTRhOGQ2MmZhMjQxYTAwMmVlMDc5MDU2NjJlZmFiM2E3M2Q0M2Y3OTRlMGY4NTQzN2JiZDBkNmQwNTUzYzc)

**4.4のリリース内容・計画は[ロードマップ](https://github.com/EC-CUBE/ec-cube/issues/6762)をご確認ください。**

+ 本ドキュメントはEC-CUBEの開発者を主要な対象者としております。
+ パッケージ版は[EC-CUBEオフィシャルサイト](https://www.ec-cube.net)で配布しています。
+ カスタマイズやEC-CUBEの利用、仕様に関しては[開発コミュニティ](https://xoops.ec-cube.net)をご利用ください。
+ 本体開発にあたって不明点などあれば[Issue](https://github.com/EC-CUBE/ec-cube/wiki/Issues%E3%81%AE%E5%88%A9%E7%94%A8%E6%96%B9%E6%B3%95)をご利用下さい。
+ EC-CUBE 3系の保守については、 [EC-CUBE/ec-cube3](https://github.com/EC-CUBE/ec-cube3/)にて開発を行っております。
+ EC-CUBE 2系の保守については、 [EC-CUBE/ec-cube2](https://github.com/EC-CUBE/ec-cube2/)にて開発を行っております。

## インストール

### EC-CUBE 4.4のインストール方法

開発ドキュメントの [インストール方法](https://doc4.ec-cube.net/quickstart/install) の手順に従ってインストールしてください。

### Docker 環境へのアクセス

`docker compose ... up -d` で起動後、以下の URL でアクセスします。

- フロント: `https://127.0.0.1:4430/`
- 管理画面: `https://127.0.0.1:4430/admin/`（初期ログインは `admin` / `password`）
- MailCatcher（送信メールの確認）: `http://localhost:1080/`

> **HTTP（`http://localhost:8080`）ではログインできません。**
> セッションクッキーが `SameSite=None` で発行されるため、ブラウザの仕様上 HTTPS 接続でないとクッキーが破棄され、ログイン・カートなどセッションを使う操作が成立しません（設定: `app/config/eccube/packages/framework.yaml` の `cookie_secure: auto` / `cookie_samesite: none`）。開発時は HTTPS（4430 番ポート）を使用してください。

#### 「保護されていない通信」の警告を消す（任意）

HTTPS でアクセスすると、ブラウザに「保護されていない通信」の警告が表示されます。これは Docker イメージが Apache の自己署名証明書（snakeoil）を使っているためで、動作上の問題はありません。そのまま「詳細設定」→「127.0.0.1 にアクセスする（安全ではありません）」で進めば利用できます。

警告自体を消したい場合は、[mkcert](https://github.com/FiloSottile/mkcert) でローカル信頼の証明書を発行し、コンテナの証明書と差し替えます。

```shell
# 1. ローカル CA を OS に登録し、127.0.0.1 用の証明書を発行する
mkcert -install
mkcert 127.0.0.1 localhost
# → 127.0.0.1+1.pem（証明書）と 127.0.0.1+1-key.pem（秘密鍵）が生成される
```

`docker-compose.dev.yml` の `ec-cube` サービスに、生成した証明書をコンテナの snakeoil 証明書へ上書きマウントする設定を追記します。

```yaml
services:
  ec-cube:
    volumes:
      - ".:/var/www/html:cached"
      - "./127.0.0.1+1.pem:/etc/ssl/certs/ssl-cert-snakeoil.pem"
      - "./127.0.0.1+1-key.pem:/etc/ssl/private/ssl-cert-snakeoil.key"
```

```shell
# 2. コンテナを再作成して反映する
docker compose -f docker-compose.yml -f docker-compose.dev.yml -f docker-compose.pgsql.yml up -d
```

> 生成した `*.pem` / `*-key.pem`（特に秘密鍵）はリポジトリにコミットしないでください（`.gitignore` に追加するなどしてください）。

> **WSL2 + Windows のブラウザを使う場合**: WSL 内で `mkcert -install` を実行しても、Windows 側ブラウザ（Chrome / Edge は Windows の証明書ストアを参照）には反映されません。`mkcert -CAROOT` にある `rootCA.pem` を Windows にコピーし、Windows 側で「信頼されたルート証明機関」に取り込んでください（例: PowerShell で `Import-Certificate -FilePath <path> -CertStoreLocation Cert:\CurrentUser\Root`）。取り込み後はブラウザを完全に再起動します。

### CSS の編集・ビルド方法

[Sass](https://sass-lang.com) を使用して記述されています。
Sass のソースコードは `html/template/{admin,default}/assets/scss` にあります。
前提として [https://nodejs.org/ja/] より、 Node.js をインストールしておいてください。

以下のコマンドでビルドすることで、 `html/template/**/assets/css` に CSS ファイルが出力されます。

```shell
npm ci # 初回およびpackage-lock.jsonに変更があったとき
npm run build # Sass のビルド
```

[`docker compose` を使用している場合](https://doc4.ec-cube.net/quickstart/docker_compose_install)は以下のコマンドを実行してください

``` shell
# 初回およびpackage-lock.jsonに変更があったとき
docker compose -f docker-compose.yml -f docker-compose.dev.yml -f docker-compose.nodejs.yml run --rm -T nodejs npm ci
# Sass のビルド
docker compose -f docker-compose.yml -f docker-compose.dev.yml -f docker-compose.nodejs.yml run --rm -T nodejs npm run build
```

### JavaScript のビルド方法

フロントエンドで使用する JavaScript のライブラリは npm で管理されています。
JavaScript のライブラリは webpack でバンドル/minifyされます。
バンドルするライブラリを変更する場合は、テンプレートごとに以下の bundle.js を修正し、リビルドしてください。
- [html/template/admin/assets/js/bundle.js](html/template/admin/assets/js/bundle.js)
- [html/template/default/assets/js/bundle.js](html/template/default/assets/js/bundle.js)
- [html/template/install/assets/js/bundle.js](html/template/default/install/js/bundle.js)

```shell
npm ci # 初回およびpackage-lock.jsonに変更があったとき
npm run build # Sass 及び JavaScript のビルド
```

JavaScript ライブラリのみをビルドしたい場合は以下でも可能です。

```shell
npx webpack
```

[`docker compose` を使用している場合](https://doc4.ec-cube.net/quickstart/docker_compose_install)は以下のコマンドを実行してください

``` shell
# 初回およびpackage-lock.jsonに変更があったとき
docker compose -f docker-compose.yml -f docker-compose.dev.yml -f docker-compose.nodejs.yml run --rm -T nodejs npm ci
# Sass のビルド
docker compose -f docker-compose.yml -f docker-compose.dev.yml -f docker-compose.nodejs.yml run --rm -T nodejs npm run build
# JavaScript ライブラリのみのビルド
docker compose -f docker-compose.yml -f docker-compose.dev.yml -f docker-compose.nodejs.yml run --rm -T nodejs npx webpack
```


### 動作確認環境

* Apache 2.4.x (mod_rewrite / mod_ssl 必須)
* PHP 8.2.x / 8.3.x / 8.4.x / 8.5.x
* PostgreSQL 13.x 〜 18.x / MySQL 8.4.x (LTS)
* ブラウザー：Google Chrome

詳しくは開発ドキュメントの [システム要件](https://doc4.ec-cube.net/quickstart/requirement) をご確認ください。

## ドキュメント

### [EC-CUBE 4.x 開発ドキュメント@doc4.ec-cube.net](https://doc4.ec-cube.net/)


EC-CUBE 4.x 系の仕様や手順、開発Tipsに関するドキュメントを掲載しています。
修正や追記、新規ドキュメントの作成をいただく場合、以下のレポジトリからPullRequestをお送りください。
[https://github.com/EC-CUBE/doc4.ec-cube.net](https://github.com/EC-CUBE/doc4.ec-cube.net)

## 開発への参加

EC-CUBE 4.4の不具合の修正、機能のブラッシュアップを目的として、継続的に開発を行っております。  
コードのリファクタリング、不具合修正以外のPullRequestを送る際は、Pull Requestのコメントなどに意図を明確に記載してください。  

開発環境の構築・ブランチ運用（PR の宛先は `4.4`）・PR を出す前に通すべき CI チェック（コードスタイル / 静的解析 / Rector / テスト）・ライセンスヘッダなど、**コントリビューションの具体的な手順は [CONTRIBUTING.md](.github/CONTRIBUTING.md) を参照**してください。レイヤ別の実装規約は [AGENTS.md](AGENTS.md) に集約しています。

Pull Requestの送信前に、Issueにて提議いただく事も可能です。
Issuesの利用方法については、[こちら](https://github.com/EC-CUBE/ec-cube/wiki/Issues%E3%81%AE%E5%88%A9%E7%94%A8%E6%96%B9%E6%B3%95)をご確認ください。

[Slack](https://join.slack.com/t/ec-cube/shared_invite/enQtNDA1MDYzNDQxMTIzLTY5MTRhOGQ2MmZhMjQxYTAwMmVlMDc5MDU2NjJlZmFiM2E3M2Q0M2Y3OTRlMGY4NTQzN2JiZDBkNmQwNTUzYzc)でも本体の開発に関する意見交換などを行っております。



### コピーライトポリシーへの同意

コードの提供・追加、修正・変更その他「EC-CUBE」への開発の御協力（Issue投稿、Pull Request投稿など、GitHub上での活動）を行っていただく場合には、
[EC-CUBEのコピーライトポリシー](https://github.com/EC-CUBE/ec-cube/wiki/EC-CUBE%E3%81%AE%E3%82%B3%E3%83%94%E3%83%BC%E3%83%A9%E3%82%A4%E3%83%88%E3%83%9D%E3%83%AA%E3%82%B7%E3%83%BC)をご理解いただき、ご了承いただく必要がございます。
Issueの投稿やPull Requestを送信する際は、EC-CUBEのコピーライトポリシーに同意したものとみなします。

## English

### What is EC-CUBE?

EC-CUBE is Japan's leading open-source e-commerce platform, powering over 35,000 online stores. It provides a full-featured, highly customizable solution specifically designed for the Japanese market.

Key differentiators:

- **Japanese Tax System**: Native support for Japan's reduced tax rate (軽減税率) and consumption tax rules
- **Point System**: Built-in customer point rewards
- **Multiple Shipping**: Send a single order to multiple addresses
- **Plugin Ecosystem**: Extensible via plugins with a dedicated marketplace ([Owners Store](https://www.ec-cube.net/owners/))

### Technology Stack

| Component | Technology |
|-----------|-----------|
| Language | PHP 8.2 / 8.3 / 8.4 / 8.5 |
| Framework | Symfony 7.4 |
| ORM | Doctrine ORM 3.x, DBAL 4.x |
| Template | Twig 3.x |
| Database | PostgreSQL 13–18 / MySQL 8.4 LTS |
| Frontend | Sass, webpack, Bootstrap 5.3, jQuery 4.x |

### Quick Start

#### Docker (Recommended)

```bash
git clone https://github.com/EC-CUBE/ec-cube.git
cd ec-cube
docker compose -f docker-compose.yml -f docker-compose.pgsql.yml up -d
# Access https://127.0.0.1:4430/ (admin: https://127.0.0.1:4430/admin/)
# Note: login does not work over HTTP (http://localhost:8080) because the
# session cookie is issued with SameSite=None, which browsers only accept over HTTPS.
```

#### Composer

```bash
composer create-project ec-cube/ec-cube ec-cube "4.4.x-dev" --keep-vcs
cd ec-cube
bin/console eccube:install
```

### Documentation

- [Developer Documentation (Japanese)](https://doc4.ec-cube.net/)
- [System Requirements](https://doc4.ec-cube.net/quickstart/requirement)
- [LLM-friendly documentation](./llms.txt)
