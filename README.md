# EC-CUBE3

[![Build Status](https://travis-ci.org/EC-CUBE/ec-cube.svg?branch=master)](https://travis-ci.org/EC-CUBE/ec-cube)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/EC-CUBE/ec-cube/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/EC-CUBE/ec-cube/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/EC-CUBE/ec-cube/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/EC-CUBE/ec-cube/?branch=master)

※本ドキュメントはEC-CUBEの開発者を主要な対象者としております。  
※パッケージ版をご利用の方は[EC-CUBEオフィシャルサイト](http://www.ec-cube.net)をご確認ください。

## EC-CUBE3のインストール方法

* 予めMySQLもしくはPostgreSQLでデータベースを作成しておいて下さい。
* htmlのフォルダが、DocumentRootとなるように設置してください
* htmlがDocumentRootでない場合は、http://{DocumentRoot}/{htmlへのパス} となります。


### シェルスクリプトインストーラー

* eccube_install.shの51行目付近、`Configuration（）`以下の設定内容を環境に応じて修正し実行してください。

* PostgreSQL

    > eccube_install.sh pgsql

* MySQL

    > eccube_install.sh mysql


インストール完了後、 http://{インストール先URL}/admin にアクセス  
EC-CUBEの管理ログイン画面が表示されればインストール成功です。以下のID/Passwordにてログインしてください。  
ID: admin PW: password 


### Webインストーラーを利用したインストール

* composerを利用して外部ライブラリのインストール

    > curl -sS https://getcomposer.org/installer | php  
    > php ./composer.phar install --dev --no-interaction

* Webインストーラーにアクセス

    インストール先のhttp://{インストール先URL}/にアクセスし表示されるインストーラーの指示にしたがってインストールしてください。

-------
### 動作確認環境

* Apache/2.2.15 (Unix)
* PHP5.4.14
* PostgreSQL 9.2.1   
* ブラウザー：Google Chrome  

### デバッグモードの有効化

通常 `/` や`/index.php`でアクセスしているところを `/index_dev.php` と書き換えてアクセスすることにより、デバッグモードが有効になります。
デバッグモードでは、開発の手助けになる、WebProfilerやDebug情報が出力されるようになります。

* `You are not allowed to access this file. Check index_dev.php for more information.`のようなエラーが表示される場合

  `index_dev.php`を開き、アクセス元のIPを以下の配列に追加してください。
```
$allow = array(
    '127.0.0.1',
    'fe80::1',
    '::1',
);
```

### 開発の最初の1歩の参考に

[EC-CUBE3のトピック@Qiita](http://qiita.com/tags/ec-cube3)

### ドキュメント

[EC-CUBE3 開発ドキュメント@ec-cube.github.io](http://ec-cube.github.io/)

EC-CUBE3 の仕様や手順、開発Tipsに関するドキュメントを掲載しています。

修正や追記、新規ドキュメントの作成をいただく場合、以下のレポジトリからPullRequestをお送りください。

https://github.com/EC-CUBE/ec-cube.github.io

### 開発への参加

EC-CUBE3の不具合の修正、機能のブラッシュアップを目的として、継続的に開発を行っております。

コードのリファクタリング、不具合修正以外のPullRequestを送る際は、Pull Requestのコメントなどに意図を明確に記載してください。  
Pull Requestの送信前に、Issueにて提議いただく事も可能です。

Issuesの利用方法については、[こちら](https://github.com/EC-CUBE/ec-cube/wiki/Issues%E3%81%AE%E5%88%A9%E7%94%A8%E6%96%B9%E6%B3%95)をご確認ください。

Gitterでも本体の開発に関する意見交換などを行っております。
[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/EC-CUBE/ec-cube?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)


### 開発協力に関して

コードの提供・追加、修正・変更その他「EC-CUBE」への開発の御協力（Issue投稿、PullRequest投稿など、GitHub上での活動）を行っていただく場合には、
[EC-CUBEのコピーライトポリシー](https://github.com/EC-CUBE/ec-cube/blob/50de4ac511ab5a5577c046b61754d98be96aa328/LICENSE.txt)をご理解いただき、ご了承いただく必要がございます。
pullRequestを送信する際は、EC-CUBEのコピーライトポリシーに同意したものとみなします。


### EC-CUBE2系の保守について

安定版であるEC-CUBE2.13系の保守については、[EC-CUBE/eccube-2_13](https://github.com/EC-CUBE/eccube-2_13/)にて開発を行っております。


                                ,.                       
                              :::;;;;                    
                          :::::::;;;;;;;;                
                   ::::::::::::::;;;;;;;;;;;;;;;         
               ::::::::::::::::::;;;;;;;;;;;;;;;##       
           ::::::::::::::::::::::;;;;;;;;;;;;;;;##       
          ,::::::::::::::::::::::;;;;;;;;;;;;;;;##       
          ,::::::::::::::::::::::;;;;;;;;;;;;;;;##       
          ,::::::::::::::::::::::;;;;;;;;;;;;;;'##       
          ,::::::::::::::::::::::;;;;;;;;;;;;;;###       
          ,::::::::::::::::::::::;;;;;;;;;;;;;'###       
          ,##::::::::::::::::::::;;;;;;;;;;;;#####       
          ,##::::::::::::::::::::;;;;;;;;;;;;#####       
          :####::::::::::::::::::;;;;;;;;;########:,     
        ,:;######;:::::::::::::::;;;;;;;'#########:::,   
      .:::;#############;::::::::;'###############:::::  
      ::::;#####################################;::::::  
      `::::::::::;########################::::::::::::,  
        `::::::::::::::::#############:::::::::::::::,   
            `:::::::::::::::::::::::::::::::::::::,      
                 ::::::::::::::::::::::::::,             

                      #                                  
       ###### ######  #  ######  #   #  ####   ######    
       ##     ##      #  ##      #   #  #   #  ##        
       ###### ##      #  ##      #   #  #####  ######    
       ##     ##      #  ##      #   #  #   #  ##        
       ###### ######  #  ######  #####  #####  ######    
                      #                                  
