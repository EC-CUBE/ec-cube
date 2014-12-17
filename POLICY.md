##  開発方針
* * * * * * * * * * * * * * * * * * * * * * * * 

### 共通規約

#### DataBase
* dtb\_, mtb\_はつけないこと [issue](https://github.com/EC-CUBE/ec-cube/issues/4)
* mtb\_系統をひとつのテーブルで取り扱う[issue](https://github.com/EC-CUBE/ec-cube/issues/5)
  ただし、id, name, rank以外の構造のmtb\_（mtb\_constants / mtb\_zipなど）は別table（constants / zip）とする
* IDカラムを持つ場合は{TABLE_NAME:単数形}_idの形式とする
```
table name: products
ID: product_id
```
* table名、column名はsnake_caseとする
* flag系統はXXX_flgに綴りを統一する

#### コーディング規約
* PSR-2に準拠すること（[PSR日本語訳](http://www.infiniteloop.co.jp/docs/psr/psr-2-coding-style-guide.html)）

* namespaceを記載すること
```
    ex: Eccube\Class\Pages\Products
```
* class名、method名にSC\_やsf、LC\_やlfなどのprefixをつけないこと
* ファイル命名規則はUpperCamelCaseとする
```
    [old]
    /data/class/pages/admin/products/LC_Page_Admin_Products_UploadCSVCategory.php
    [new]
    /data/class/pages/admin/products/UploadCsvCategory.php
```
* data/class_extends/を廃止する

#### data/class/pages/以下について
* data/class/pages/以下のClass->action()は以下に示すフォーマットに従う
* $this->getMode()はswitch文の中の１回のみとし、引数に$thisを渡すこと
これにより下記のhook point の生成が可能となる
  + {$CLASS}\_action\_(before|after)()
  + {$CLASS}\_{$MODE}\_before()

* カウンタ変数以外でのローカル変数を許容しない
  必ず、クラス変数とすること [issue](https://github.com/EC-CUBE/ec-cube/issues/6)
```
    [NG]
    $foo = 'bar';
    [OK]
    $this->foo = 'bar';
```


#### autoloader
下記の呼び出し以外のrequire記述を禁じる
```
    require_once '/data/ClassLoader.php';
```



### 構造変更

##### Router
* html/index.phpからhtml/router.phpを読み込み、URLに応じたclassを呼び出す構造とする [issue](https://github.com/EC-CUBE/ec-cube/issues/7)
* ルーティングの実装はhtml/router.phpを作成し、設定ファイル以外の読み込みをしない構造とする
* ルーティングを示す設定ファイルをhtml/routes.phpとする
  routes.phpに定義する内容は以下に従う。
```
$arrMaps = array(
    array(
        'method' => 'GET|POST',
        'path'   => '/',
        'dir'    => '',
        'class'  => 'index'
    ),
    array(
        'method' => 'GET|POST',
        'path'   => '/products/detail',
        'dir'    => 'products',
        'class'  => 'detail'
    ),
    array(
        'method' => 'GET|POST',
        'path'   => '/admin/',
        'dir'    => 'admin',
        'class'  => 'index'
    ),
    array(
        'method' => 'GET|POST',
        'path'   => '/admin/login',
        'dir'    => 'admin',
        'class'  => 'login'
    ),
);
```

#### autoloader
data/ClassLoader.phpを作成し、全classをautoloadできるようにする [issue](https://github.com/EC-CUBE/ec-cube/issues/8)

#### Plugin作成において（α以降）
* 設定ファイルはYAML形式にて記述すること。
* プラグイン内で一貫したnamespaceを備えること。1
```
    Vendor\package
```
* Smartyテンプレート内でinclude_php()を使用した読み込みをしないこと
  + 構造を変更する必要あり

### API
* * * * * * * * * * * * * * * * * * * * * * * * 
実装をする。議論・案出しはissueにて行う [issue](https://github.com/EC-CUBE/ec-cube/issues/9)

