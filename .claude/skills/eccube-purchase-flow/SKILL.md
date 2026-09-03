---
name: eccube-purchase-flow
description: EC-CUBE 4.4 の受注処理（PurchaseFlow の Processor/Validator）を実装・改修するときの規約。「受注処理を追加して」「値引き/送料/ポイントの計算を入れて」「在庫チェックを追加して」「Processorを作って」「Validatorを作って」「PurchaseFlowにフックして」などと言われたとき、または src/Eccube/Service/PurchaseFlow・プラグインの PurchaseFlow 配下を作成・編集するときに使用する。
---

# PurchaseFlow 規約 — 受注処理パイプライン（EC-CUBE 4.4）

**対象**: `src/Eccube/Service/PurchaseFlow/**`, `app/Plugin/{Code}/Service/PurchaseFlow/**`
**前提**: Symfony 7.4 / PHP 8.2+

> 目的: 受注に関わる計算・検証・確定（送料/手数料/税/値引き/ポイント/在庫引当・採番）は、
> コントローラや汎用 Service に直書きせず、`PurchaseFlow` のパイプライン上の Processor/Validator に置く。
> これが EC-CUBE の受注処理の核心。Skill `eccube-service` と対で使う。

## パイプラインの構造（`PurchaseFlow::validate()` の実行順）

`PurchaseFlow` は **cart / shopping / order** の 3 フローぶん存在し（`PurchaseContext::CART_FLOW` /
`SHOPPING_FLOW` / `ORDER_FLOW`）、それぞれ別の Processor 群を持つ。`validate()` は次の順で実行する
（各段階の間で金額の再集計 `calculateAll()` が走る）:

| 段階 | コンポーネント（基底） | 役割 | 実コード例 |
|---|---|---|---|
| 明細検証 | `ItemValidator`（abstract） | 明細1行ごとの検証 | `StockValidator` `PriceChangeValidator` |
| 受注検証 | `ItemHolderValidator`（abstract） | カート/受注全体の検証 | `EmptyItemsValidator` `StockMultipleValidator` |
| 明細前処理 | `ItemPreprocessor`（interface） | 明細1行ごとの調整 | （コアでは未使用。拡張ポイント） |
| 受注前処理 | `ItemHolderPreprocessor`（interface） | 送料/税/手数料明細の付与・調整 | `TaxProcessor` `DeliveryFeePreprocessor` |
| 値引き | `DiscountProcessor`（interface） | 値引き明細の削除→追加 | `PointProcessor` |
| 最終検証 | `ItemHolderPostValidator`（abstract） | 全処理後の最終検証・確定値の確定 | `AddPointProcessor` `PaymentTotalNegativeValidator` |

確定系は別メソッドで、`validate()` とは独立に呼ばれる:

| メソッド | コンポーネント | 役割 | 実コード例 |
|---|---|---|---|
| `prepare()` | `PurchaseProcessor`（interface） | 仮確定（在庫引当） | `StockReduceProcessor::prepare()` |
| `commit()` | `PurchaseProcessor` | 確定 | `OrderNoProcessor` 系 |
| `rollback()` | `PurchaseProcessor` | 仮確定の取消（在庫戻し） | `StockReduceProcessor::rollback()` |

> `validate()` 内では `removeDiscountItem()` を**全 DiscountProcessor について先に呼び**、値引き明細をクリアしてから
> `addDiscountItem()` を呼ぶ。値引きは「いったん全消し→再計算で積み直す」のが大前提（後述）。

## Item と ItemHolder の違い

- **`ItemInterface`（明細1行）**: `OrderItem` / `CartItem`。`isProduct()` / `isDeliveryFee()` / `isCharge()` /
  `isDiscount()` / `isPoint()` / `isTax()` で**明細種別**を判定し、`getPrice()` / `getPriceIncTax()` /
  `getQuantity()` / `getProductClass()` を持つ。送料・手数料・値引き・税も「明細の1行」として表現される点に注意。
