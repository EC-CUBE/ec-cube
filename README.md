# EC-CUBE 4.0-beta

[![Build Status](https://travis-ci.com/EC-CUBE/ec-cube.svg?branch=4.0)](https://travis-ci.com/EC-CUBE/ec-cube)
[![Build status](https://ci.appveyor.com/api/projects/status/lg3uh1539cwln2g6/branch/4.0?svg=true)](https://ci.appveyor.com/project/ECCUBE/ec-cube/branch/4.0)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/EC-CUBE/ec-cube/badges/quality-score.png?b=4.0)](https://scrutinizer-ci.com/g/EC-CUBE/ec-cube/?branch=4.0)
[![Coverage Status](https://coveralls.io/repos/github/EC-CUBE/ec-cube/badge.svg?branch=4.0)](https://coveralls.io/github/EC-CUBE/ec-cube?branch=4.0)

[![Slack](http://img.shields.io/badge/slack-join%5fchat-brightgreen.svg?style=flat)](https://ec-cube.slack.com/messages)

[![Deploy](https://www.herokucdn.com/deploy/button.png)](https://heroku.com/deploy)


+ 本ドキュメントはEC-CUBEの開発者を主要な対象者としております。  
+ パッケージ版は正式リリース後に[EC-CUBEオフィシャルサイト](http://www.ec-cube.net)で配布します。  
+ カスタマイズやEC-CUBEの利用、仕様に関しては[開発コミュニティ](http://xoops.ec-cube.net)をご利用ください。  
+ 本体開発にあたって不明点などあれば[Issue](https://github.com/EC-CUBE/ec-cube/wiki/Issues%E3%81%AE%E5%88%A9%E7%94%A8%E6%96%B9%E6%B3%95)をご利用下さい。
+ EC-CUBE 3.0系のについては、masterブランチにて開発を行っております。
+ EC-CUBE 2.13系の保守については、[EC-CUBE/eccube-2_13](https://github.com/EC-CUBE/eccube-2_13/)にて開発を行っております。

## インストール

### EC-CUBE 4.0のインストール方法

開発ドキュメントの [インストール方法](http://doc4.ec-cube.net/quickstart_install) の手順に従ってインストールしてください。

### CSS の編集・ビルド方法

[Sass](http://sass-lang.com) を使用して記述されています。
Sass のソースコードは `html/template/{admin,default}/assets/scss` にあります。
前提として [https://nodejs.org/ja/] より、 Node.js をインストールしておいてください。

以下のコマンドでビルドすることで、 `html/template/{admin,default}/assets/css` に CSS ファイルが出力されます。

```shell
npm install # 初回のみ
npm run build # Sass のビルド
```

### 動作確認環境

* Apache/2.4.x (mod_rewrite / mod_ssl 必須)
* PHP7.1.20
* PostgreSQL 9.2.1   
* ブラウザー：Google Chrome  

詳しくは開発ドキュメントの [システム要件](http://doc4.ec-cube.net/quickstart_requirement) をご確認ください。

## ドキュメント

### [EC-CUBE 4.0 開発ドキュメント@doc4.ec-cube.net](http://doc4.ec-cube.net/)


EC-CUBE 4.0 の仕様や手順、開発Tipsに関するドキュメントを掲載しています。  
修正や追記、新規ドキュメントの作成をいただく場合、以下のレポジトリからPullRequestをお送りください。  
[https://github.com/EC-CUBE/ec-cube.github.io](https://github.com/EC-CUBE/ec-cube.github.io)

## 開発への参加

EC-CUBE 4.0の不具合の修正、機能のブラッシュアップを目的として、継続的に開発を行っております。  
コードのリファクタリング、不具合修正以外のPullRequestを送る際は、Pull Requestのコメントなどに意図を明確に記載してください。  

Pull Requestの送信前に、Issueにて提議いただく事も可能です。  
Issuesの利用方法については、[こちら](https://github.com/EC-CUBE/ec-cube/wiki/Issues%E3%81%AE%E5%88%A9%E7%94%A8%E6%96%B9%E6%B3%95)をご確認ください。  

[Slack](https://ec-cube.slack.com/messages)でも本体の開発に関する意見交換などを行っております。



### コピーライトポリシーへの同意

コードの提供・追加、修正・変更その他「EC-CUBE」への開発の御協力（Issue投稿、PullRequest投稿など、GitHub上での活動）を行っていただく場合には、
[EC-CUBEのコピーライトポリシー](https://github.com/EC-CUBE/ec-cube/blob/50de4ac511ab5a5577c046b61754d98be96aa328/LICENSE.txt)をご理解いただき、ご了承いただく必要がございます。
Issueの投稿やPullRequestを送信する際は、EC-CUBEのコピーライトポリシーに同意したものとみなします。
