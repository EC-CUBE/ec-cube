---
name: eccube-csv
description: EC-CUBE 4.4 の CSV 入出力（CsvImportService・CsvExportService・CSV 定義）を実装・改修するときの規約。「CSVインポートを実装して」「CSVエクスポートを追加して」「商品/受注のCSV出力を作って」「CSVの項目を増やして」などと言われたとき、または src/Eccube/Service/Csv*Service・CSV 定義を作成・編集するときに使用する。
---

# CSV 入出力 規約（EC-CUBE 4.4）

**対象**: `src/Eccube/Service/CsvImportService.php`, `src/Eccube/Service/CsvExportService.php`,
CSV 項目定義（`dtb_csv` ＝ `Eccube\Entity\Csv` / `mtb_csv_type` ＝ `Eccube\Entity\Master\CsvType`），
および CSV 入出力を行う管理画面コントローラ（`src/Eccube/Controller/Admin/**`）。
**前提**: Symfony 7.4 / PHP 8.2+ / Doctrine ORM 3.x

> 目的: EC-CUBE の CSV 入出力は「**出力項目をマスタ（`dtb_csv`）で定義**し、`CsvExportService` が
> エンティティから値を引く」「**入力は `CsvImportService`（Iterator）で 1 行ずつ読む**」という二つの確立した仕組みに乗る。
> 自前で `fgetcsv` / `fputcsv` を書き散らさず、既存サービスとマスタ定義の枠組みに従う。

## 対象 / 前提

- **エクスポート**: `CsvExportService`（`Eccube\Service`）。`dtb_csv` の定義に従ってヘッダ・データ行を `php://output` へ流す。
  コントローラ側は `StreamedResponse` でラップして返す。
- **インポート**: `CsvImportService`（`Eccube\Service`）。`\SplFileObject` を包む `\Iterator`/`\SeekableIterator`/`\Countable`。
  1 行を連想配列（ヘッダ名 => 値）として返す。
- **項目定義**: 何のエンティティのどのカラムを CSV のどの列に出すかは `dtb_csv`（`Csv` エンティティ）で持つ。
  CSV 種別（商品・会員・受注・配送…）は `mtb_csv_type`（`CsvType` マスタ、`CSV_TYPE_*` 定数）で区別する。
- 文字コード・区切り文字は **コントローラやサービスにハードコードせず `EccubeConfig`（`eccube.yaml`）の設定値**を使う。

## 基本ルール

- **新規に CSV 入出力ロジックを書くときも、まず既存サービスに乗れないか確認する**。
  `fputcsv` / `fgetcsv` の直書きは `CsvExportService::fputcsv()` / `CsvImportService` で吸収されている。
- **出力項目はコードに埋め込まず `dtb_csv` 定義で表現する**。項目の追加・並び替え・有効無効は
  `Csv`（`field_name` / `reference_field_name` / `disp_name` / `sort_no` / `enabled`）で制御する。
- **文字コード・区切り文字は設定値を使う**（`src/Eccube/Service/CsvExportService.php` / `eccube.yaml`）:
  - 出力エンコーディング: `eccube_csv_export_encoding`（既定 `SJIS-win`）
  - 出力区切り文字: `eccube_csv_export_separator`（既定 `,`）
  - 出力日付フォーマット: `eccube_csv_export_date_format`（既定 `Y-m-d H:i:s`）
  - 複数データ（one-to-many）の区切り: `eccube_csv_export_multidata_separator`（既定 `,`）
  - 入力エンコーディング候補: `eccube_csv_import_encoding`、入力区切り/囲み: `eccube_csv_import_delimiter` / `eccube_csv_import_enclosure`
- **エクスポートは必ず `StreamedResponse`**。メモリに全件貯めず、ストリームへ逐次出力する
  （件数が膨大になり得るため）。レスポンスは `Content-Type: application/octet-stream` ＋ `Content-Disposition: attachment`。
- **ストアド項目の追加・拡張はイベントで行う**。コア改変ではなく、`EccubeEvents` の CSV エクスポートイベント
  （`ADMIN_*_CSV_EXPORT*`）を購読して `ExportCsvRow` に列を足す（後述）。
- データアクセス・業務ロジックの分担は Skill `eccube-service` / `eccube-repository` に従う（検索条件の組み立ては Repository の
  `getQueryBuilderBySearchData*()`、値のバインドは `setParameter()`）。

