# AgentCommerce — エージェントコマース基盤

AI エージェント（OpenAI 等）が EC-CUBE の商品を発見し購入できるようにするための基盤サービス群。
ACP（Agentic Commerce Protocol）/ UCP（Universal Commerce Protocol）の 2 系統に対し、
プロトコル非依存の中立表現を核に置いて共通処理を提供する。
チェックアウトは新規フローを作らず**通常購入と同一の shopping flow**（[PurchaseFlow](../PurchaseFlow/README.md)）を再利用する（#6777）。

- 📖 仕様（人間向け）: [README.html](./README.html)
- 📦 同梱リソースの出所: [`Resource/AgentCommerce/README.md`](../../Resource/AgentCommerce/README.md)
- 📚 プロトコル仕様: [ACP](https://github.com/agentic-commerce-protocol/agentic-commerce-protocol) / [UCP](https://github.com/Universal-Commerce-Protocol/ucp)

## サブドメイン

- [Catalog](./Catalog/README.html) — カタログの中立 DTO 化と ACP Feed / UCP Catalog 出力・検証
- [CheckoutSession](./CheckoutSession/README.html) — 見積〜確定（complete）の中立表現と状態機械
- [Discovery](./Discovery/README.html) — UCP discovery profile 組み立てと決済ハンドラ収集口
- [Fulfillment](./Fulfillment/README.html) — 配送方法・送料・支払方法の選択肢を中立表現へ
- [Idempotency](./Idempotency/README.html) — Idempotency-Key による二重実行防止（DB 一意制約）
- [Payment](./Payment/README.html) — 決済ハンドラレジストリと支払方法割当リゾルバ
- [Security](./Security/README.html) — OAuth2 検証・scope 照合・メッセージ署名・鍵ストア
- [Exception](./Exception/README.html) — プロトコル系エラーのコード分類と例外

## ルート直下の共通ユーティリティ

- `AgentCheckoutPurchaseFlowAdapter.php` — 中立 DTO → `Cart`/`Order` を構築し shopping flow で再計算（`buildOrder`/`prepare`/`commit`/`rollback`）
- `AddressMappingService.php` — 住所系エンティティを ACP/UCP 住所 DTO へ写す（国コード numeric→alpha-2、region=`Pref` 名）
- `MinorUnitConverter.php` — major-unit ⇄ minor-unit 変換（ISO 4217 権威データ・bcmath・負数対応）
- `StorefrontUrlResolver.php` — 店舗 URL（規約・プライバシー・注文 permalink）を絶対 URL で実行時生成
