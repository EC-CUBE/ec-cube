---
name: eccube-service
description: EC-CUBE 4.4 の Service を実装・改修するときの責務分離規約。「サービスを作って」「ロジックをサービスに切り出して」「このサービスを直して」「コントローラから業務処理を抽出して」などと言われたとき、または src/Eccube/Service・app/Customize/Service 配下を作成・編集するときに使用する。業務ロジックの受け皿を単一責任・HTTP非依存に保つための規約。
---

# Service 規約 — 業務ロジックの置き場所（EC-CUBE 4.4）

**対象**: `src/Eccube/Service/**/*.php`, `app/Customize/Service/**/*.php`
**前提**: Symfony 7.4 / PHP 8.2+

> 目的: コントローラから抽出した業務ロジックの受け皿。Service は「単一責任」を保ち、
> 上位層（Controller / HTTP）に依存しないことで、テスト容易性と再利用性を確保する。
> Skill `eccube-controller` と対で使う。

## Service の責務（ここに置くもの）

- **業務ロジック**: 税の算出、各種マスタを使った判定、ステータス遷移など。
  - 例: `TaxRuleService::getTax()` / `calcTax()` のような計算は Service に集約されている。
  - ただし**受注に関わる計算・検証・確定（在庫引当・採番・ポイント付与・送料/値引き）は PurchaseFlow へ**（後述）。
- **永続化を伴う業務操作**: `EntityManager` の `persist()` / `flush()` は **Service に置いてよい**（むしろここが正しい置き場所）。
- **複数 Repository をまたぐ処理**・外部 API 連携・メール送信・ファイル入出力。
- 同一処理の再利用（複数のコントローラ/コマンドから呼ばれる共通ロジック）。

```php
namespace Eccube\Service;

class ExampleService
{
    public function __construct(
        private readonly ExampleRepository $exampleRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function update(Example $Example): void
    {
        // 業務ロジックと永続化は Service に集約する
        $Example->recalculate();
        $this->entityManager->persist($Example);
        $this->entityManager->flush();
    }
}
```

## 受注処理は PurchaseFlow へ（EC-CUBE の核）

「業務ロジック = Service」は一般化しすぎ。**注文に関わる計算・検証・確定は、汎用 Service ではなく
`src/Eccube/Service/PurchaseFlow/` の PurchaseFlow パイプライン**に置く。これが EC-CUBE の受注処理の核心。

| 段階 | 担うコンポーネント | 例 |
|---|---|---|
| 明細の準備 | `ItemPreprocessor` / `ItemHolderPreprocessor` | 送料・手数料の計算 |
| 明細の検証 | `ItemValidator` / `ItemHolderValidator` | 在庫・販売制限・合計金額のチェック |
| 最終検証 | `ItemHolderPostValidator` | 全処理後の最終バリデーション |
| 購入確定 | `PurchaseProcessor` | **在庫引当・注文番号の採番・ポイント付与** |
| 値引き適用 | `DiscountProcessor` | 値引きの算出・適用 |

- **在庫引当・採番・ポイント付与・値引きを、コントローラや汎用 Service に直書きしない**。該当する Processor/Validator を拡張する。
- 設定は `app/config/eccube/packages/purchaseflow.yaml`。
- 受注に直接関わらない業務ロジック（商品管理・会員管理・メール送信等）は通常の Service に置いてよい。

## DB レコードとファイルを対で扱う Service

ページ・ブロック・メールテンプレート（`dtb_page` / `dtb_block` / `dtb_mail_template` と `app/template/**` の twig）のように
**DB レコードとファイルが対**になるものは、`src/Eccube/Service/Content/*ContentService` の形に揃える。
管理画面のコントローラと CLI（`eccube:page:*` 等）が**同じ Service を通る**ことで、検証・書き出し・キャッシュ削除が二重にならない。

- **検証は管理画面と同じ FormType を submit して行う**（`PageContentService::apply()` は `MainEditType` を組み立てて submit）。
  CLI 専用の検証を書かない。失敗は `ContentValidationException`（エラー一覧を持つ）で返す。
- **順序は DB → ファイル、しかもトランザクションの中**。`EntityManager::wrapInTransaction()` の中で `persist` / `flush` してから
  `dumpFile()` する。ファイルの書き出しに失敗したら例外でロールバックし、**レコードだけが残る状態を作らない**
  （残ると参照先の無いテンプレートとして画面が「Unable to find template」で落ちる）。権限を分離した構成では
  `app/template` への書き込みだけが失敗し得るので、この順序が効く。
