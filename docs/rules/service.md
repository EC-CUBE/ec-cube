# Service 規約 — 業務ロジックの置き場所（EC-CUBE 4.4）

**対象**: `src/Eccube/Service/**/*.php`, `app/Customize/Service/**/*.php`
**前提**: Symfony 7.4 / PHP 8.2+

> 目的: コントローラから抽出した業務ロジックの受け皿。Service は「単一責任」を保ち、
> 上位層（Controller / HTTP）に依存しないことで、テスト容易性と再利用性を確保する。
> [`controller.md`](./controller.md) と対で使う。

## Service の責務（ここに置くもの）

- **業務ロジック**: 金額・在庫・送料・ポイント・税の算出、ステータス遷移などの判定。
  - 例: `TaxRuleService::getTax()` / `calcTax()` のような計算は Service に集約されている。
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

## やってはいけないこと

- **上位層（Controller）への依存**: Service が `Eccube\Controller\...` を `use` / 型ヒントするのは
  **レイヤ違反**。依存の向きは Controller → Service → Repository の一方向に保つ。
- **HTTP の知識を持ち込む**: `Request` / `Response` を引数で受け取る設計は避け、必要な値（プリミティブやエンティティ）だけを渡す。
  （`Session` 等の利用は EC-CUBE 既存実装にもあるが、新規では極力 HTTP 非依存に保つ）
- **God Service 化**: 1 つの Service に無関係な責務を詰め込まない。責務が増えたら分割する。

## Fat 化の目安（新規・改修コード向け）

機械的な失格条件ではなく、超えたら設計を見直すサイン。

- 1 メソッド: 約 50 行を超えたら private メソッドへ分割、または別 Service へ。
- コンストラクタ依存: 約 7 個を超えたら責務過多のサイン（Service の分割を検討）。
- トランザクション境界は最小限に。`flush()` の乱発（ループ内など）に注意。

## 自己チェック

```bash
php tools/check-architecture.php --changed   # 変更された Controller/Service を検査
php tools/check-architecture.php src/Eccube/Service/CartService.php
```

メソッド長・コンストラクタ依存数・Controller への依存（レイヤ違反）を報告する。
**CI を落とすためのものではなく、レビュー観点を可視化するための補助**。

## よくある間違い

- ❌ Service が Controller を `use` する → ✅ 依存は一方向（Controller → Service）
- ❌ 1 つの Service に無関係な処理を寄せ集める → ✅ 単一責任で分割
- ❌ `Request` を Service に渡す → ✅ 必要な値だけを引数で渡す
- ❌ ループ内で毎回 `flush()` → ✅ まとめて `flush()`（トランザクション境界を意識）