## 実装パターン

### エクスポート（コントローラ側）

`src/Eccube/Controller/Admin/Order/OrderController.php::exportCsv()` が定石。
`CsvExportService` を `StreamedResponse` のコールバック内で駆動する:

```php
protected function exportCsv(Request $request, int $csvTypeId, string $fileName): StreamedResponse
{
    set_time_limit(0);
    // 大量出力時は SQL Logger を無効化
    $this->entityManager->getConfiguration()->setSQLLogger();

    $response = new StreamedResponse();
    $response->setCallback(function () use ($request, $csvTypeId): void {
        // 1. CSV 種別で初期化（dtb_csv から有効・sort_no 順の定義を読み込む）
        $this->csvExportService->initCsvType($csvTypeId);

        // 2. 検索条件のクエリビルダを取得（Repository 由来）
        $qb = $this->csvExportService->getOrderQueryBuilder($request);

        // 3. ヘッダ行（dtb_csv.disp_name）
        $this->csvExportService->exportHeader();

        // 4. データ行（100 件ずつページングし em->clear() しながら出力）
        $this->csvExportService->setExportQueryBuilder($qb);
        $this->csvExportService->exportData(function ($entity, $csvService): void {
            $Csvs = $csvService->getCsvs();
            foreach ($entity->getOrderItems() as $OrderItem) {
                $ExportCsvRow = new ExportCsvRow();
                foreach ($Csvs as $Csv) {
                    // getData() が「定義エンティティと一致するか」を判定して値を返す
                    $ExportCsvRow->setData($csvService->getData($Csv, $entity));
                    if ($ExportCsvRow->isDataNull()) {
                        $ExportCsvRow->setData($csvService->getData($Csv, $OrderItem));
                    }
                    // ...（必要なら Shipping 等もフォールバック探索）
                    $ExportCsvRow->pushData();
                }
                $csvService->fputcsv($ExportCsvRow->getRow());
            }
        });
    });

    $response->headers->set('Content-Type', 'application/octet-stream');
    $response->headers->set('Content-Disposition', 'attachment; filename='.$fileName);

    return $response;
}
```

ポイント:
- `initCsvType()` は `dtb_csv` を `enabled = true` かつ `sort_no ASC` で読む（`CsvExportService::initCsvType()`）。
- `getData(Csv $Csv, AbstractEntity $entity)` が値の取り出しを一手に担う:
  - `Csv::getEntityName()` と実エンティティのクラスが一致しなければ `null`（複数エンティティを順に当てて探す前提）。
  - one-to-one は `reference_field_name` の値、one-to-many は `eccube_csv_export_multidata_separator` で連結、
    `\DateTime` は `eccube_csv_export_date_format`、bool は `'1'`/`'0'` に変換。
- `fputcsv()` は `getConvertEncodingCallback()` を通して **UTF-8 → 出力エンコーディング**へ変換してから書き出す。

### インポート（コントローラ側）

`AbstractCsvImportController`（`src/Eccube/Controller/Admin/AbstractCsvImportController.php`）を継承し、
`getImportData()` で `CsvImportService` を得る。これが定石（商品/会員/受注インポート各コントローラが踏襲）:

```php
$data = $this->getImportData($formFile); // CsvImportService|false
if ($data === false) {
    $this->addErrors(trans('admin.common.csv_invalid_format'));
    return $this->renderWithError($form, $headers, false);
}

// 必須ヘッダの充足チェック
$columnHeaders = $data->getColumnHeaders();
if (count(array_diff($requireHeader, $columnHeaders)) > 0) { /* エラー */ }
if (count($data) < 1) { /* データ無しエラー */ }

$this->entityManager->getConnection()->beginTransaction();
try {
    foreach ($data as $row) {           // $row はヘッダ名 => 値 の連想配列
        $line = $data->key() + 1;        // 行番号
        if ($headerSize != count($row)) { /* 列数不一致エラー */ }
        // ... エンティティへマッピングし persist
    }
    if ($this->hasErrors()) {            // 途中で addErrors されていたら
        $this->entityManager->getConnection()->rollback();
    } else {
        $this->entityManager->flush();
        $this->entityManager->getConnection()->commit();
    }
} finally {
    $this->removeUploadedFile();         // 一時ファイルを必ず削除
}
```

