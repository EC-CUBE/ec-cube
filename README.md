# EC-CUBE 4.0

[![Build Status](https://travis-ci.com/EC-CUBE/ec-cube.svg?branch=4.0)](https://travis-ci.com/EC-CUBE/ec-cube)
[![AppVeyor](https://img.shields.io/appveyor/ci/ECCUBE/ec-cube)](https://ci.appveyor.com/project/ECCUBE/ec-cube)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/EC-CUBE/ec-cube/badges/quality-score.png?b=4.0)](https://scrutinizer-ci.com/g/EC-CUBE/ec-cube/?branch=4.0)
[![Coverage Status](https://coveralls.io/repos/github/EC-CUBE/ec-cube/badge.svg?branch=4.0)](https://coveralls.io/github/EC-CUBE/ec-cube?branch=4.0)

[![Slack](https://img.shields.io/badge/slack-join%5fchat-brightgreen.svg?style=flat)](https://join.slack.com/t/ec-cube/shared_invite/enQtNDA1MDYzNDQxMTIzLTY5MTRhOGQ2MmZhMjQxYTAwMmVlMDc5MDU2NjJlZmFiM2E3M2Q0M2Y3OTRlMGY4NTQzN2JiZDBkNmQwNTUzYzc)

[![Deploy](https://www.herokucdn.com/deploy/button.png)](https://heroku.com/deploy)


+ 本ドキュメントはEC-CUBEの開発者を主要な対象者としております。  
+ パッケージ版は正式リリース後に[EC-CUBEオフィシャルサイト](https://www.ec-cube.net)で配布します。  
+ カスタマイズやEC-CUBEの利用、仕様に関しては[開発コミュニティ](https://xoops.ec-cube.net)をご利用ください。  
+ 本体開発にあたって不明点などあれば[Issue](https://github.com/EC-CUBE/ec-cube/wiki/Issues%E3%81%AE%E5%88%A9%E7%94%A8%E6%96%B9%E6%B3%95)をご利用下さい。
+ EC-CUBE 3系の保守については、 [EC-CUBE/ec-cube3](https://github.com/EC-CUBE/ec-cube3/)にて開発を行っております。
+ EC-CUBE 2系の保守については、 [EC-CUBE/ec-cube2](https://github.com/EC-CUBE/ec-cube2/)にて開発を行っております。

## インストール

### EC-CUBE 4.0のインストール方法

開発ドキュメントの [インストール方法](https://doc4.ec-cube.net/quickstart_install) の手順に従ってインストールしてください。

### CSS の編集・ビルド方法

[Sass](https://sass-lang.com) を使用して記述されています。
Sass のソースコードは `html/template/{admin,default}/assets/scss` にあります。
前提として [https://nodejs.org/ja/] より、 Node.js をインストールしておいてください。

以下のコマンドでビルドすることで、 `html/template/**/assets/css` に CSS ファイルが出力されます。

```shell
npm ci # 初回およびpackage-lock.jsonに変更があったとき
npm run build # Sass のビルド
```

### OAuth2の設定

```shell
mkdir var/oauth
cd var/oauth
openssl genrsa -out private.key 2048
openssl rsa -in private.key -pubout -out public.key
```

[詳しくはこちら](https://oauth2.thephpleague.com/installation/#generating-public-and-private-keys)

[クライアントの作成コマンドはこちら](https://github.com/trikoder/oauth2-bundle/blob/v2.x/docs/basic-setup.md)
[grant-type](https://github.com/trikoder/oauth2-bundle/blob/v2.x/OAuth2Grants.php)

クライアント作成例

```shell
bin/console trikoder:oauth2:create-client --redirect-uri=http://127.0.0.1:8000/ --grant-type=authorization_code --grant-type=client_credentials --grant-type=implicit --grant-type=password --grant-type=refresh_token --scope=read --scope=write
bin/console trikoder:oauth2:list-clients
```

#### Client credentials grant

```shell
curl -X POST \
  http://127.0.0.1:8000/token \
  -H 'cache-control: no-cache' \
  -H 'content-type: application/x-www-form-urlencoded' \
  -d 'grant_type=client_credentials&client_id={client_id}&client_secret={client_secret}&scope=read'
```

#### Resource owner password credentials grant

```shell
curl -X POST \
  http://127.0.0.1:8000/token \
  -H 'cache-control: no-cache' \
  -H 'content-type: application/x-www-form-urlencoded' \
  -d 'grant_type=password&client_id={client_id}&client_secret={client_secret}&scope=read&username=admin&password=password'
```

#### Implicit grant

```uri
http://127.0.0.1:8000/admin/authorize?response_type=token&client_id={client_id}&client_secret={client_secret}&scope=read&state={csrf_token}
```

#### Authorization code grant

```uri
http://127.0.0.1:8000/admin/authorize?response_type=code&client_id={client_id}&redirect_uri={redirect_uri}&scope=read&state={csrf_token}
```

```shell
curl -X POST \
  http://127.0.0.1:8000/token \
  -H 'cache-control: no-cache' \
  -H 'content-type: application/x-www-form-urlencoded' \
  -d 'grant_type=authorization_code&client_id={client_id}&client_secret={client_secret}&redirect_uri={redirect_uri}&code={code}'
```

#### Refresh token grant

```shell
curl -X POST \
  http://127.0.0.1:8000/token \
  -H 'cache-control: no-cache' \
  -H 'content-type: application/x-www-form-urlencoded' \
  -d 'grant_type=refresh_token&client_id={client_id}&client_secret={client_secret}&refresh_token={refresh_token}'
```

### 動作確認環境

* Apache/2.4.x (mod_rewrite / mod_ssl 必須)
* PHP7.1.20
* PostgreSQL 9.2.1   
* ブラウザー：Google Chrome  

詳しくは開発ドキュメントの [システム要件](https://doc4.ec-cube.net/quickstart_requirement) をご確認ください。

## ドキュメント

### [EC-CUBE 4.0 開発ドキュメント@doc4.ec-cube.net](https://doc4.ec-cube.net/)


EC-CUBE 4.0 の仕様や手順、開発Tipsに関するドキュメントを掲載しています。  
修正や追記、新規ドキュメントの作成をいただく場合、以下のレポジトリからPullRequestをお送りください。  
[https://github.com/EC-CUBE/doc4.ec-cube.net](https://github.com/EC-CUBE/doc4.ec-cube.net)

## 開発への参加

EC-CUBE 4.0の不具合の修正、機能のブラッシュアップを目的として、継続的に開発を行っております。  
コードのリファクタリング、不具合修正以外のPullRequestを送る際は、Pull Requestのコメントなどに意図を明確に記載してください。  

Pull Requestの送信前に、Issueにて提議いただく事も可能です。  
Issuesの利用方法については、[こちら](https://github.com/EC-CUBE/ec-cube/wiki/Issues%E3%81%AE%E5%88%A9%E7%94%A8%E6%96%B9%E6%B3%95)をご確認ください。  

[Slack](https://join.slack.com/t/ec-cube/shared_invite/enQtNDA1MDYzNDQxMTIzLTY5MTRhOGQ2MmZhMjQxYTAwMmVlMDc5MDU2NjJlZmFiM2E3M2Q0M2Y3OTRlMGY4NTQzN2JiZDBkNmQwNTUzYzc)でも本体の開発に関する意見交換などを行っております。



### コピーライトポリシーへの同意

コードの提供・追加、修正・変更その他「EC-CUBE」への開発の御協力（Issue投稿、PullRequest投稿など、GitHub上での活動）を行っていただく場合には、
[EC-CUBEのコピーライトポリシー](https://github.com/EC-CUBE/ec-cube/wiki/EC-CUBE%E3%81%AE%E3%82%B3%E3%83%94%E3%83%BC%E3%83%A9%E3%82%A4%E3%83%88%E3%83%9D%E3%83%AA%E3%82%B7%E3%83%BC)をご理解いただき、ご了承いただく必要がございます。
Issueの投稿やPullRequestを送信する際は、EC-CUBEのコピーライトポリシーに同意したものとみなします。
