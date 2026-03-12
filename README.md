# EC-CUBE 4.3

[![Unit test for EC-CUBE](https://github.com/EC-CUBE/ec-cube/actions/workflows/unit-test.yml/badge.svg?branch=4.3)](https://github.com/EC-CUBE/ec-cube/actions/workflows/unit-test.yml)
[![E2E test for EC-CUBE](https://github.com/EC-CUBE/ec-cube/actions/workflows/e2e-test.yml/badge.svg?branch=4.3)](https://github.com/EC-CUBE/ec-cube/actions/workflows/e2e-test.yml)
[![Plugin test for EC-CUBE](https://github.com/EC-CUBE/ec-cube/actions/workflows/plugin-test.yml/badge.svg?branch=4.3)](https://github.com/EC-CUBE/ec-cube/actions/workflows/plugin-test.yml)
[![PHPStan](https://github.com/EC-CUBE/ec-cube/actions/workflows/phpstan.yml/badge.svg?branch=4.3)](https://github.com/EC-CUBE/ec-cube/actions/workflows/phpstan.yml)
[![codecov](https://codecov.io/gh/EC-CUBE/ec-cube/branch/4.3/graph/badge.svg?token=BhnPjjvfwd)](https://codecov.io/gh/EC-CUBE/ec-cube)

[![Slack](https://img.shields.io/badge/slack-join%5fchat-brightgreen.svg?style=flat)](https://join.slack.com/t/ec-cube/shared_invite/enQtNDA1MDYzNDQxMTIzLTY5MTRhOGQ2MmZhMjQxYTAwMmVlMDc5MDU2NjJlZmFiM2E3M2Q0M2Y3OTRlMGY4NTQzN2JiZDBkNmQwNTUzYzc)

**4.2からの更新内容は[リリースノート](https://github.com/EC-CUBE/ec-cube/releases/tag/4.3.0)をご確認ください。**

+ 本ドキュメントはEC-CUBEの開発者を主要な対象者としております。
+ パッケージ版は[EC-CUBEオフィシャルサイト](https://www.ec-cube.net)で配布しています。
+ カスタマイズやEC-CUBEの利用、仕様に関しては[開発コミュニティ](https://xoops.ec-cube.net)をご利用ください。
+ 本体開発にあたって不明点などあれば[Issue](https://github.com/EC-CUBE/ec-cube/wiki/Issues%E3%81%AE%E5%88%A9%E7%94%A8%E6%96%B9%E6%B3%95)をご利用下さい。
+ EC-CUBE 3系の保守については、 [EC-CUBE/ec-cube3](https://github.com/EC-CUBE/ec-cube3/)にて開発を行っております。
+ EC-CUBE 2系の保守については、 [EC-CUBE/ec-cube2](https://github.com/EC-CUBE/ec-cube2/)にて開発を行っております。

## インストール

### EC-CUBE 4.3のインストール方法

開発ドキュメントの [インストール方法](https://doc4.ec-cube.net/quickstart/install) の手順に従ってインストールしてください。

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
* PHP 8.1.x / 8.2.x / 8.3.x
* PostgreSQL 12.x or higher / MySQL 8.4.x
* ブラウザー：Google Chrome

詳しくは開発ドキュメントの [システム要件](https://doc4.ec-cube.net/quickstart/requirement) をご確認ください。

## ドキュメント

### [EC-CUBE 4.x 開発ドキュメント@doc4.ec-cube.net](https://doc4.ec-cube.net/)


EC-CUBE 4.x 系の仕様や手順、開発Tipsに関するドキュメントを掲載しています。
修正や追記、新規ドキュメントの作成をいただく場合、以下のレポジトリからPullRequestをお送りください。
[https://github.com/EC-CUBE/doc4.ec-cube.net](https://github.com/EC-CUBE/doc4.ec-cube.net)

## 開発への参加

EC-CUBE 4.3の不具合の修正、機能のブラッシュアップを目的として、継続的に開発を行っております。  
コードのリファクタリング、不具合修正以外のPullRequestを送る際は、Pull Requestのコメントなどに意図を明確に記載してください。  

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
| Language | PHP 8.1 / 8.2 / 8.3 |
| Framework | Symfony 6.4 |
| ORM | Doctrine ORM 2.x |
| Template | Twig 3.8 |
| Database | PostgreSQL 12+ / MySQL 8.4 |
| Frontend | Sass, webpack, jQuery |

### Quick Start

#### Docker (Recommended)

```bash
git clone https://github.com/EC-CUBE/ec-cube.git
cd ec-cube
docker compose -f docker-compose.yml -f docker-compose.pgsql.yml up -d
# Access http://localhost:8080
```

#### Composer

```bash
composer create-project ec-cube/ec-cube ec-cube "4.3.x-dev" --keep-vcs
cd ec-cube
bin/console eccube:install
```

### Documentation

- [Developer Documentation (Japanese)](https://doc4.ec-cube.net/)
- [System Requirements](https://doc4.ec-cube.net/quickstart/requirement)
- [LLM-friendly documentation](./llms.txt)
