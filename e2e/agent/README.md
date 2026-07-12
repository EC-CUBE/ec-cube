# Agentic Commerce E2E — PHP エージェントシミュレータ

本体 (ACP/UCP) ↔ [eccube-api4](https://github.com/EC-CUBE/eccube-api4) (OAuth2) ↔ sample-payment-plugin (決済ハンドラ) の結合を、
「AI エージェント役」のクライアントが **外部 HTTP** で検証するハーネス。CI からは
[`.github/workflows/agentic-commerce-e2e.yml`](../../.github/workflows/agentic-commerce-e2e.yml) (`workflow_dispatch`) が呼び出す。

## 構成

| ファイル | 役割 |
|---|---|
| `acp-checkout.php` | ACP discovery スモーク + checkout フロー (create→update→get→complete)。OAuth2 Bearer (api4) 認証 |
| `ucp-checkout.php` | UCP checkout フロー (create→get→update→complete)。RFC 9421 署名は CI では不要 (`ucp_checkout_enabled` ゲートのみ・api4 非依存) |

## 2 フェーズと skip ゲート

### `acp-checkout.php` (ACP)

1. **discovery スモーク** — 常に実行。`/.well-known/ucp` (常時公開) と `/.well-known/acp.json`
   (`acp_checkout_enabled` ゲート) の生存・形状 (秘密鍵非混入・`merchant_id` 非混入等) を検証。
   api4 / 決済ハンドラに非依存なので **day 1 から green**。
2. **checkout フロー** — `AGENT_E2E_TOKEN` がある時のみ。OAuth2 Bearer で ACP 5 エンドポイントを叩く。
   トークン発行は **eccube-api4#188 (client_credentials + `acp:`/`ucp:` scope)** と決済ハンドラの
   landing が前提。未 landing の間は `AGENT_E2E_TOKEN` が空のまま → フェーズ 1 のみ実行して正常終了。

### `ucp-checkout.php` (UCP)

1. **checkout セッション** — 常に実行。`ucp_checkout_enabled` ゲートのみで、UCP のインバウンド認証
   (RFC 9421 署名) は CI では `requireSignature=false` のため署名なしで通る (api4 非依存)。
   create → get → update を検証。
2. **complete シナリオ** — `AGENT_E2E_PAYMENT_READY=true` の時のみ。sample-payment の UCP 決済ハンドラ
   経由で成功 / escalation / 拒否の 3 シナリオを検証。

## ローカル実行

```bash
# 1. フラグを立て result cache pool を消す (BaseInfo は result cache されるため必須)
bin/console dbal:run-sql "UPDATE dtb_base_info SET acp_checkout_enabled = 1, ucp_checkout_enabled = 1"
bin/console cache:pool:clear --all

# 2. ビルトインサーバ起動 (EC-CUBE は public-dir="." のため codeception/router.php を router にする)
php -S 127.0.0.1:8000 codeception/router.php &

# 3. ACP discovery-only (トークン無し)
BASE_URL=http://127.0.0.1:8000 php e2e/agent/acp-checkout.php

# 3'. ACP checkout 込み (api4#188 + 決済ハンドラ landing 後)
BASE_URL=http://127.0.0.1:8000 AGENT_E2E_TOKEN=<bearer> php e2e/agent/acp-checkout.php

# 4. UCP checkout セッションのみ (署名なし・api4 非依存)
BASE_URL=http://127.0.0.1:8000 php e2e/agent/ucp-checkout.php

# 4'. UCP complete 込み (決済ハンドラ landing 後)
BASE_URL=http://127.0.0.1:8000 AGENT_E2E_PAYMENT_READY=true php e2e/agent/ucp-checkout.php
```

## env

| 変数 | 既定 | 説明 |
|---|---|---|
| `BASE_URL` | `http://127.0.0.1:8000` | 稼働中サーバ |
| `AGENT_E2E_TOKEN` | (空) | OAuth2 Bearer (ACP のみ)。空ならフェーズ 2 を skip |
| `AGENT_E2E_PAYMENT_READY` | `false` | `true` の時のみ complete (決済) を実行。ACP/UCP 共通 |
| `AGENT_E2E_ITEM_ID` | ACP=`1` / UCP=`2` | checkout で使う ProductClass id (UCP の `1` は visible=0 の規格) |

> **注意**: このブランチ (`feature/agentic-commerce-e2e`) は ACP/UCP/feed/共通基盤を 1 本に集約した
> **テスト専用の統合ブランチ**。各機能は個別 PR (#6802/#6815/#6825/#6837/#6843) で 4.4 へマージされる。
> E2E 成果物は 4.4 マージ後に専用 PR で移送する。