- **削除は退避 → DB 削除 → 退避ファイルの削除**（`TemplateRemovalTrait::removeTemplatesAround()`）。ファイルを先に消すと DB 削除の失敗で
  レコードだけが残り、後に消すとファイル削除の失敗でファイルだけが残る。同じディレクトリへ `.removing-<乱数>` で rename して退避し、
  失敗時は戻す。
- **本文が現在の内容と同じならファイルを書き出さない**（`TemplateBodyTrait::shouldWriteTemplate()`）。`app/template/{theme}` は
  `src/Eccube/Resource/template/default` より twig の探索で優先されるため、内容が同じ写しを作ると upstream のテンプレート修正
  （脆弱性パッチを含む）が画面へ反映されなくなる。比較は FormType の `trim` に合わせて正規化する。
  ただしどこからも解決できないテンプレートは必ず書き出す。
- **本文を指定しない新規登録は配置先に既にあるテンプレートを初期値にする**（`readExistingTemplate()`）。Git でコミット済みのテンプレートから
  レコードを作れるようにするため。
- **I/O の失敗は `ContentWriteException::forWrite()` / `forRemove()` に包んで投げる**。握りつぶすと「保存できました」と出て反映されない。
- 結果は `ContentResult`（`status` = `Created` / `Updated` / `Unchanged` / `Removed`、書き出した・削除したパス、`--dry-run` 用の変更前後）で返す。
  `dryRun` を引数で受け、`true` なら DB もファイルも触らずに `ContentResult` だけ返す。
- キャッシュの削除は Service に書かず、呼び出し側（コマンドは `ContentCommandTrait::clearContentCache()`、管理画面は
  `CacheUtil::clearCache()`）に任せる。分離した構成では削除できないことがあり、その扱いは入口ごとに異なる（Skill `eccube-permission-lanes`）。

## やってはいけないこと

- **上位層（Controller）への依存**: Service が `Eccube\Controller\...` を `use` / 型ヒントするのは
  **レイヤ違反**。依存の向きは Controller → Service → Repository の一方向に保つ。
- **HTTP の知識を持ち込む**: `Request` / `Response` を引数で受け取る設計は避け、必要な値（プリミティブやエンティティ）だけを渡す。
  （`Session` 等の利用は EC-CUBE 既存実装にもあるが、新規では極力 HTTP 非依存に保つ）
- **God Service 化**: 1 つの Service に無関係な責務を詰め込まない。責務が増えたら分割する。

## Fat 化のシグナル（質的）

数値（メソッド行数・依存数）で線を引かない。
代わりに**単一責任が崩れる質的シグナル**で判断する:

- 1 つの Service に**無関係な責務**（例: 商品検索とメール送信）が同居している → 分割。
- メソッドが**複数の関心事**（取得・整形・永続化・通知）を一気に処理している → private メソッド／別 Service へ。
- トランザクション境界が曖昧。`flush()` をループ内で乱発している → まとめて `flush()`。

## ツールに委ねる（整形・変換）

整形・型・変換は散文で重複説明せず、ローカルでツールを実行して担保する:

```bash
vendor/bin/rector process --dry-run            # アノテ→PHP8属性、コンストラクタDI 等
vendor/bin/phpstan analyse src                 # 型宣言・静的解析（level 6）
vendor/bin/php-cs-fixer fix                     # PSR-12 整形・ライセンスヘッダ
```

> `.husky/pre-commit`（PR #6761）がマージされれば、これらが commit 時に自動実行される想定。

## よくある間違い

- ❌ Service が Controller を `use` する → ✅ 依存は一方向（Controller → Service）
- ❌ 1 つの Service に無関係な処理を寄せ集める → ✅ 単一責任で分割
- ❌ `Request` を Service に渡す → ✅ 必要な値だけを引数で渡す
- ❌ ループ内で毎回 `flush()` → ✅ まとめて `flush()`（トランザクション境界を意識）
- ❌ DB を commit してからテンプレートを `dumpFile()` する → ✅ トランザクションの中で DB → ファイルの順。失敗はロールバック
- ❌ テンプレートを無条件に `dumpFile()` する → ✅ 本文が同じなら書かない（`app/template` の写しが upstream の修正を隠す）
- ❌ 管理画面と CLI で検証や書き出しを別々に実装する → ✅ 同じ `*ContentService` を通し、検証は FormType の submit で行う

---

実装・改修後は、Skill `eccube-review-responsibility` で責務分離を点検すること。
