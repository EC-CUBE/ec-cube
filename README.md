# EC-CUBE
[![Build Status](https://travis-ci.org/EC-CUBE/ec-cube.svg?branch=eccube-3.0.0-beta)](https://travis-ci.org/EC-CUBE/ec-cube)
[![Coverage Status](https://coveralls.io/repos/EC-CUBE/ec-cube/badge.svg?branch=eccube-3.0.0-beta)](https://coveralls.io/r/EC-CUBE/ec-cube?branch=eccube-3.0.0-beta)


### β開発への参加

eccube-3.0.0-betaブランチにてβ版の開発を行っております。  
これまでのEC-CUBEから、Silexベースのコードへの置き換えを進めています。  

[リファクタリング対象一覧](https://docs.google.com/spreadsheets/d/1df5Sc4eoEQv4ZVm6_q8-QE0-PduS2gnAtNXVQco0x-8/edit?usp=sharing)

開発着手時は、上記ファイルのassigneeにGitHubアカウントを記入して開発後、プルリクエストをお送り下さい。

### 開発前に

[開発環境の構築](http://qiita.com/chihiro-adachi/items/645fee870d50a985dc88)  
[GitHubの利用方法](http://qiita.com/chihiro-adachi/items/f31c9d90b1bcc3553c20)


### 開発の最初の1歩の参考に

[リファクタリングガイドライン](https://github.com/EC-CUBE/ec-cube/wiki/%E3%83%AA%E3%83%95%E3%82%A1%E3%82%AF%E3%82%BF%E3%82%AC%E3%82%A4%E3%83%89%E3%83%A9%E3%82%A4%E3%83%B3)  
[EC-CUBE3のメモ - 画面を作ってみる -](http://qiita.com/chihiro-adachi/items/28af6e0b3837983515fe)  
[EC-CUBE3のメモ - ユニットテスト -](http://qiita.com/chihiro-adachi/items/f2fd1cbe10dccacb3631)  


### 開発協力に関して

コードの提供・追加、修正・変更その他「EC-CUBE」への開発の御協力（以下「コミット」といいます）を行っていただく場合には、
[EC-CUBEのコピーライトポリシー](https://github.com/EC-CUBE/ec-cube/blob/50de4ac511ab5a5577c046b61754d98be96aa328/LICENSE.txt)をご理解いただき、ご了承いただく必要がございます。
pull requestを送信する際は、EC-CUBEのコピーライトポリシーに同意したものとみなします。

* * * * * * * * * * * * * * * * * * * *
### インストール方法
* Composerのインストール  
  `curl -sS https://getcomposer.org/installer | php`
* EC-CUBEインストーラーの実行
  実行前に環境に合わせて修正すること  
  `sh eccube_intall.sh mysql`  
  `sh eccube_intall.sh pgsql`  

* * * * * * * * * * * * * * * * * * * *



### 開発方針

本リポジトリは、EC-CUBEメジャーバージョンアップを目的としており、
以下の対応を開発方針として定めます。

* 新規機能開発
* 構造（DB構造を含む）の変化を伴う大きな修正
* 画面設計にかかわる大きな修正

リファクタリング以外のPullRequestを送る際は、必ず紐づくissueをたて、その旨を明示してください。
規約等については、POLICY.mdを参照してください。

安定版であるEC-CUBE2.13系の保守については、[EC-CUBE/eccube-2_13](https://github.com/EC-CUBE/eccube-2_13/)にて開発を行っております。