- **`ItemHolderInterface`（受注/カート全体）**: `Order` / `Cart`。`getItems()`（`ItemCollection`）で明細を束ねる。
  **`Order` 固有の処理は `instanceof Order` でガードする**（例: `Cart` には Shipping もポイントも無い）。
- **`PurchaseContext`**: 実行中コンテキスト。`isCartFlow()` / `isShoppingFlow()` / `isOrderFlow()` で
  どのフローかを判定でき、`getOriginHolder()`（フロー実行前の状態）/ `getUser()` を持つ。

## 基本ルール

- **追加先のコンポーネントを正しく選ぶ**: 検証なら Validator、明細の付与/調整なら Preprocessor、値引きなら
  DiscountProcessor、在庫引当・採番など確定処理なら PurchaseProcessor。上表で対応づける。
- **`abstract` 基底は `validate()`（protected）を override する**。`execute()` は `final` で、`InvalidItemException`
  を捕捉して `ProcessResult` に変換する（自分で try/catch しない）。
- **`interface` 系（ItemPreprocessor / ItemHolderPreprocessor / DiscountProcessor / PurchaseProcessor）は
  メソッドを実装する**。PurchaseProcessor は `AbstractPurchaseProcessor` を継承すれば必要なメソッドだけ override 可。
- **`supports()` で早期 return**: フロー種別・`Order` か否か・店舗設定（`BaseInfo`）で適用可否を判定し、
  対象外なら何もしない（`AddPointProcessor::supports()` が手本）。
- **トランザクション境界はリクエスト全体**: `TransactionListener` が `kernel.request` で `beginTransaction()`、
  `kernel.terminate` で `commit()`、例外時に `rollback()` する。Processor 内の `flush()` は SQL を発行するだけで
  **コミットではない**ため、在庫引当・採番の可視性や競合を論じるときは「flush 済み＝確定」と読み替えないこと。
- **金額計算は `bcmath`**（`bcadd` / `bcsub` / `bcmul` / `bccomp`）。float 演算で組まない。
  合計・税・送料・値引きの集計は `PurchaseFlow::calculateAll()` が各段階後に行うので、Processor 側は
  **明細（Item）を足し引きする**ことに集中する（合計の手計算は不要）。

## エラーと警告の使い分け

| 投げ方 | どう扱われるか | 用途 |
|---|---|---|
| `ItemValidator` で `throwInvalidItemException(...)` | **常に warning** に変換され、`handle()` で後処理（数量丸め等）が走る | カート段階の自動補正（在庫超過を在庫数に丸める等） |
| `ItemHolderValidator` / `ItemHolderPostValidator` で `throwInvalidItemException(..., warning: true)` | warning | 続行可能な注意 |
| 同上で `warning` を付けない | **error**（`PurchaseFlowResult::hasError()` が true → 呼び出し側が処理中断） | 購入を止めるべき致命的検証 |
| PurchaseProcessor で `throw new PurchaseException(...)` / `ShoppingException` | 例外が伝播し確定処理が中断 | 在庫引当失敗など確定時の異常 |

- `throwInvalidItemException()` は `ValidatorTrait` のヘルパ。`ProductClass` を渡すと商品名つきメッセージになる。
  メッセージは**翻訳キー**を渡す（`trans()` 相当が内部で走る）。
- `ProcessResult` は `success()` / `warn()` / `error()` のファクトリのみ（直接 new 不可）。`addError` のような
  メソッドは無い。**例外を投げる→基底の `execute()` が `ProcessResult` に変換する**のが正規フロー。

## 実装パターン

### 明細検証（ItemValidator）

