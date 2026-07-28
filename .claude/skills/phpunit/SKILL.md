---
name: phpunit
description: EC-CUBE 4.4 の PHPUnit テストを実装・修正するときの規約。「テストを書いて」「PHPUnitテストを追加して」「コントローラのテストを作って」「テストが落ちる原因を調べて直して」などと言われたとき、または tests/Eccube/Tests 配下のテストコードを作成・編集するときに使用する。
---

# PHPUnit テスト規約（EC-CUBE 4.4）

**対象**: `tests/Eccube/Tests/**/*Test.php`
**前提**: PHPUnit 11（`vendor/bin/phpunit` を直接実行）/ PHP 8.2+ / Symfony 7.4

## 基本ルール

- テストは `tests/Eccube/Tests/` 配下に、名前空間 `Eccube\Tests\...` をディレクトリ構成と一致させて配置する。
- クラスは原則 `final` で宣言する。クラス名・ファイル名は `〜Test` で終える。
- PHP ファイル先頭には EC-CUBE のライセンスヘッダを付与する（`php-cs-fixer` が強制）。
- メソッド引数・戻り値には型宣言を付ける（PHPStan level 6 を通すこと）。
- 実装後は必ずテストを実行し、`vendor/bin/phpstan analyse src` も通す。

## 基底クラスの選択

EC-CUBE のテスト基底クラスは用途で使い分ける。

| 種別 | 継承する基底クラス | 用途 |
|------|--------------------|------|
| Web（コントローラ）テスト | `Eccube\Tests\Web\AbstractWebTestCase` | HTTP リクエストを伴う画面・APIのテスト |
| Repository / Service / FormType 等 | `Eccube\Tests\EccubeTestCase` | コンテナ・EntityManager を使うユニット寄りのテスト |

継承関係は `AbstractWebTestCase` → `EccubeTestCase` → `Symfony\...\WebTestCase`。
`EccubeTestCase` は `$this->entityManager` やフィクスチャ生成メソッドを提供する。

## 実装パターン

### Web（コントローラ）テスト

```php
namespace Eccube\Tests\Web;

use Eccube\Entity\Product;
use Eccube\Repository\ProductRepository;

final class ExampleControllerTest extends AbstractWebTestCase
{
    private ?ProductRepository $productRepository = null;

    protected function setUp(): void
    {
        parent::setUp();
        // リポジトリは EntityManager 経由で取得する
        $this->productRepository = $this->entityManager->getRepository(Product::class);
    }

    public function testRouting(): void
    {
        // 親クラスが用意した $this->client を使う
        $crawler = $this->client->request('GET', $this->generateUrl('product_list'));

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }
}
```

- HTTP クライアントは親クラスが用意する `$this->client`（`KernelBrowser`）を使う。自前で生成しない。
- URL は文字列直書きではなく `$this->generateUrl('route_name')` で生成する。
- ログインが必要な場合は `$this->loginTo($User)`（または `$this->logIn()`）を使う。

### フィクスチャ生成

テストデータは `EccubeTestCase` のヘルパを使う（内部で `Eccube\Tests\Fixture\Generator` に委譲）。
手書きで Entity を組み立てない。

```php
$Customer = $this->createCustomer();                 // 会員
$Product  = $this->createProduct();                  // 商品（ProductClass 3 件付き）
$Order    = $this->createOrder($Customer);           // 受注
$Customers = $this->createCustomers(3);              // 複数件
```

主なヘルパ: `createCustomer()` / `createCustomerAddress()` / `createProduct()` /
`createOrder()` / `createOrderWithProductClasses()` / `createCustomers()` /
`createOrders()` / `createProducts()`。

### データプロバイダ

複数パターンは `#[DataProvider]` 属性（PHPUnit 11 の属性ベース）で記述する。
旧来の `@dataProvider` アノテーションは使わない。

```php
use PHPUnit\Framework\Attributes\DataProvider;

#[DataProvider('provideStatuses')]
public function testStatus(int $status, bool $expected): void
{
    // ...
}

public static function provideStatuses(): array
{
    return [
        [1, true],
        [2, false],
    ];
}
```

## よくある間違い

- ❌ `@dataProvider` アノテーション → ✅ `#[DataProvider]` 属性（PHPUnit 11）。
- ❌ `new Client()` など HTTP クライアントの自前生成 → ✅ 親クラスの `$this->client`。
- ❌ URL の文字列直書き（`'/products/list'`）→ ✅ `$this->generateUrl('product_list')`。
- ❌ Entity の手組み → ✅ `createXxx()` フィクスチャヘルパ。
- ❌ 支払方法のデフォルト/再選択テストで `find(1)` 等の ID 前提 → ✅ `Generator::createPayment()` で sort_no・利用条件を明示し `assertSame()` で再選択先を固定（フィクスチャ並び変更で偽陽性になり得る）。
- ❌ ステータス値のハードコーディング（`if ($status == 1)`）→ ✅ 定数（例: `OrderStatus::NEW`）を使う。
- ❌ 型宣言の省略 → ✅ 引数・戻り値に型を付け、PHPStan level 6 を通す。
- ❌ `setUp()` で未宣言のプロパティに代入（`$this->Member = ...`）→ ✅ プロパティを必ず宣言する。PHP 8.2 の動的プロパティ deprecation が `failOnDeprecation`（`phpunit.xml.dist`）で CI red になる。
- ❌ テストのプロパティを非 nullable で宣言（`protected array $Items = [];`）→ ✅ `protected ?array $Items = null;` と nullable にする。`EccubeTestCase::cleanUpProperties()` が tearDown で全プロパティに `null` を代入するため、非 nullable だと `TypeError` で全テストが落ちる（初期値が必要なら `setUp()` で代入する）。
- ❌ HTML パートを持たないメールに `assertEmailHtmlBodyNotContains()` → ✅ `assertNull($Message->getHtmlBody())`。前者は `str_contains(null, …)` の deprecation を出し、かつ「HTML パートが無いので必ず通る」空振りアサーションになる。

## 実行方法

```bash
# 全テスト
vendor/bin/phpunit

# 単一ファイル
vendor/bin/phpunit tests/Eccube/Tests/Web/ProductControllerTest.php

# フィルタ
vendor/bin/phpunit --filter testRouting
```
