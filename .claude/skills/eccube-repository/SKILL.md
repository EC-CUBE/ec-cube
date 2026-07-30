---
name: eccube-repository
description: EC-CUBE 4.4 の Doctrine リポジトリを実装・改修するときの規約。「リポジトリを作って」「検索メソッドを追加して」「クエリを書いて」「一覧の絞り込みを実装して」などと言われたとき、または src/Eccube/Repository・app/Customize/Repository 配下を作成・編集するときに使用する。
---

# Repository 規約（EC-CUBE 4.4）

**対象**: `src/Eccube/Repository/**/*.php`, `app/Customize/Repository/**/*.php`
**前提**: Doctrine ORM 3.x / Symfony 7.4

## 基本ルール

- 名前空間 `Eccube\Repository`。`Eccube\Repository\AbstractRepository<T>`（`ServiceEntityRepository` を継承）を継承する。
- コンストラクタで `parent::__construct($registry, X::class)` を呼ぶ。
- PHP ファイル先頭に EC-CUBE ライセンスヘッダ。型宣言を付ける（PHPStan level 6）。
- **責務はデータアクセスのみ**。業務ロジック（金額計算・状態遷移等）は Service へ（Skill `eccube-service`）。

```php
namespace Eccube\Repository;

use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Eccube\Entity\Example;

/**
 * @extends AbstractRepository<Example>
 */
class ExampleRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Example::class);
    }

    public function getQueryBuilderBySearchData(array $searchData): QueryBuilder
    {
        $qb = $this->createQueryBuilder('e');

        if (!empty($searchData['name'])) {
            // パラメータは必ずバインドする（SQL インジェクション対策）
            $qb->andWhere('e.name LIKE :name')
                ->setParameter('name', '%'.$searchData['name'].'%');
        }

        return $qb->orderBy('e.id', 'DESC');
    }
}
```

## クエリ

- **`createQueryBuilder()` / DQL を使う**。生 SQL の文字列連結は避ける。
- 検索条件の値は **必ず `setParameter()` でバインド**する（SQL インジェクション対策）。
- 一覧の検索メソッドは `getQueryBuilderBySearchData(array $searchData): QueryBuilder` の形が定石。
  管理画面用は `getQueryBuilderBySearchDataForAdmin()` のように分ける。
- N+1 を避けるため、必要に応じて `addSelect()` / JOIN で関連を取得する。

## 永続化

- 保存・削除は `AbstractRepository` の `save(AbstractEntity $entity)` / `delete(AbstractEntity $entity)` を使う。
- メソッドをオーバーライドする場合は親クラスのシグネチャを厳守する。

## ページネーション

- 一覧のページングは Repository では QueryBuilder を返すに留め、ページネータ
  （`knp_paginator`）はコントローラ/サービス側で適用する。

## よくある間違い

- ❌ 生 SQL の文字列連結・値の直挿し → ✅ QueryBuilder ＋ `setParameter()` バインド
- ❌ Repository に業務ロジックを書く → ✅ データアクセスに徹し、ロジックは Service
- ❌ `ServiceEntityRepository` を直接継承 → ✅ `AbstractRepository<T>` を継承
- ❌ オーバーライドで親と異なるシグネチャ → ✅ 親シグネチャを厳守
- ❌ 画面表示の一覧・関連取得を無制限に全件取得（件数が際限なく増え得る）→ ✅ ページング（Paginator 用に QueryBuilder を返す）か上限を設ける
