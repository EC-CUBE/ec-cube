---
name: event-subscriber
description: EC-CUBE 4.4 のイベント（EventSubscriber/EventListener・EC-CUBE独自イベント・テンプレートイベント・Doctrineイベント）を実装・改修するときの規約。「イベントサブスクライバを作って」「リスナーを追加して」「このイベントを購読して」「処理にフックして」「テンプレートに差し込んで」「ログイン時に処理を足して」などと言われたとき、または src/Eccube/Event・src/Eccube/EventListener・app/Customize/EventListener 配下を作成・編集するときに使用する。
---

# イベント規約（EC-CUBE 4.4）

**対象**: `src/Eccube/Event/**`, `src/Eccube/EventListener/**`, `src/Eccube/Doctrine/EventSubscriber/**`,
`app/Customize/EventListener/**`, プラグインの `EventListener/**`
**前提**: Symfony 7.4（EventDispatcher）/ PHP 8.2+

> 目的: EC-CUBE の拡張は「コア改変ではなくイベント購読」が基本。
> Symfony 標準の `EventSubscriberInterface` に EC-CUBE 独自イベント・テンプレートイベント・Doctrine イベントが乗る構造を正しく使う。

## イベントの4分類（まず種類を見分ける）

| 種類 | ペイロード | 購読キー | 用途 |
|---|---|---|---|
| **Symfony Kernel イベント** | `RequestEvent` / `ResponseEvent` 等 | `KernelEvents::REQUEST` 等 | リクエスト/レスポンスのライフサイクル |
| **EC-CUBE 独自イベント** | `EventArgs` | `EccubeEvents::XXX` 定数 | コントローラ処理の前後にフック |
| **テンプレートイベント** | `TemplateEvent` | **テンプレートのファイル名** | 画面への差し込み（Skill `twig-template`） |
| **Doctrine イベント** | `LifecycleEventArgs` 等 | `#[AsDoctrineListener(event: ...)]` | エンティティの永続化前後 |

## 基本ルール

- サブスクライバは `Symfony\Component\EventDispatcher\EventSubscriberInterface` を実装する。
- **`getSubscribedEvents()` は `static` メソッド**で、`[イベント名 => メソッド名]` を返す（static にしないと登録されない）。
- **サービス登録は不要**。`services.yaml` の `autoconfigure: true` で `Eccube\` / `Customize\` / `Plugin\` 配下は
  自動的に `kernel.event_subscriber` タグが付く。**手動で services.yaml に登録すると二重登録になる**。
- EC-CUBE 独自イベントの名前は **`EccubeEvents` クラスの定数**を使う（文字列直書きはタイポの温床）。
  命名は `CONTEXT_CONTROLLER_ACTION_PHASE`（例: `FRONT_PRODUCT_INDEX_INITIALIZE`, `ADMIN_ORDER_EDIT_COMPLETE`）。
- **優先度（priority）は数値が大きいほど先に実行**される。
- Doctrine の作成日時/更新者の自動設定などは `#[AsDoctrineListener]` を使う（`SaveEventSubscriber` が手本）。
- **イベントリスナーに業務ロジックを集中させない**。重い処理は Service に委譲し、リスナーは「フック点で Service を呼ぶ」薄い層に保つ（Skill `service`）。

## 実装パターン

### EC-CUBE 独自イベントの購読（最も多い拡張）
コントローラが `new EventArgs([...], $request)` を dispatch する。リスナーは `getArgument()`/`setArgument()` で値を読み書きする。

```php
class ProductListExtendListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            EccubeEvents::FRONT_PRODUCT_INDEX_SEARCH => 'onSearch',
        ];
    }

    public function onSearch(EventArgs $event): void
    {
        $qb = $event->getArgument('qb');        // コントローラが渡した QueryBuilder
        // ... 検索条件を足す ...
        $event->setArgument('qb', $qb);         // 変更を書き戻す
    }
}
```

- EventArgs の **第1引数は arguments 配列**（後で `getArgument('key')`）、**第2引数は `Request`**。`new EventArgs(['key' => $v], $request)`。
- レスポンスを差し替えたいときは `$event->setResponse(...)`。コントローラ側は `if ($event->hasResponse()) return $event->getResponse();` で受ける。

### Kernel イベント（複数メソッド・優先度指定）
```php
public static function getSubscribedEvents(): array
{
    return [
        KernelEvents::REQUEST => [
            ['onKernelRequestEarly', 500],  // 大きい数値 = 先に実行
            ['onKernelRequest', 6],
        ],
        KernelEvents::EXCEPTION => ['onKernelException', -4],
    ];
}

public function onKernelRequest(RequestEvent $event): void
{
    if (!$event->isMainRequest()) {   // サブリクエストを除外するのが定石
        return;
    }
    // ...
}
```

### Doctrine イベント
```php
#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::preUpdate)]
class ExampleDoctrineListener
{
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        // method_exists でトレイト拡張の有無を見てから触るのがコアの作法
    }
}
```

### テンプレートイベント
ファイル名がイベント名。`addSnippet()` / `addAsset()` / `setSource()` で差し込む。詳細は Skill `twig-template`。

## よくある間違い

- ❌ `getSubscribedEvents()` を非 static で定義 → ✅ `public static function` にする（さもないと登録されない）
- ❌ イベント名を文字列直書き（`'front.product.index.initialize'`）→ ✅ `EccubeEvents::FRONT_PRODUCT_INDEX_INITIALIZE` 定数
- ❌ autoconfigure 済みなのに services.yaml で手動登録 → ✅ 登録しない（二重発火を防ぐ）
- ❌ EventArgs の第1引数に値を直接渡す → ✅ `['key' => $value]` の連想配列で渡し `getArgument('key')` で取る
- ❌ 優先度を「小さいほど先」と誤解 → ✅ **大きい数値が先**
- ❌ Kernel イベントでサブリクエストを除外し忘れる → ✅ `if (!$event->isMainRequest()) return;`
- ❌ テンプレートイベント／Doctrine イベントに業務ロジックを書き込む → ✅ Service へ委譲し、リスナーは薄く保つ

## 実行・確認方法

QA ツール・コンソールは Docker 上で実行する（Skill `docker-qa`）。

```bash
bin/console debug:event-dispatcher                                   # 登録済みリスナー一覧
bin/console debug:event-dispatcher 'front.product.index.initialize'  # 特定イベントの購読状況・優先度
```

- 自作リスナーが効かないときは、まず `debug:event-dispatcher` に出ているか（＝登録されているか）を確認する。
- 出ていなければ `getSubscribedEvents()` が static か、クラスが autoconfigure 対象パスにあるかを疑う。

---

実装・改修後は、Skill `review-responsibility` でリスナーに業務ロジックが偏っていないか点検すること。
