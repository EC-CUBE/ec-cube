# Agent Commerce テスト (ACP / UCP)

`src/Eccube/Service/AgentCommerce` 配下 (Product Feed / Catalog / Discovery) のテストです。
ローカルでは EC-CUBE 既定の SQLite で実行できます。

```bash
# DB 不要の純ロジック/契約テスト + DB を使う Web テストを含め一括実行
vendor/bin/phpunit tests/Eccube/Tests/Service/AgentCommerce tests/Eccube/Tests/Web/AgentCommerce
```

## スキーマ契約テストと spec schema の解決

ACP / UCP の JSON Schema は**リポジトリに同梱しません** (Apache-2.0 ライセンスの露出を避けるため)。

### ACP feed schema

`AcpFeedSchemaContractTest` / `AcpFeedConformanceTest` は **製品に同梱された runtime リソース**
`src/Eccube/Resource/AgentCommerce/Acp/schema.feed.json` を使います (pre-push 検証と同一)。
追加取得は不要です。出所は [`src/Eccube/Resource/AgentCommerce/README.md`](../../../../../src/Eccube/Resource/AgentCommerce/README.md) を参照。

### UCP schema (公式リポジトリから取得)

`UcpCatalogSchemaContractTest` は UCP 公式リポジトリの `source/schemas` ツリーを参照します。
`SchemaValidatorTrait` が次の順で解決し、**いずれも無ければ `markTestSkipped`** します:

1. 環境変数 `ECCUBE_UCP_SCHEMA_DIR`
2. `var/agent-commerce-spec/ucp/source/schemas` (CI で clone・`var/` は gitignore)
3. `specifications/ucp/source/schemas` (ローカル開発クローン)

ローカルでこのテストを実行するには、いずれかを用意してください:

```bash
# 例: var/ 配下に公式リポジトリのリリースタグ v2026-04-08 を取得
git clone --filter=blob:none --branch v2026-04-08 --single-branch \
  https://github.com/Universal-Commerce-Protocol/ucp.git var/agent-commerce-spec/ucp

# または既存クローンを環境変数で指定
ECCUBE_UCP_SCHEMA_DIR=/path/to/ucp/source/schemas vendor/bin/phpunit tests/Eccube/Tests/Service/AgentCommerce/Schema
```

CI (`.github/workflows/unit-test.yml` / `coverage.yml`) はリリースタグ `v2026-04-08` を自動で clone します。

## ライセンス / 出所

- **ACP** schema: [agentic-commerce-protocol](https://github.com/agentic-commerce-protocol/agentic-commerce-protocol) `spec/2026-04-17` (Apache-2.0)
- **UCP** schema: [Universal-Commerce-Protocol/ucp](https://github.com/Universal-Commerce-Protocol/ucp) `v2026-04-08` 相当 (Apache-2.0)

UCP schema は取得物であり本リポジトリには含めません。ACP schema は runtime 検証に必要なため
`src/Eccube/Resource/AgentCommerce/Acp/` に同梱し、出所・ライセンスを明記しています。