```php
namespace Eccube\Service\PurchaseFlow\Processor;

use Eccube\Entity\ItemInterface;
use Eccube\Service\PurchaseFlow\ItemValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;

class StockValidator extends ItemValidator
{
    #[\Override]
    protected function validate(ItemInterface $item, PurchaseContext $context): void
    {
        if (!$item->isProduct()) {
            return; // 商品明細以外は対象外
        }
        if ($item->getProductClass()->isStockUnlimited()) {
            return;
        }
        if ($item->getProductClass()->getStock() < $item->getQuantity()) {
            // ProductClass を渡すと商品名つきメッセージになる。常に warning 化される。
            $this->throwInvalidItemException('front.shopping.out_of_stock', $item->getProductClass());
        }
    }

    #[\Override]
    protected function handle(ItemInterface $item, PurchaseContext $context): void
    {
        // warning 後の自動補正（在庫数に丸める）
        $item->setQuantity($item->getProductClass()->getStock());
    }
}
```

### 受注前処理（ItemHolderPreprocessor）— 明細の付与・調整

```php
class DeliveryFeePreprocessor implements ItemHolderPreprocessor
{
    #[\Override]
    public function process(ItemHolderInterface $itemHolder, PurchaseContext $context): void
    {
        if (!$itemHolder instanceof Order) {
            return; // Cart には Shipping が無い
        }
        // 1. 自分が前に作った明細を消す（ProcessorName で識別）
        // 2. 計算し直して付け直す（冪等にする）
        // OrderItem を new し、setProcessorName(self::class) で自前の明細に印を付ける
    }
}
```

> **冪等性が要**: Preprocessor は `validate()` が複数回走っても結果が変わらないよう、
> 自分が追加した明細を `getProcessorName() === self::class` で識別して**毎回いったん削除→再追加**する
> （`DeliveryFeePreprocessor` が手本）。

### 値引き（DiscountProcessor）

```php
interface DiscountProcessor // 実装する2メソッド
{
    public function removeDiscountItem(ItemHolderInterface $itemHolder, PurchaseContext $context): void;
    public function addDiscountItem(ItemHolderInterface $itemHolder, PurchaseContext $context): ?ProcessResult;
}
```

- `removeDiscountItem()` で自分の値引き明細を削除 → `addDiscountItem()` で追加。**合計金額を超える値引きを作らない**
  （超える場合は利用可能額まで丸めるかスキップし、`ProcessResult::warn()` を返す）。`PointProcessor` が手本。

### 確定処理（PurchaseProcessor）— 在庫引当・採番・ポイント付与

```php
class StockReduceProcessor extends AbstractPurchaseProcessor
{
    #[\Override]
    public function prepare(ItemHolderInterface $itemHolder, PurchaseContext $context): void
    {
        if (!$itemHolder instanceof Order) {
            return;
        }
        // 在庫を引く。失敗時は ShoppingException / PurchaseException を投げる
    }

    #[\Override]
    public function rollback(ItemHolderInterface $itemHolder, PurchaseContext $context): void
    {
        // prepare の逆操作（在庫を戻す）を必ず実装する
    }
}
```

## 対象フローへの登録方法

PurchaseFlow への登録は **2 通り**。どちらも「対象フロー（cart/shopping/order）」を指定する。

### (A) コア: `purchaseflow.yaml` のタグで登録

`app/config/eccube/packages/purchaseflow.yaml` でサービス定義にタグを付ける。`flow_type` で対象フロー、
`priority` で実行順（**降順＝大きいほど先**）を指定する。

```yaml
eccube.purchase.flow.item.validator.stock.validator:
    class: Eccube\Service\PurchaseFlow\Processor\StockValidator
    tags:
        - { name: eccube.item.validator, flow_type: cart, priority: 700 }
```

タグ名（`PurchaseFlowPass` の定数）と対応コンポーネント:

| タグ名 | コンポーネント |
|---|---|
| `eccube.item.validator` | ItemValidator |
| `eccube.item.holder.validator` | ItemHolderValidator |
| `eccube.item.preprocessor` | ItemPreprocessor |
| `eccube.item.holder.preprocessor` | ItemHolderPreprocessor |
| `eccube.discount.processor` | DiscountProcessor |
| `eccube.item.holder.post.validator` | ItemHolderPostValidator |
| `eccube.purchase.processor` | PurchaseProcessor |

