# EC-CUBE
[![Build Status](https://travis-ci.org/EC-CUBE/ec-cube.svg)](https://travis-ci.org/EC-CUBE/ec-cube)
[![Coverage Status](https://img.shields.io/coveralls/EC-CUBE/ec-cube.svg)](https://coveralls.io/r/EC-CUBE/ec-cube)

* * * * * * * * * * * * * * * * * * * *
### インストール方法
* Composerのインストール  
  `curl -sS https://getcomposer.org/installer | php`
* EC-CUBEインストーラーの実行
  実行前に環境に合わせて修正すること  
  `sh eccube_intall.sh mysql`  
  `sh eccube_intall.sh pgsql`  
* `app/config/eccube/config.yml`を環境に合わせて作成  
```
# sample
database:
    driver: pdo_pgsql
    dbname: db_name
    user: db_user
    password : password
    charset: utf8
mail:
    host: localhost
    port: 25
    username: vagrant
    password: vagrant
    encryption: 
    auth_mode: 
```

* * * * * * * * * * * * * * * * * * * *
### β開発

現在、eccube-3.0.0-betaブランチにてβ版の開発を行っております。
開発の指針については、[Wikiの開発要件](https://github.com/EC-CUBE/ec-cube/wiki/%E9%96%8B%E7%99%BA%E8%A6%81%E4%BB%B6)を参照してください。
[プロトタイプ](https://github.com/shinichi-takahashi/ec-cube-poc)をもとに、Silex＋DoctrineORMの構築へ向け、全体に大きなリファクタを行います。

* ～3/初週：土台の挿げ替え
  + ORMの導入
    - ec-cube-poc参照
  + ディレクトリ・ファイル構成の整備
    - [Wiki参照](https://github.com/EC-CUBE/ec-cube/wiki/%E9%96%8B%E7%99%BA%E8%A6%81%E4%BB%B6#%E3%83%87%E3%82%A3%E3%83%AC%E3%82%AF%E3%83%88%E3%83%AA%E6%A7%8B%E6%88%90)
  + Smarty3の導入
    - 未実装
  + DBの見直し
    - https://github.com/EC-CUBE/ec-cube/issues/4
    - スキーマの見直し
  + テスト
～3/末：全機能のリファクタ

* * * * * * * * * * * * * * * * * * * *
### 開発協力

コードの提供・追加、修正・変更その他「EC-CUBE」への開発の御協力（以下「コミット」といいます）を行っていただく場合には、
[EC-CUBEのコピーライトポリシー](https://github.com/EC-CUBE/ec-cube/blob/50de4ac511ab5a5577c046b61754d98be96aa328/LICENSE.txt)をご理解いただき、ご了承いただく必要がございます。
pull requestを送信する際は、EC-CUBEのコピーライトポリシーに同意したものとみなします。


### 開発方針

本リポジトリは、EC-CUBEメジャーバージョンアップを目的としており、
以下の対応を開発方針として定めます。

* 新規機能開発
* 構造（DB構造を含む）の変化を伴う大きな修正
* 画面設計にかかわる大きな修正

PullRequestを送る際は、必ず紐づくissueをたて、その旨を明示してください。
規約等については、POLICY.mdを参照してください。

安定版であるEC-CUBE2.13系の保守については、[EC-CUBE/eccube-2_13](https://github.com/EC-CUBE/eccube-2_13/)にて開発を行っております。

## マイルストーン
* * * * * * * * * * * * * * * * * * * *
|   version  | リリース予定 |                                                         概要                                                         |
|------------|--------------|----------------------------------------------------------------------------------------------------------------------|
| α版       | -2015/01/22  | 構造の大きな変更<br>フロント：購入フローの短縮化<br>管理画面：ダッシュボード/会員登録のUI変更・マルチデバイス対応    |
| β版       | -2015/03/31  | 全機能の実装（テストコード含）が完了し、正常系動作を保証                                                             |
| RC版       | -2015/04/30  | 全機能（テストコード含）が不具合なく動作                                                                             |
| リリース版 | -2015/05/31  | 製品版<br>新機能・変更機能・画面一覧についてのドキュメントを合わせて公開                                             |
