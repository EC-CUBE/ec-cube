# Selenideを使用した自動テストスクリプト

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->


- [フォルダ・パッケージ構成](#%E3%83%95%E3%82%A9%E3%83%AB%E3%83%80%E3%83%BB%E3%83%91%E3%83%83%E3%82%B1%E3%83%BC%E3%82%B8%E6%A7%8B%E6%88%90)
- [設定の変更方法](#%E8%A8%AD%E5%AE%9A%E3%81%AE%E5%A4%89%E6%9B%B4%E6%96%B9%E6%B3%95)
  - [Selenideの機能で設定できるもの](#selenide%E3%81%AE%E6%A9%9F%E8%83%BD%E3%81%A7%E8%A8%AD%E5%AE%9A%E3%81%A7%E3%81%8D%E3%82%8B%E3%82%82%E3%81%AE)
  - [テスト対象特有のもの](#%E3%83%86%E3%82%B9%E3%83%88%E5%AF%BE%E8%B1%A1%E7%89%B9%E6%9C%89%E3%81%AE%E3%82%82%E3%81%AE)
- [PageObjectデザインパターン](#pageobject%E3%83%87%E3%82%B6%E3%82%A4%E3%83%B3%E3%83%91%E3%82%BF%E3%83%BC%E3%83%B3)
- [Pageクラスの作成](#page%E3%82%AF%E3%83%A9%E3%82%B9%E3%81%AE%E4%BD%9C%E6%88%90)
  - [命名のコツ](#%E5%91%BD%E5%90%8D%E3%81%AE%E3%82%B3%E3%83%84)
  - [全体の構成](#%E5%85%A8%E4%BD%93%E3%81%AE%E6%A7%8B%E6%88%90)
  - [コンストラクタ](#%E3%82%B3%E3%83%B3%E3%82%B9%E3%83%88%E3%83%A9%E3%82%AF%E3%82%BF)
  - [Publicメソッドの作り方](#public%E3%83%A1%E3%82%BD%E3%83%83%E3%83%89%E3%81%AE%E4%BD%9C%E3%82%8A%E6%96%B9)
  - [セレクタの指定とprivateメソッド](#%E3%82%BB%E3%83%AC%E3%82%AF%E3%82%BF%E3%81%AE%E6%8C%87%E5%AE%9A%E3%81%A8private%E3%83%A1%E3%82%BD%E3%83%83%E3%83%89)
  - [Assertionの書き方](#assertion%E3%81%AE%E6%9B%B8%E3%81%8D%E6%96%B9)
  - [ヘッダのような共通部分の扱い](#%E3%83%98%E3%83%83%E3%83%80%E3%81%AE%E3%82%88%E3%81%86%E3%81%AA%E5%85%B1%E9%80%9A%E9%83%A8%E5%88%86%E3%81%AE%E6%89%B1%E3%81%84)
- [Testクラスの作成](#test%E3%82%AF%E3%83%A9%E3%82%B9%E3%81%AE%E4%BD%9C%E6%88%90)
  - [必要なアノテーション](#%E5%BF%85%E8%A6%81%E3%81%AA%E3%82%A2%E3%83%8E%E3%83%86%E3%83%BC%E3%82%B7%E3%83%A7%E3%83%B3)
  - [データ駆動テスト（外からのデータ読み込み）](#%E3%83%87%E3%83%BC%E3%82%BF%E9%A7%86%E5%8B%95%E3%83%86%E3%82%B9%E3%83%88%EF%BC%88%E5%A4%96%E3%81%8B%E3%82%89%E3%81%AE%E3%83%87%E3%83%BC%E3%82%BF%E8%AA%AD%E3%81%BF%E8%BE%BC%E3%81%BF%EF%BC%89)
    - [1件だけ読み込む](#1%E4%BB%B6%E3%81%A0%E3%81%91%E8%AA%AD%E3%81%BF%E8%BE%BC%E3%82%80)
    - [複数件読み込む](#%E8%A4%87%E6%95%B0%E4%BB%B6%E8%AA%AD%E3%81%BF%E8%BE%BC%E3%82%80)
    - [テストコード内でデフォルト値を元にデータを生成する](#%E3%83%86%E3%82%B9%E3%83%88%E3%82%B3%E3%83%BC%E3%83%89%E5%86%85%E3%81%A7%E3%83%87%E3%83%95%E3%82%A9%E3%83%AB%E3%83%88%E5%80%A4%E3%82%92%E5%85%83%E3%81%AB%E3%83%87%E3%83%BC%E3%82%BF%E3%82%92%E7%94%9F%E6%88%90%E3%81%99%E3%82%8B)
- [実行時にブラウザを変更する](#%E5%AE%9F%E8%A1%8C%E6%99%82%E3%81%AB%E3%83%96%E3%83%A9%E3%82%A6%E3%82%B6%E3%82%92%E5%A4%89%E6%9B%B4%E3%81%99%E3%82%8B)
- [ブラウザ拡張を読み込んで起動する](%E3%83%96%E3%83%A9%E3%82%A6%E3%82%B6%E6%8B%A1%E5%BC%B5%E3%82%92%E8%AA%AD%E3%81%BF%E8%BE%BC%E3%82%93%E3%81%A7%E8%B5%B7%E5%8B%95%E3%81%99%E3%82%8B)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

## フォルダ・パッケージ構成

現在のテスト（src/test以下）の構成は以下のようになっています。いくつか特殊なフォルダは説明を割愛しています。

| フォルダ | 内容 |
|:------|:--------|
|java/jp/shiftinc/automation/eccube3| テストコードの中身を記載します。|
|　data | ECサイトで言えばユーザや商品のようなデータを表すクラス用のパッケージです。|
|　pages | ページオブジェクトを格納するパッケージです。|
|　scenarios | テストケースを格納するパッケージです。この名前は変えても良く、複数種類のテストが同居する場合には親パッケージ(testcasesなど)の下に種類ごとのフォルダを作るのが良いでしょう。|
|　util | 特定のケースによらないユーティリティクラスを格納するパッケージです。|
|resources||
|　fixtures/eccube3/scenarios|テストケースの中で使うテストデータを格納するフォルダです。ケースごとにフォルダを分け、どこで使われるリソースかをわかるようにしておきます。|
|　config.properties|（名前はプロジェクトにより変更）次で述べる設定値を記載したファイルです。|

## 設定の変更方法

検証環境のURLやログイン情報など、テストの実行時に変更しうるものは設定ファイルに書き出しておき、環境によってテストコードの内部を書き換えなくても良いようにしておきます。

設定の内容はすべて`src/test/resources/config.properties`に記載していますが、項目としては大きく「Selenideの機能」「それ以外（テスト対象特有）」に分かれます。

### Selenideの機能で設定できるもの

下記がSelenideの機能で設定できるものです。上から、

- 検証環境のベースのURL
- ブラウザの種類
- 指定した条件が満たされない時の待機時間のタイムアウト(ms)
- 指定した条件が満たされない待機中の、条件をチェックする間隔(ms)

```
selenide.baseUrl=http://192.168.99.100
selenide.browser=jp.shiftinc.automation.driver.ChromeDriverProvider
selenide.timeout=10000
selenide.pollingInterval=500
```

Selenideはこれら決まったキーの値をJavaのシステムプロパティから受け取るようになっていますが、このプロジェクトではテスト開始時に`config.properties`にある`selenide.XX`というプロパティをすべてシステムプロパティに設定するという処理を入れているので、このファイルの値が使用されます。実行時にさらにコマンドラインからシステムプロパティを与えた場合は、コマンドラインのものが優先されます（`Configuration.java`参照）。

ブラウザの種類は、`core` ライブラリによって定義されています（`jp.shiftinc.tech.automation.driver.*DriverProvider`）。v1.0.2では次のブラウザをサポートしています。

|クラス名|ブラウザ|説明|
|:---|:---|:---|
|ChromeDriverProvider|Google Chrome/Chromium||
|HeadlessChromeDriverProvider|Google Chrome/Chromium Headless|GUIを表示しない動作モード。Chrome/Chromium v59から利用可能|
|GeckoDriverProvider|Firefox (v48以降)||
|LegacyFirefoxDriverProvider|Firefox (v47以前)||
|IEDriverProvider|Internet Explorer||
|EdgeDriverProvider|Microsoft Edge||
|SafariDriver|Apple Safari (macOS)||


その他に使えるキーとしてはSelenideの`Configuration`クラスのドキュメントを参照してください。

### テスト対象特有のもの

その他、テスト対象特有の設定項目を自由に追加することができます。デフォルトでは、EC-CUBEの管理画面のID/PWが入っています。

```
## admin account
admin.id=admin
admin.password=password
```

これに呼応して、`ConfigurationKey.java`ではテスト側からこの設定値を呼び出すためのキーを定数で指定しています。つまり、新しい項目を追加するには`config.properties`と`ConfigurationKey.java`両方に変更を加える必要があります。

```java
package jp.shiftinc.automation.eccube3.constant;

public class ConfigurationKey {
    public static final String ADMIN_ID = "admin.id";
    public static final String ADMIN_PASSWORD = "admin.password";
}
```

設定した値は、Testクラスの中で`configuration.get(ConfigurationKey.【キー名】)`のようにして呼び出すことができます。

## PageObjectデザインパターン

テストスクリプト用のコードのほとんどは、大きく分けて「pages」以下のクラス群と「scenarios」以下のクラス群に分かれています。これは「Webページの操作」と「テストケース(シナリオ)」を分離するという考え方に基づいています。この考え方を**PageObjectデザインパターン**と呼びます。
テスト対象ページ内の要素取得や具体的な操作をpage側のクラスに隠蔽し、テストケース側ではそれらのpage側クラスが提供する機能(「IDを入力する」「ログインボタンを押す」等)を使用してテスト内容を記述していきます。
このように役割分担するとテスト対象ページに変更が入った場合もPageクラス側(pages以下)を修正すればTestクラス側(scenarios以下)をほぼ変更せずにすむため、保守性の高いスクリプトを書くことができます。

## Pageクラスの作成

`pages`以下に作成するPageクラスの作成方法について説明します。Pageクラスは、原則1画面に対して1クラスずつ作っていきます。ダイアログやウィザードなど、画面としては完全に分かれていない場合でも独立した機能をいくつか持っているようであれば分けて作ります。

### 命名のコツ

クラス名はあまり自由に付けてしまうと迷う時間が長くなったり、後でどの画面か分からなくなったりするため、基本的には画面のURLに呼応するような形で付けます。たとえば、URLが`http://192.168.99.100/admin/product/product/new`であれば`jp.shiftinc.automation.eccube.pages.admin.product.ProductNewPage`といったような感じです。

### 全体の構成

Pageクラスは、大まかに

- コンストラクタ
  - 画面に正しく遷移したことを確認する役割も持つ
- publicメソッド
  - Testクラスから呼び出され、操作や値の取得をする
- privateメソッド
  - publicメソッドの一部を切り出したもの

から成ります。

### コンストラクタ

Selenideでは画面遷移を伴うメソッドで`return page(XXPage.class)`のような書き方で遷移先のPageクラスのインスタンスを返すことができます。このときコンストラクタが呼ばれ、以下のようなshouldXX()を含む実装にしておくことでこの条件が成り立っているかどうかをチェックしてくれます。条件に合わない場合は、想定している画面に遷移していないとみなしてテストが失敗します。

```java
public ProductNewPage() {
    $("h1 span").shouldHave(text("商品登録"));
}
```

### Publicメソッドの作り方

publicメソッドはTestクラスから呼ばれるメソッドです。なるべくTest側でPageの細かい作りを気にしなくて良いように、1つ1つの入力操作のような単位ではなく「会員登録に必要な情報を入力して確認画面を開くボタンをクリックする」くらいのサイズで作成します。

```java
@Step("商品を登録する:{0}")
public ProductMasterPage registerProduct(Product product) {
    productDetail(product);

    $("#admin_product_name").setValue(product.getProductName());
    $("#admin_product_class_product_type_" + Integer.toString(product.getProductType())).setValue(Integer.toString(product.getProductType()));
    $("#admin_product_description_detail").setValue(product.getProductDescriptionDetail());
    $("#admin_product_class_price02").setValue(Integer.toString(product.getSellingPrice()));
    $("#admin_product_class_stock").setValue(Integer.toString(product.getProductStock()));
    $("#admin_product_Category").parent().parent().$("a").click(); //商品カテゴリのアコーディオンパネルを開く
    $("#admin_product_Category_" + Integer.toString(product.getProductCategory())).setSelected(true);

    executeJavaScript("scroll(0, 0);"); // 公開、非公開を押せるように画面を一番上に戻す
    $("#admin_product_Status_" + Integer.toString(product.getProductStatus())).click();

    $$("#aside_column button").first().click();
    return Selenide.page(ProductMasterPage.class);
}
```

大きく2つのルールがあります。

- `@Step`アノテーションの付与
  - メソッドには必ず`@Step`アノテーションを付け、`()`内にそのメソッドで行う操作・確認内容を記載します。ここで書いた内容がAllure Reportに表示されます。メソッドの引数をレポートに出したい場合は、引数の順序に従って`{0}``{1}`のような書き方をすることで引数の値に入れ替えたものが出力されます。
- 返り値の作り方
  - メソッドの返り値は基本的に操作後のPageクラスとなります。returnするときには、上の例のように`Selenide.page(【Pageクラス】.class)`としてPageクラスをインスタンス化します。

### セレクタの指定とprivateメソッド

一般的には上のpublicメソッドの例のように要素のセレクタを直接メソッド内に書くのではなくクラスの上部にまとめてフィールドとして書くことが多いですが、Selenide+Allureを使う場合はあまりその恩恵を受けられないため、ここでは敢えてセレクタはメソッド内に直接書いています。

同じセレクタが複数箇所に登場すると保守性が下がってしまうため、登場箇所は極力1箇所に抑えられるようにメソッドを切ります。また、Allureではメソッドに付けたアノテーションの単位でしか動作を区切ることができません。Allureで細かい動作を見たい場合は、それだけ細かくpublicメソッドの中身を割って、privateメソッドを作る必要があります。上の商品登録の例であれば、最初のほうは次のように書き換えられます。

```java
@Step("商品を登録する:{0}")
public ProductMasterPage registerProduct(Product product) {
    productDetail(product);

    setName(product.getProductName())
    .setProductType(product.getProductType());
    ...
}

@Step("商品名を{0}に設定する")
private ProductMasterPage setName(String name) {
    $("#admin_product_name").setValue(name);
}

@Step("商品種別を{0}に設定する")
private ProductMasterPage setProductType(int type) {
    $("#admin_product_class_product_type_" + Integer.toString(type)).setValue(Integer.toString(type));
}
```

### Assertionの書き方

通常、Selenideを使ったテストでは`shouldXX()`というメソッドを使ってTest側でassertion（確認）を行います。下記は公式ドキュメントの例です。

```java
  GoogleSearchPage searchPage = open("/login", GoogleSearchPage.class);
  GoogleResultsPage resultsPage = searchPage.search("selenide");
  resultsPage.results().shouldHave(size(10));
  resultsPage.results().get(0).shouldHave(text("Selenide: Concise UI Tests in Java"));
```

しかし、この書き方では「専用のメソッドを切っていないため、確認項目に関する`@Step`をAllureで出力できない」という弱点があります。また、PageObjectデザインパターンの「assertはTest側で行う」という原則は満たしているものの、「HTMLの情報はPage側に押し込める」という点についてはグレーになっています。

そこで現在はレポート出力の利便性を優先し、各Pageクラスに必要なより具体的な`shouldXX()`を用意してTestからはそちらを呼ぶようにしています。

```java
@Step("商品名が{0}であることを確認する")
public void shouldHaveProductName(String productName) {
    getItemName().shouldHave(text(productName));
}
```

### ヘッダのような共通部分の扱い

ヘッダやサイドメニューなど、複数画面に共通して存在する部品はモジュールとして切り出しておくと便利です。モジュールの中身はPageクラスと同じように実装します。

```java
public class HeaderModule {

    @Step("ログアウトする")
    public LoginPage logout() {
        $(".navbar-menu .cb-angle-down").click();
        $(byXpath("//a[text()='ログアウト']")).click();

        return page(LoginPage.class);
    }
}
```

この部品を扱うPageクラスでは、モジュールを返すようなメソッドを作っておきます。この例では、「ログイン後の画面にはすべて共通のヘッダが存在する」という前提でログイン後の画面を表す抽象クラスを作り、個別のPageからはそれを継承するようにしています。

```java
public abstract class AdminAuthorizedPage {

    public HeaderModule header() {
        return Selenide.page(HeaderModule.class);
    }
}
```

ヘッダを持つPageを扱う側のクラスは下記のようになります。

```java
@Stories("管理画面にログイン・ログアウトできる")
@Test
public void adminCanLoginLogout() {
    LoginPage loginPage = open("/admin", LoginPage.class);
    loginPage.login(configuration.get(ConfigurationKey.ADMIN_ID), configuration.get(ConfigurationKey.ADMIN_PASSWORD))
            .header()
            .logout();
}
```

## Testクラスの作成

Testクラスにはテストの手順を記載します。こちら側には、HTMLの構成を意識する内容は入れないように注意してください。また、テスト設計書とのトレーサビリティを保つためにアノテーションの内容やクラス名に命名規則を設定してテストの番号等を入れておくと良いでしょう。

### 必要なアノテーション

Allureにはテスト結果を集計するためのアノテーションがありますが、今回はその中の`@Features`と`@Stories`を使用します。

```java
@Features("会員登録")
public class RegisterUserNegativeTest extends BaseTest {

    // 中略

    @Stories("会員登録の情報に誤りがあった場合、登録できない(YAML使用)")
    @Test(dataProvider = "default")
    public void userCannotRegisterProfile(User user) throws Exception {
        EntryPage entryPage = open("/entry", EntryPage.class);
        entryPage.registerUserInvalid(user);
        // 登録画面に遷移したことを確認できているので、これで完了
    }
}
```

各アノテーションの内容は案件ごとにカスタマイズしてOKですが、レポート上はFeatures単位→Stories単位でまとめられるので、Featuresに機能のカテゴリ、Storiesに実際にどんなことをテストするのかを書くのがバランスが良いでしょう。この例ではクラスに`@Features`、メソッドに`@Stories`を付けています。また、テストであると認識させるためにTestNGのアノテーション`@Test`をメソッドに付ける必要があります。たとえば「会員登録」「商品検索」などがFeatures、会員登録の下で「正常なデータを入れて新規会員登録が行える」というStoriesがあるというイメージです。

### データ駆動テスト（外からのデータ読み込み）

データ駆動テストとは、テストの手順と入出力のデータの値を分けて管理し、テストの成否が環境やデータに左右されないようにすることを言います。手順とデータを分けることで複数のデータで同じ手順を回すことも容易になるので、この複数パターン回すことをデータ駆動テストと呼ぶケースもありますが、元々の意味は手順とデータの分離自体のことを指します。

このプロジェクトでは、データを外出ししたファイルから読み込むために[SnakeYAML](https://bitbucket.org/asomov/snakeyaml)というライブラリを用いています。SnakeYAMLは、YAML形式で書かれたデータを読み取ってJavaのオブジェクトに変換、もしくはその逆を行ってくれるライブラリです。StringやList、Mapのような単純な型も使えますし、クラス名を指定してフィールドを合わせればそれ以外のオリジナルのクラスと対応させることもできます。EC-CUBEのテストで使うために定義した`User`クラスとそれに対応するYAMLファイルの一部を紹介します。

#### 1件だけ読み込む

Javaのクラスのほうには`@Data`というアノテーションが付いていますが、これはフィールドに対して自動的にgetter/setterを生成してくれる[Lombok](https://projectlombok.org/)というライブラリの機能です。Lombokを使うとソースコードにgetter/setterを書く必要がないので、ぐっとコード量を減らすことができます。`fromYaml()`メソッドは、SnakeYAMLの機能で指定したファイルパスからデータを読み込むものです。

```java
@Data
public class User {

    private String lastName;
    private String firstName;
    private String lastNameKana;
    private String firstNameKana;
    private String zip1;
    private String zip2;
    private String prefecture;
    private String town;
    private String address;
    private String tel1;
    private String tel2;
    private String tel3;

    // 略

    public static User fromYaml(String path) {
        User user = (User) (new Yaml()).load(User.class.getResourceAsStream(path));
        return user.uniqueEmail();
    }
}
```

こちらがYAMLのファイルです。このように、Javaのフィールド名とYAML側のキーの名前を揃えていくと、SnakeYAMLが自動的に`setLastName()`のようなメソッドを探して実行してくれます。逆を言うと、SnakeYAMLの仕様に合わせたsetterを用意しておかないとインスタンスを生成することはできません。

```yml
!!jp.shiftinc.automation.eccube3.data.User
lastName: 山田
firstName: 太郎
lastNameKana: ヤマダ
firstNameKana: タロウ
zip1: 106
zip2: 0041
prefecture: 東京都
town: 港区麻布台
address: 2-4-5
tel1: 03
tel2: 6809
tel3: 1128
```

インスタンスを生成しているTestクラスのコードは下のようになります。

```java
@Features("会員登録")
public class RegisterUserPositiveTest extends BaseTest {
    private User user;

    @BeforeMethod
    public void setup() {
        user = User.fromYaml("/fixtures/eccube3/scenarios/RegisterUserPositiveTest/user.yml");
    }
}
```

#### 複数件読み込む

次に、複数件読み込む例です。`User`クラスには、`fromYaml()`同様に複数件のデータを読み込むための`listFromYaml()`メソッドを用意してあります（ここでは割愛）。YAMLとTestクラスは下記のようになります。

```yml
- !!jp.shiftinc.automation.eccube3.data.User #姓が未入力
  lastName:
  firstName: 太郎
  lastNameKana: ヤマダ
  firstNameKana: タロウ

- !!jp.shiftinc.automation.eccube3.data.User #名が未入力
  lastName: 山田
  firstName:
  lastNameKana: ヤマダ
  firstNameKana: タロウ
```

YAMLは先程と似たような形です。違いとしては、クラス名の前についている`- `によってListの要素を示しており、読み込んだ結果は要素数が2のListとなります。

```java
@Features("会員登録")
public class RegisterUserNegativeTest extends BaseTest {

    @DataProvider(name = "default")
    public static Object[][] parameters() {
        List<User> userList = User.listFromYaml("/fixtures/eccube3/scenarios/RegisterUserNegativeTest/users.yml");
        Object[][] parameters = new Object[userList.size()][1];
        for (int i = 0; i < parameters.length; i++) {
            parameters[i][0] = userList.get(i);
        }
        return parameters;
    }

    @Stories("会員登録の情報に誤りがあった場合、登録できない(YAML使用)")
    @Test(dataProvider = "default")
    public void userCannotRegisterProfile(User user) throws Exception {
        EntryPage entryPage = open("/entry", EntryPage.class);
        entryPage.registerUserInvalid(user);
        // 登録画面に遷移したことを確認できているので、これで完了
    }
}
```

Javaで複数データを読み込んで繰り返しテストを行うには、TestNGのDataProviderという仕組みを使います。データを提供するメソッドは返り値の型が決まっており、`@DataProvider`というアノテーションを付ける必要があります。使う側のメソッドでは、`@Test`アノテーションのパラメタとして`dataProvider`を指定し、提供されたデータと同じ型をパラメタとして受け取ります。これで、データが2件あればテスト用のメソッドが2回実行されるようになります。

#### テストコード内でデフォルト値を元にデータを生成する

1つ前の例では、複数データでテストを行うために2件のデータのフィールドをすべてフルに指定していました。しかし、特に異常系のテストでは「特定のフィールドが空になっていれば、あとは適当な正常データが入っていれば良い」というようなケースが多く、すべてをフルに指定してしまうとかえってテストの意図が分かりにくくなることがあります。そういったケースに対応する方法として、もう1つ別のパターンでデータ生成する例を挙げます。

```java
@Features("会員登録")
public class RegisterUserNegativeDefaultTest extends BaseTest {

    @DataProvider(name = "default")
    public static Object[][] parameters() {
        return new Object[][]{
                { User.getFixture().lastName(""), "姓が未入力" },
                { User.getFixture().firstName(""), "名が未入力" },
                { User.getFixture().lastNameKana(""), "姓（カナ）が未入力" },
                { User.getFixture().firstNameKana(""), "名（カナ）が未入力" },
                { User.getFixture().email2("test@example.com"), "メールアドレス1と2が異なる" }
        };
    }

    ...
}
```

`@DataProvider`の使い方は先程と同じで、パラメタの生成のしかたが異なります。ここでは`User.getFixture()`というメソッド呼び出しでデータのデフォルト値のようなものを生成しており、そこに対して特定のパターンで試したい差分のみ（「姓が未入力」など）を入れてデータとして返しています。これを行うには、`User`クラス側に2つの仕組みが必要です。

- デフォルト値を生成する仕組み
- フィールドを変更するvoid型のメソッドではなく、変更後のインスタンスを返すメソッド

前者は、[Fixture Factory](https://github.com/six2six/fixture-factory)というライブラリを用いています。Fixture Factoryを使うと、予め各フィールドに入れたい値のパターンを正規表現等でルール化しておくことで、ある程度ランダムな値を生成してくれます。ここまでやらなくても固定値で良い、という場合には、前述のSnakeYAMLを使って1件だけデータを指定したYAMLから読み取ったものを使いまわしてもOKです。

```java
// ルールを定義している箇所
static {
    Fixture.of(User.class).addTemplate("valid", new Rule() {
        {
            // うまくランダムに出来なかったものは、とりあえずデフォルト値（全部デフォルト値でも良さそう）
            add("lastName", regex("[あ-ん]{2}"));
            add("firstName", regex("[あ-ん]{2}"));
            add("lastNameKana", regex("[ア-ン]{2}"));
            add("firstNameKana", regex("[ア-ン]{2}"));
            // （略）
        }
    });
}

// 定義したルール（"valid"）を使ってデータを生成している箇所
public static User getFixture() {
    User user = Fixture.from(User.class).gimme("valid");
    return user.uniqueEmail();
}
```

後者は、Lombokで生成したsetterではカバーできない内容なので必要に応じて別メソッドを生成しています。Lombokの機能でこのようなvoid型でないsetterを生成することも可能ですが、一度に自動生成できるsetterの種類は1つであり、こちらに合わせるとSnakeYAMLが動かなくなったりするので注意が必要です。

```java
public User lastName(String lastName) {
    this.lastName = lastName;
    return this;
}

public User firstName(String firstName) {
    this.firstName = firstName;
    return this;
}
```

YAMLでフルにデータ指定する方法とランダムに指定する方法にはそれぞれメリット・デメリットがあるため、テストしたい内容に合わせて使い分けます。

## 実行時にブラウザを変更する

`config.properties`でもテストに使うブラウザを変更することができますが、それだけではブラウザを切り替えるためにいちいち設定ファイルを変更してgitにコミットすることになります。これを避けるため、実行時にシステムプロパティを渡すことでもブラウザを切り替えることができます。

```
# Firefox(48以降)を使う場合
./gradew test -Dselenide.browser=jp.shiftinc.automation.driver.GeckoDriverProvider
```

## ブラウザ拡張を読み込んで起動する
Google Chromeを利用した自動テストにおいて、以下作業を行うことでChrome拡張を有効にし自動テストを行うことができます。

1. Chrome拡張ファイルを入手する
    1. Chrome拡張をインストールする
    1. [拡張機能管理ページ](chrome://extensions/)を開く
    1. 「デベロッパーモード」チェックボックスにチェックを入れる
    1. 各アドオンの行にIDが表示されるので、取得したい拡張のIDを控える
    1. 拡張機能のパッケージ化をクリック
    1. 拡張機能のルートディレクトリに拡張のルートディレクトリを設定
        * Windowsの場合`%USERPROFILE%\AppData\Local\Google\Chrome\User Data\Default\Extensions\[取得したい拡張のID]\[バージョン]`を設定
            * %USERPROFILE%などはChrome上からは展開されないので、適宜展開してください。
            * バージョンは実際に拡張のフォルダにアクセスすると存在する、さらに下のフォルダです
    1. crxファイルが作成されるのでそれを取得する
1. drivers/chrome-extensionディレクトリにchrome拡張ファイル(.crx)を配置する
    * このディレクトリは `driver.chrome.extension` システムプロパティにより変更することも可能です
1. システムプロパティ `driver.chrome.privatebrowse` にfalse文字列を設定する
    * Chrome拡張はプライベートブラウジングモードでは無効のため、本設定によりプライベートブラウジングをオフにします
1. Chromeに対応したWebDriverを起動する

> 上記システムプロパティの名称は、DriverConfigurationKey クラスにて定数として定義されています。
> 
> - CHROME_EXTENSION_PATH: エクステンションを配置したパスを指定
> - CHROME_PRIVATE_BROWSE: プライベートブラウズの有効無効を設定

コードによる設定例は共通ライブラリテストパッケージ内の`driver/WithExtensionChromeDriverTest.java`テストの`BeforeClass`による設定をご覧ください。