### (B) プラグイン/Customize: 属性 `#[CartFlow]` / `#[ShoppingFlow]` / `#[OrderFlow]` で登録

`Kernel` が基底（`ItemValidator` 等）を `registerForAutoconfiguration` でタグ付けするため、
**基底を継承/実装したクラスは自動でタグが付く**。あとは**どのフローに乗せるかを属性で宣言**する
（`src/Eccube/Attribute/CartFlow.php` 等）。`flow_type` ごとの YAML 配線は不要。

```php
use Eccube\Attribute\CartFlow;
use Eccube\Attribute\ShoppingFlow;
use Eccube\Attribute\OrderFlow;
use Eccube\Service\PurchaseFlow\ItemValidator;

#[CartFlow]
#[ShoppingFlow]
#[OrderFlow] // 乗せたいフローだけ付ける
class SaleLimitOneValidator extends ItemValidator
{
    protected function validate(ItemInterface $item, PurchaseContext $context): void { /* ... */ }
}
```

- 手本は `app/Plugin/PurchaseProcessors/Service/PurchaseFlow/Processor/SaleLimitOneValidator.php`。
- `PurchaseFlowPass` は YAML 配線済みなら属性での二重登録を防ぐ（`alreadyWired()`）。**(A) と (B) を併用しない**。
- 属性方式は priority を指定できない（属性だけでは順序制御不可）。**実行順が重要なら (A) の YAML タグ**を使う。

## よくある間違い

- ❌ 受注処理をコントローラや汎用 Service に直書きする／検証と明細付与でコンポーネントを取り違える → ✅ パイプライン表で役割に合う Processor / Validator を選び、そこを拡張する
- ❌ `final` な `execute()` の override・自前 try-catch・`ProcessResult` の `new` → ✅ `validate()` だけ override し `InvalidItemException` を投げる
- ❌ ItemValidator で購入を止めようとする → ✅ ItemValidator は**常に warning**。中断したい検証は `ItemHolderValidator` / `PostValidator` の error にする
- ❌ Preprocessor で明細を追加しっぱなし（再実行で多重化） → ✅ `setProcessorName(self::class)` で印を付け、毎回削除→再追加で冪等にする
- ❌ 値引きで合計金額を超える明細を作る → ✅ 利用可能額まで丸めるかスキップし `ProcessResult::warn()` を返す
- ❌ 金額を float / `+`・`*` で計算 → ✅ `bcadd`/`bcsub`/`bcmul`/`bccomp` を使う
- ❌ `Cart` でも `getShippings()` / `getCustomer()` を呼ぶ → ✅ `instanceof Order` でガード（Cart には Shipping もポイントも無い）
- ❌ PurchaseProcessor の `rollback()` を実装し忘れる → ✅ `prepare()` の逆操作（在庫戻し等）を必ず実装する
- ❌ 属性で実行順を制御する／YAML タグと属性を両方付ける → ✅ 順序は YAML タグの `priority`（降順）。登録はどちらか一方（既定はコア=YAML / プラグイン=属性、順序が要るならプラグインも YAML）
- ❌ 送料無料を商品単位の性質として扱う → ✅ `DeliveryFeeFreePreprocessor` はカート合計と `BaseInfo` の閾値を比べる**カート全体の条件**。確定前の商品ページでは判定できないので出さない

## 実行・確認方法

コンソール・QA ツール（PHPUnit / PHPStan / PHP-CS-Fixer / Rector）の実行方法は AGENTS.md「開発コマンド」を参照。

- パイプラインに実際にどの Processor が、どの順で乗っているかは `PurchaseFlow::dump()`（`__toString()`）で
  ツリー表示できる。登録できているか・順序が意図どおりかの確認に使う。
- プラグインでエンティティ拡張を伴う場合はプロキシ再生成（`bin/console eccube:generate:proxies`）を忘れない。

---

実装・改修後は、Skill `eccube-service`（責務分離）と `eccube-review-responsibility` で点検すること。
プラグインから追加する場合は Skill `eccube-plugin` も参照。
