---
name: repository
description: EC-CUBE 4.4 の Doctrine リポジトリを実装・改修するときの規約。「リポジトリを作って」「検索メソッドを追加して」「クエリを書いて」「一覧の絞り込みを実装して」などと言われたとき、または src/Eccube/Repository・app/Customize/Repository 配下を作成・編集するときに使用する。
---

# EC-CUBE 4.4 Repository 実装規約

リポジトリを実装・改修する際は、必ず次のドキュメントを読み込み、その規約に従うこと。

**規約本文: [`docs/rules/repository.md`](../../../docs/rules/repository.md)**

要点（詳細は本文参照）:
- `Eccube\Repository\AbstractRepository<T>` を継承し、コンストラクタで `parent::__construct($registry, X::class)`。
- クエリは **`createQueryBuilder()` / DQL**。生 SQL 連結は避け、値は **必ず `setParameter()` でバインド**（SQLインジェクション対策）。
- 検索は `getQueryBuilderBySearchData(array): QueryBuilder` の形。管理画面用は `...ForAdmin()` で分ける。
- 保存/削除は `AbstractRepository` の `save()` / `delete()`。オーバーライドは親シグネチャ厳守。
- **責務はデータアクセスのみ**。業務ロジックは Service へ（[`docs/rules/service.md`](../../../docs/rules/service.md)）。
- ライセンスヘッダ・型宣言必須。
