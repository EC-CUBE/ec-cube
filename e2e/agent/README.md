# Agentic Commerce E2E — PHP エージェントシミュレータ

本体 (ACP/UCP) ↔ [eccube-api4](https://github.com/EC-CUBE/eccube-api4) (OAuth2) ↔
[sample-payment-plugin](https://github.com/EC-CUBE/sample-payment-plugin) (決済ハンドラ) の結合を、
「AI エージェント役」のクライアントが **外部 HTTP** で検証するハーネス。CI からは
[`.github/workflows/agentic-commerce-e2e.yml`](../../.github/workflows/agentic-commerce-e2e.yml) が呼び出す
(`main.yml` からの `workflow_call` で push/PR 時に自動実行。`workflow_dispatch` では参照ブランチを手動指定できる)。

依存プラグインは両リポジトリの **`4.4` ブランチ**を参照する
([eccube-api4#191](https://github.com/EC-CUBE/eccube-api4/pull/191) /
[sample-payment-plugin#54](https://github.com/EC-CUBE/sample-payment-plugin/pull/54) がマージ済み)。
どちらもデフォルトブランチは 4.2 系 (composer 名が旧名の `ec-cube/api42` / `ec-cube/samplepayment42`) のため、
CI は `4.4` ブランチを clone して composer の **path リポジトリ**として取り込む。

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
   トークン発行は **eccube-api4 の client_credentials + `acp:`/`ucp:` scope** に依存する
   (#188 / #191 で 4.4 に landing 済み)。CI では `/token` から実トークンを発行するため常に実行される。
   ローカルで `AGENT_E2E_TOKEN` を省いた場合はフェーズ 1 のみ実行して正常終了する
   (discovery だけ確認したいときの意図的なゲート)。

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

# 2. ビルトインサーバ起動。EC-CUBE は public-dir="." のため front controller はルートの index.php。
#    codeception/router.php は GET に対し false を返し、静的フォールバック挙動が PHP バージョン依存で
#    404 になり得るため、index.php を直接 router にして確実に kernel へ通す (CI と同じ方針)。
php -S 127.0.0.1:8000 index.php &

# 3. ACP discovery-only (トークン無し)
BASE_URL=http://127.0.0.1:8000 php e2e/agent/acp-checkout.php

# 3'. ACP checkout 込み (Api44 で client_credentials トークンを発行して渡す)
BASE_URL=http://127.0.0.1:8000 AGENT_E2E_TOKEN=<bearer> php e2e/agent/acp-checkout.php

# 4. UCP checkout セッションのみ (署名なし・api4 非依存)
BASE_URL=http://127.0.0.1:8000 php e2e/agent/ucp-checkout.php

# 4'. UCP complete 込み (SamplePayment44 の UCP 決済ハンドラを使う)
BASE_URL=http://127.0.0.1:8000 AGENT_E2E_PAYMENT_READY=true php e2e/agent/ucp-checkout.php
```

## env

| 変数 | 既定 | 説明 |
|---|---|---|
| `BASE_URL` | `http://127.0.0.1:8000` | 稼働中サーバ |
| `AGENT_E2E_TOKEN` | (空) | OAuth2 Bearer (ACP のみ)。空ならフェーズ 2 を skip |
| `AGENT_E2E_PAYMENT_READY` | `false` | `true` の時のみ complete (決済) を実行。ACP/UCP 共通。CI は既定 `true` (`run_payment`) |
| `AGENT_E2E_ITEM_ID` | `2` (ACP/UCP 共通) | checkout で使う ProductClass id。`1` は fixtures の visible=0 なダミー規格で purchase flow が明細を除去するため使わない |

> **経緯**: エージェントコマースのコア実装は共通基盤 (#6802) / feed・catalog・discovery (#6815) /
> CheckoutSession 中核 (#6825) / UCP checkout (#6837) / ACP checkout (#6843) に分割して 4.4 へマージ済み。
> 本ハーネスと通常 CI への統合は #6872 で取り込まれた。
