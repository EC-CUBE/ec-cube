                    PHPUnit を使用したテストケースについて
                 ____________________________________________

	EC-CUBE では, PHPUnit を使用して, テスト駆動開発が可能です.
	このドキュメントは, PHPUnit の使用方法を説明します.


1. PHPUnit について
-------------------

	PHPUnit
	http://www.phpunit.de/

	日本語マニュアル
	http://www.phpunit.de/manual/3.4/ja/index.html

	PHPUnit3.4.x を使用してテストを行います.
	残念ながら, このバージョンでは PHP4 はサポートされません.

2. 動作環境
-----------

	http://www.phpunit.de/wiki/Requirements

3. インストール
---------------

	http://www.phpunit.de/manual/3.4/ja/installation.html

	マニュアルに沿ってインストールしたあと, require.php にて
	PHPUnit/Framework.php のフルパスを指定します.
	これは, EC-CUBE が独自に include_path を設定するため必要です.

4. 実行方法
-----------

	EC-CUBE のクラス名は, PHPUnit の規約に沿ってないため, 引数で
	PHPファイルを指定する必要があります.

	
	すべてのテストを実行するとき

	  TestSuite クラスを実行します.
          ------------------------------------------------------------
	  $ phpunit TestSuite TestSuite.php
	  PHPUnit 3.4.13 by Sebastian Bergmann.

	  .................

	  Time: 0 seconds

	  OK (17 tests, 20 assertions)
          ------------------------------------------------------------

	パッケージごとにテストを実行するとき

	  Package_AllTests クラスを実行します.
          ------------------------------------------------------------
	  $ phpunit DB_AllTests class/db/DB_AllTests.php
	  PHPUnit 3.4.13 by Sebastian Bergmann.

	  ....

	  Time: 0 seconds

	  OK (4 tests, 6 assertions)
          ------------------------------------------------------------

	クラスごとにテストを実行するとき

	  テストクラスを指定して実行します.
          ------------------------------------------------------------
	  $ phpunit LC_Page_Test class/page/LC_Page_Test.php 
	  PHPUnit 3.4.13 by Sebastian Bergmann.

	  ..........

	  Time: 0 seconds

	  OK (10 tests, 11 assertions)
          ------------------------------------------------------------


	  カレントディレクトリからも実行可能です.
          ------------------------------------------------------------
          $ cd class/page
	  $ phpunit LC_Page_Test LC_Page_Test.php 
	  PHPUnit 3.4.13 by Sebastian Bergmann.

	  ..........

	  Time: 0 seconds

	  OK (10 tests, 11 assertions)
          ------------------------------------------------------------