ポイント（`AbstractCsvImportController` 由来）:
- `getImportData()` がアップロードファイルを `eccube_csv_temp_realdir` に退避し、
  `eccube_csv_import_delimiter` / `eccube_csv_import_enclosure` を使って `CsvImportService` を生成、`setHeaderRowNumber(0)` する。
- `CsvImportService` は **先頭行を見て UTF-8 でなければ SJIS-win → UTF-8 の stream filter を自動適用**する
  （`SjisToUtf8EncodingFilter` / `ConvertLineFeedFilter`）。エンコーディング判定を自前で書かない。
- `count($data)` で行数、`$data->key()` で現在行番号、`foreach` で 1 行ずつ取得（メモリに全展開しない）。
- 取り込みは**トランザクションで囲み、エラー時は rollback**。終了時に一時ファイルを削除する。

### CSV 項目を増やす（プラグイン / カスタマイズ）

- **管理画面で増やせる出力項目**は `dtb_csv` のレコード追加（CSV 設定画面）で完結する。コード変更は不要。
- **コードで列を足したい**場合は、コア改変ではなく **CSV エクスポートイベントを購読**する。
  `EccubeEvents` に種別ごとのイベントがある:
  - `ADMIN_ORDER_CSV_EXPORT_ORDER`（受注・出荷 CSV 共通。`OrderController::exportCsv()` は受注/出荷どちらのエクスポートでもこの1イベントを dispatch する）
  - `ADMIN_PRODUCT_CSV_EXPORT` / `ADMIN_CUSTOMER_CSV_EXPORT`
  - `ADMIN_PRODUCT_CATEGORY_CSV_EXPORT` / `ADMIN_PRODUCT_CLASS_NAME_CSV_EXPORT` / `ADMIN_PRODUCT_CLASS_CATEGORY_CSV_EXPORT`
  購読側で `EventArgs` から `ExportCsvRow` を受け取り、`setData()` / `pushData()` で列を追加する
  （`OrderController::exportCsv()` のイベント dispatch 箇所を参照）。イベント実装の作法は Skill `eccube-event-subscriber`。
- 新しいエンティティに紐づくマスタ種別を足すなら `mtb_csv_type` への INSERT（**STI なので `discriminator_type` 必須**、Skill `eccube-migration`）。

## よくある間違い

- ❌ `fgetcsv` / `fputcsv` を直書きする → ✅ `CsvImportService` / `CsvExportService::fputcsv()` に乗る。直書きなら escape まで明示（省略は PHP 8.4 で非推奨）
- ❌ 文字コード・区切り文字をハードコードする → ✅ `EccubeConfig` の `eccube_csv_export_*` / `eccube_csv_import_*` を使う
- ❌ エクスポートで全件を配列に貯めて一括出力する → ✅ `StreamedResponse` ＋ `exportData()` のページング（100 件ずつ `em->clear()`）で逐次出力
- ❌ 出力項目をコントローラに `if` で羅列する → ✅ `dtb_csv` 定義（`field_name` / `sort_no` / `enabled`）で表現し `getData()` に引かせる
- ❌ インポートで自前エンコーディング判定や全行読み込みをする → ✅ `CsvImportService`（stream filter 自動適用・Iterator）に任せ 1 行ずつ処理
- ❌ インポートをトランザクション無しで `flush()`／一時ファイルを残す → ✅ `beginTransaction`〜`commit`/`rollback` で囲み `removeUploadedFile()` する
- ❌ 出力項目追加のためにコアの export 処理を改変する → ✅ `ADMIN_*_CSV_EXPORT*` イベントを購読して `ExportCsvRow` に列追加
- ❌ `mtb_csv_type` へ `discriminator_type` なしで INSERT する → ✅ STI なので必ず指定する（Skill `eccube-migration`）

## 実行・確認方法

- 実装後の整形・型・静的解析・テストは **AGENTS.md「開発コマンド」** に従って実行する
  （PHP-CS-Fixer / PHPStan level 6 / PHPUnit）。
- 関連レイヤの規約も参照: Skill `eccube-service`（サービス責務）/ `eccube-repository`（クエリビルダ）/
  `eccube-event-subscriber`（エクスポートイベント購読）/ `eccube-migration`（`mtb_csv_type` 追加）。
- 実装・改修後は Skill `eccube-review-responsibility` で責務分離・セキュリティを点検すること。
