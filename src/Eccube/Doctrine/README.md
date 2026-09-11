# Doctrine — EC-CUBE 固有の Doctrine 拡張

エンティティ定義そのものではなく、Doctrine ORM / DBAL に被せた EC-CUBE 独自の基盤拡張。
クエリの後付けカスタマイズ機構、日時の UTC 保存型、日本語検索用の DQL 関数、
表示制御用 SQL フィルタ、エンティティプロキシ用マッピングドライバ、
CSV からの初期データ投入、ライフサイクルフックを収める。

- 📖 仕様（人間向け）: [README.html](./README.html)

## 主要ファイル

- `Query/QueryCustomizer.php` / `Query/Queries.php` — 既存クエリに WHERE/JOIN/ORDER BY を後付けする機構。`WhereClause` / `JoinClause` で句を組み立て、DI タグ `eccube.query_customizer` で登録
- `DBAL/Types/UTCDateTimeType.php` — DB へは UTC 保存、PHP 側でアプリ TZ へ変換するカスタム日時型（`doctrine.yaml` の `types` で差し替え）
- `ORM/Query/Normalize.php` / `ORM/Query/Extract.php` — 日本語あいまい検索・日時抽出の独自 DQL 関数（`NORMALIZE` / `EXTRACT`）
- `Filter/OrderStatusFilter.php` / `Filter/NoStockHiddenFilter.php` — 仮受注除外・在庫なし非表示の SQL フィルタ（既定は無効、必要な経路で有効化）
- `EventSubscriber/SaveEventSubscriber.php` — prePersist/preUpdate で作成・更新日時や creator を自動セット

> クエリに条件を足すときは Repository を直接書き換えず `QueryCustomizer` を登録して差し込む（アップグレード安全）。
> SQL フィルタは既定で無効なので、必要な箇所で `enableFilter()` を明示的に呼ぶ点に注意。
