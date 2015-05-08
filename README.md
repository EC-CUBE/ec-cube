# EC-CUBE3
[![Build Status](https://travis-ci.org/EC-CUBE/ec-cube.svg?branch=eccube-3.0.0-beta)](https://travis-ci.org/EC-CUBE/ec-cube)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/EC-CUBE/ec-cube/badges/quality-score.png?b=eccube-3.0.0-beta)](https://scrutinizer-ci.com/g/EC-CUBE/ec-cube/?branch=eccube-3.0.0-beta)
[![Code Coverage](https://scrutinizer-ci.com/g/EC-CUBE/ec-cube/badges/coverage.png?b=eccube-3.0.0-beta)](https://scrutinizer-ci.com/g/EC-CUBE/ec-cube/?branch=eccube-3.0.0-beta)




### ver.βのインストール方法

現在の最新バージョンは3.0.0-beta(以下、ver.β)です。  
Web画面からのインストーラーは未実装のため、以下の手順にてインストールを行ってください。  

* `eccube_install.sh`を開き、30行目から43行目を環境に合わせて変更してください。
```
CONFIG_PHP="app/config/eccube/config.php"
CONFIG_YML="app/config/eccube/config.yml"
ADMIN_MAIL=${ADMIN_MAIL:-"admin@example.com"}
SHOP_NAME=${SHOP_NAME:-"EC-CUBE SHOP"}
HTTP_URL=${HTTP_URL:-"http://test.local/"} # EC-CUBEを動かすURLに変更
HTTPS_URL=${HTTPS_URL:-"http://test.local/"} # EC-CUBEを動かすURLに変更
ROOT_URLPATH=${ROOT_URLPATH:-"/"} # DocumentRootからEC-CUBEを動かすディレクトリへのパスへ変更
DOMAIN_NAME=${DOMAIN_NAME:-""}
ADMIN_DIR=${ADMIN_DIR:-"admin/"}

DBSERVER=${DBSERVER-"127.0.0.1"} # DBサーバのIPに変更
DBNAME=${DBNAME:-"cube3_dev"} # EC-CUBEをインストールするDB名に変更
DBUSER=${DBUSER:-"cube3_dev_user"} # DBのユーザ名に変更
DBPASS=${DBPASS:-"password"} # DBのパスワードに変更
```

* `eccube_install.sh mysql` もしくは `eccube_install.sh pgsql` をコマンドラインにて実行
* インストール完了後、 `http://{インストール先URL}/admin` にアクセス
* EC-CUBEの管理ログイン画面が表示されればインストール成功です。
* ID: `admin` PW: `password` にてログインしてください。



### ver.βの確認環境・不具合

+ 動作確認環境：
    + Apache/2.2.15 (Unix)  
    + PHP5.4.14
    + PostgreSQL 9.2.1   
    + ブラウザー：Google Chrome  

ver.βは、開発段階のため、まだ不具合が残っております。  


* EC-CUBE ver.2.13のコードで動作しているページのリンクが不正 ([一覧](https://github.com/EC-CUBE/ec-cube/wiki/%E6%9C%AA%E7%BD%AE%E3%81%8D%E6%8F%9B%E3%81%88%E3%83%9A%E3%83%BC%E3%82%B8%E4%B8%80%E8%A6%A7))
* 商品画像が表示されない
* 集計が正しく行われない

などがあります。  
その他の不具合等はIssueにてご報告いただけると幸いです。


### デバッグモードの有効化
html/index.phpを書き換えて、Applicationに設定を渡してあげれば、デバッグモードで開発ができます。  
開発の手助けになる、WebProfilerやDebug情報が出力されるようになります。  
設定は、 array('env' => 'dev') です。

#### before
```
<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Eccube\Application();
$app->run();
```

#### after
```
<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Eccube\Application(array(
    'env' => 'dev',
));
$app->run();
```




* * * * * * * * * * * * * * * * * * * *




### 開発の最初の1歩の参考に

[開発環境の構築](http://qiita.com/chihiro-adachi/items/645fee870d50a985dc88)  
[GitHubの利用方法](http://qiita.com/chihiro-adachi/items/f31c9d90b1bcc3553c20)

[リファクタリングガイドライン](https://github.com/EC-CUBE/ec-cube/wiki/%E3%83%AA%E3%83%95%E3%82%A1%E3%82%AF%E3%82%BF%E3%82%AC%E3%82%A4%E3%83%89%E3%83%A9%E3%82%A4%E3%83%B3)  
[EC-CUBE3のメモ - 画面を作ってみる -](http://qiita.com/chihiro-adachi/items/28af6e0b3837983515fe)  
[EC-CUBE3のメモ - ユニットテスト -](http://qiita.com/chihiro-adachi/items/f2fd1cbe10dccacb3631)  




### 開発への参加

eccube-3.0.0-betaブランチにてEC-CUBE3正式版へ向けた開発を行っております。  
これまでのEC-CUBEから、Silexベースのコードへの置き換えを進めています。  

以下を対象に開発・修正を行い、必要に応じてリファクタリングを行います。

* 新規機能開発
* 構造（DB構造を含む）の変化を伴う大きな修正
* 画面設計にかかわる大きな修正

リファクタリング以外のPullRequestを送る際は、必ず紐づくissueをたて、その旨を明示してください。
規約等については、[コーディング規約](POLICY.md)を参照してください。




### 開発協力に関して

コードの提供・追加、修正・変更その他「EC-CUBE」への開発の御協力（Issue投稿、PullRequest投稿など、GitHub上での活動）を行っていただく場合には、
[EC-CUBEのコピーライトポリシー](https://github.com/EC-CUBE/ec-cube/blob/50de4ac511ab5a5577c046b61754d98be96aa328/LICENSE.txt)をご理解いただき、ご了承いただく必要がございます。
pullRequestを送信する際は、EC-CUBEのコピーライトポリシーに同意したものとみなします。



* * * * * * * * * * * * * * * * * * * *



### EC-CUBE2系の保守について


安定版であるEC-CUBE2.13系の保守については、[EC-CUBE/eccube-2_13](https://github.com/EC-CUBE/eccube-2_13/)にて開発を行っております。

