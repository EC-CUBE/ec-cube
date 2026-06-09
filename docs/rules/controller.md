# Controller 規約 — 責務分離と Fat 化防止（EC-CUBE 4.4）

**対象**: `src/Eccube/Controller/**/*.php`, `app/Customize/Controller/**/*.php`
**前提**: Symfony 7.4 / PHP 8.2+

> 目的: コントローラを「HTTP の入出力変換」に徹した薄い層に保ち、業務ロジックは Service へ寄せる。
> EC-CUBE コアには歴史的に巨大なコントローラ（`CsvImportController` 2000行超等）が存在するが、
> **新規・改修分でそれを増やさない**ことがこの規約の狙い。既存コードの一括修正は求めない。

## コントローラの責務（やってよいこと）

コントローラ 1 アクションの仕事は、原則これだけ:

1. リクエスト（`Request` / ルートパラメータ）を受け取る
2. フォームの生成・`handleRequest` / バリデーション
3. **Service / Repository(参照) を呼んで処理を委譲**する
4. フラッシュメッセージ・リダイレクト・テンプレート変数を組み立てて返す

基底クラス `Eccube\Controller\AbstractController`（および Symfony の親クラス）が提供するヘルパを使う:
`addSuccess()` / `addError()` / `addWarning()` / `addRequestError()` /
`isTokenValid()` / `forwardToRoute()` / `getUser()`（`getUser()` は Symfony 親クラス由来）など。

```php
class ExampleController extends AbstractController
{
    public function __construct(
        private readonly ExampleService $exampleService,   // 業務処理は Service に注入
        private readonly ExampleRepository $exampleRepository,
    ) {
    }

    #[Route(path: '/example/{id}', name: 'example_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Example $Example): Response
    {
        $form = $this->createForm(ExampleType::class, $Example);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->exampleService->update($Example);   // ← 業務ロジックは Service へ委譲
            $this->addSuccess('登録しました', 'admin');

            return $this->redirectToRoute('example_edit', ['id' => $Example->getId()]);
        }

        return $this->render('example/edit.twig', ['form' => $form->createView()]);
    }
}
```

- ルーティングは **`#[Route]` 属性**を使う（属性クラスの FQCN は共通規約に集約。`@Route` アノテーションは使わない）。
- 依存はコンストラクタインジェクション（`private readonly`）。インターフェース型ヒントを優先。

## 認可・CSRF（セキュリティ）

新規・改修アクションでは、**認可**と **CSRF トークン検証**を必ず意識する。

- **認可**: 管理画面は `security.yaml` の `admin` ファイアウォール（`^/%eccube_admin_route%/`）で保護される。
  新規の管理アクションは **必ず `%eccube_admin_route%` プレフィックス配下のパス**に置くこと
  （例: `#[Route(path: '/%eccube_admin_route%/product/...', ...)]`）。配下に置けば認証が要求される。
  EC-CUBE コアは個別の `#[IsGranted]` 属性ではなく、ファイアウォール＋ロールで制御している点に注意。
- **CSRF**: **GET 以外の状態変更（更新・削除・Ajax 等）はトークンを検証する**。
  - フォーム経由（`$form->handleRequest()` ＋ `isValid()`）は CSRF 保護込み（[`formtype.md`](./formtype.md) 参照）。
  - **フォームを介さない削除・Ajax アクションは、基底クラスの `$this->isTokenValid()` を明示的に呼ぶ**。
    検証失敗時は `BadRequestHttpException` を投げる（コアの定石）。

```php
// 削除（フォームを介さない）: methods は GET 以外にし、トークンを検証する
#[Route(path: '/%eccube_admin_route%/example/{id}/delete', name: 'example_delete', methods: ['DELETE'])]
public function delete(Request $request, Example $Example): RedirectResponse
{
    $this->isTokenValid();   // ← CSRF トークン検証（コアの定石）

    $this->exampleService->delete($Example);
    $this->addSuccess('削除しました', 'admin');

    return $this->redirectToRoute('example_list');
}
```

## コントローラに書いてはいけないこと（Service へ出す）

以下が混ざり始めたら **Fat 化のサイン**。Service（または Repository のメソッド）へ抽出する。

- **業務的な計算・判定ロジック**（金額・在庫・送料・ポイント等の算出）
  - 例: `CartController::index` の送料無料判定の `bccomp`/`bcsub` 計算群はサービス化候補。
- **エンティティの業務的な永続化**: コントローラ内での `$em->persist()` / `$em->flush()` の直書き
  （単純な1操作を除き、トランザクションを伴う業務操作は Service にまとめる）
- **複数 Repository をまたぐ処理**・外部 API 連携・メール送信・ファイル入出力
- **同じロジックの重複**（複数アクションにコピペされた処理 → Service の 1 メソッドへ）

## Fat 化の目安（新規・改修コード向け）

数値はあくまで「超えたら設計を見直す」サイン。機械的な失格条件ではない。

- 1 メソッド: **約 50 行**を超えたら分割・委譲を検討
- 1 クラス: アクションが増えすぎたら **コントローラ分割**（EC-CUBE は `Admin/Product/` のように機能単位で分割済み）
- コンストラクタ依存: **約 7 個**を超えたら責務過多のサイン（Service へ集約）
- 認知的複雑度が高い分岐の塊 → Service の private メソッドへ切り出し

## 自己チェック

実装・改修後に、変更したコントローラを簡易検査できる（依存追加なし・助言用）:

```bash
php tools/check-architecture.php --changed   # 変更された Controller/Service のみ
php tools/check-architecture.php src/Eccube/Controller/CartController.php
```

メソッド長・コンストラクタ依存数・`persist`/`flush` 直書きを報告する。
**CI を落とすためのものではなく、レビュー観点を可視化するための補助**。

## よくある間違い

- ❌ コントローラ内に金額/在庫/送料の計算ロジック → ✅ Service に移し、コントローラは結果を受け取るだけ
- ❌ アクション内で `$em->persist()`/`$em->flush()` を直書きして業務処理 → ✅ Service のメソッドに集約
- ❌ 複数アクションに同じ処理をコピペ → ✅ Service の 1 メソッドに共通化
- ❌ `@Route` アノテーション → ✅ `#[Route]` 属性
- ❌ 具象クラス型ヒントで密結合 → ✅ インターフェース型ヒント＋コンストラクタ DI
- ❌ 削除/Ajax 等の状態変更でトークン未検証 → ✅ `$this->isTokenValid()` を呼ぶ（GET 以外）
- ❌ 管理アクションを `%eccube_admin_route%` 配下以外に置く → ✅ admin ファイアウォール配下に置く
