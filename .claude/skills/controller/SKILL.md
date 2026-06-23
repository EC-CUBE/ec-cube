---
name: controller
description: EC-CUBE 4.4 のコントローラを実装・改修するときの責務分離規約。「コントローラを作って」「アクションを追加して」「このコントローラを直して」「ルーティングを追加して」などと言われたとき、または src/Eccube/Controller・app/Customize/Controller 配下を作成・編集するときに使用する。Fat コントローラを避け業務ロジックを Service へ寄せるための規約。
---

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
  - フォーム経由（`$form->handleRequest()` ＋ `isValid()`）は CSRF 保護込み（Skill `formtype` 参照）。
  - **フォームを介さない削除・Ajax アクションは、基底クラスの `$this->isTokenValid()` を明示的に呼ぶ**。
    `isTokenValid()` は検証失敗時に `AccessDeniedHttpException` を投げる（`AbstractController::isTokenValid()`）。

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

## Fat 化のシグナル（質的）

「1 メソッド何行」「依存いくつ」という**数値で線を引かない**。
「51 行だから分割／49 行だから OK」というカーゴカルト的判断を誘発するため、行数や依存数そのものを基準にしない。

代わりに、**Service / PurchaseFlow へ出すべき「質的なシグナル」**で判断する。次が混ざり始めたら抽出を検討:

- **業務的な計算・判定**（金額・在庫・送料・ポイント・税の算出）がアクション内にある。
- アクション内に `$em->persist()` / `$em->flush()` を**業務的に直書き**している。
- **複数 Repository をまたぐ処理**・外部 API 連携・メール送信・ファイル IO がアクションにある。
- **同一処理のコピペ重複**が複数アクションに散っている。
- 1 クラスにアクションが増えすぎた → 機能単位で**コントローラ分割**（EC-CUBE は `Admin/Product/` 等で分割済み）。

→ 受注に関わる計算・検証・確定（在庫引当・採番・ポイント付与・値引き）は Service ではなく
**PurchaseFlow パイプライン**へ（Skill `service` の「PurchaseFlow」参照）。

## ツールに委ねる（整形・変換）

整形・型・アノテーション変換は**散文で重複説明せず、ローカルでツールを実行**して担保する
（CI は最後の砦であって一次防衛線ではない）。実装・改修後に:

```bash
vendor/bin/rector process --dry-run            # @Route→#[Route]、アノテ→PHP8属性、コンストラクタDI 等
vendor/bin/phpstan analyse src                 # 型宣言・静的解析（level 6）
vendor/bin/php-cs-fixer fix                     # PSR-12 整形・ライセンスヘッダ
```

> `.husky/pre-commit`（PR #6761）がマージされれば、staged な `.php` を含む commit 時に rector(dry-run)→phpstan→php-cs-fixer が自動実行される想定。

## よくある間違い（責務・セキュリティの判断）

整形・変換系（`@Route`→`#[Route]` 等）は上記ツールが扱うのでここでは挙げない。**ツールでは判断できない**観点だけ:

- ❌ コントローラ内に金額/在庫/送料の計算ロジック → ✅ Service に移し、コントローラは結果を受け取るだけ
- ❌ アクション内で `$em->persist()`/`$em->flush()` を直書きして業務処理 → ✅ Service のメソッドに集約
- ❌ 複数アクションに同じ処理をコピペ → ✅ Service の 1 メソッドに共通化
- ❌ 具象クラス型ヒントで密結合 → ✅ インターフェース型ヒント＋コンストラクタ DI
- ❌ 削除/Ajax 等の状態変更でトークン未検証 → ✅ `$this->isTokenValid()` を呼ぶ（GET 以外）
- ❌ 管理アクションを `%eccube_admin_route%` 配下以外に置く → ✅ admin ファイアウォール配下に置く

---

実装・改修後は、Skill `review-responsibility` で責務分離を点検すること。
