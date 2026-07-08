# AgentCommerce Catalog — カタログ提供

EC-CUBE の `Product` / `ProductClass` をプロトコル非依存の中立 DTO に写し、
ACP Feed（push 型）と UCP Catalog（POST RPC）の両方へ出力するサブドメイン。
親 `Product` = `AgentCatalogItemDto` / variant `ProductClass` = `AgentCatalogVariantDto`。

- 📖 仕様（人間向け）: [README.html](./README.html)
- ⬆ 親: [AgentCommerce README](../README.md)
- 📦 同梱リソースの出所: [`Resource/AgentCommerce/README.md`](../../../Resource/AgentCommerce/README.md)

## 主要ファイル

- `CatalogMapper.php` — `Product`/`ProductClass` → 中立 DTO への写像（出力対象・availability 判定の中核）
- `CatalogProvider.php`（`CatalogProviderInterface.php`）— 公開中 `Product` を逐次供給（大規模カタログ対応）
- `ProductReferenceResolver.php`（`ProductReferenceResolverInterface.php`）— sku / product_class_id / barcode を `ProductClass` へ解決
- `AgentCatalog*Dto.php` / `AvailabilityStatus.php` — プロトコル非依存の中立 DTO・enum
- `Acp/` — ACP Feed の生成（`AcpFeedGenerator`）/ シリアライズ / 検証（`AcpFeedValidator`）/ push（`AcpFeedClient`）
- `Ucp/` — UCP Catalog のレスポンス組み立て（`UcpCatalogResponseBuilder`）/ シリアライズ / FS キャッシュ
- `Exception/` — ACP Feed 用例外（`AcpFeedException` / `AcpFeedTransportException` / `AcpFeedValidationException`）
