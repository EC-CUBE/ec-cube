# Agent Commerce 同梱リソース

## `Acp/schema.feed.json`

ACP (Agentic Commerce Protocol) の Product Feed JSON Schema です。
`AcpFeedValidator` が **push 前の生成データ検証 (pre-push validation)** に runtime で使用します。
サーバ (OpenAI) 側の ack が粗い (`{accepted: bool}`) ため、加盟店側で送信前に
`products.jsonl` / `metadata.json` がスキーマ適合することを保証する目的の必須リソースです。

- 出所: [agentic-commerce-protocol](https://github.com/agentic-commerce-protocol/agentic-commerce-protocol)
  `spec/2026-04-17/json-schema/schema.feed.json`
- バージョン: ACP `2026-04-17`
- ライセンス: **Apache License 2.0** (原文ママ・無改変)

更新する場合は上記リポジトリの同名ファイルと同期し、バージョンを併記してください。
