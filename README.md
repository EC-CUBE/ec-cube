ec-cube
=======

### 開発方針

本リポジトリは、EC-CUBEメジャーバージョンアップを目的としており、
以下の対応を開発方針として定めます。

* 新規機能開発
* 構造の変化を伴う大きな修正
* 画面設計にかかわる大きな修正

安定版であるEC-CUBE2.13系の保守については、[EC-CUBE/eccube-2_13](https://github.com/EC-CUBE/eccube-2_13/)にて開発を行っております。

### 開発環境構築方法

﻿CentOS, Apache, PostgreSQL, PHPの環境をVagrantで構築できます。

#### 設定方法

##### Vagrant のインストール

1. <http://www.vagrantup.com/> からダウンロードしてインストール
2. 必要なプラグインをインストールしておく

```sh
vagrant plugin install vagrant-omnibus
```

##### VirtualBox のインストール

<https://www.virtualbox.org/> からダウンロードしてインストール

#### 使用方法

##### 起動

初回は、仮想マシンのイメージをダウンロードするため時間がかかります。

```sh
vagrant up
```

##### ssh 接続

```sh
vargrant ssh
```

##### 設定変更する場合

```sh
vargrant provision
```

##### 仮想マシンを破棄する場合

```sh
vargrant destroy
```
