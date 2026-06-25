# Agentic Commerce E2E — PHP エージェントシミュレータ

本体 (ACP/UCP) ↔ [eccube-api4](https://github.com/EC-CUBE/eccube-api4) (OAuth2) ↔ sample-payment-plugin (決済ハンドラ) の結合を、
「AI エージェント役」のクライアントが **外部 HTTP** で検証するハーネス。CI からは
[`.github/workflows/agentic-commerce-e2e.yml`](../../.github/workflows/agentic-commerce-e2e.yml) (`workflow_dispatch`) が呼び出す。

## 構成

| ファイル | 役割 |
|---|---|
| `acp-checkout.php` | ACP discovery スモーク + checkout フロー (create→update→get→complete) |

## 2 フェーズと skip ゲート

1. **discovery スモーク** — 常に実行。`/.well-known/ucp` (常時公開) と `/.well-known/acp.json`
   (`acp_checkout_enabled` ゲート) の生存・形状 (秘密鍵非混入・`merchant_id` 非混入等) を検証。
   api4 / 決済ハンドラに非依存なので **day 1 から green**。
2. **checkout フロー** — `AGENT_E2E_TOKEN` がある時のみ。OAuth2 Bearer で ACP 5 エンドポイントを叩く。
   トークン発行は **eccube-api4#188 (client_credentials + `acp:`/`ucp:` scope)** と決済ハンドラの
   landing が前提。未 landing の間は `AGENT_E2E_TOKEN` が空のまま → フェーズ 1 のみ実行して正常終了。

## ローカル実行

```bash
# 1. フラグを立て result cache pool を消す (BaseInfo は result cache されるため必須)
bin/console dbal:run-sql "UPDATE dtb_base_info SET acp_checkout_enabled = 1"
bin/console cache:pool:clear --all

# 2. ビルトインサーバ起動 (EC-CUBE は public-dir="." のため codeception/router.php を router にする)
php -S 127.0.0.1:8000 codeception/router.php &

# 3. discovery-only (トークン無し)
BASE_URL=http://127.0.0.1:8000 php e2e/agent/acp-checkout.php

# 3'. checkout 込み (api4#188 + 決済ハンドラ landing 後)
BASE_URL=http://127.0.0.1:8000 AGENT_E2E_TOKEN=<bearer> php e2e/agent/acp-checkout.php
```

## env

| 変数 | 既定 | 説明 |
|---|---|---|
| `BASE_URL` | `http://127.0.0.1:8000` | 稼働中サーバ |
| `AGENT_E2E_TOKEN` | (空) | OAuth2 Bearer。空ならフェーズ 2 を skip |
| `AGENT_E2E_ITEM_ID` | `1` | checkout で使う ProductClass id |

> **注意**: このブランチ (`feature/agentic-commerce-e2e`) は ACP/UCP/feed/共通基盤を 1 本に集約した
> **テスト専用の統合ブランチ**。各機能は個別 PR (#6802/#6815/#6825/#6837/#6843) で 4.4 へマージされる。
> E2E 成果物は 4.4 マージ後に専用 PR で移送する。
