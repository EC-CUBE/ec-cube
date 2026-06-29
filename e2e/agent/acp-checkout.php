#!/usr/bin/env php
<?php
/**
 * ACP (Agentic Commerce Protocol) エージェントシミュレータ.
 *
 * 稼働中の EC-CUBE に対し「AI エージェント役」として HTTP で discovery / checkout を叩き、
 * 本体 ↔ eccube-api4 (OAuth2) ↔ sample-payment-plugin (決済ハンドラ) の結合を検証する。
 * WebTestCase の kernel 内呼び出しではなく、外部 HTTP による真の E2E。
 *
 * 2 フェーズ構成:
 *   1. discovery スモーク   … 常に実行 (api4/決済ハンドラ非依存)。/.well-known/{ucp,acp.json} の生存と形状。
 *   2. checkout フロー      … AGENT_E2E_TOKEN がある時のみ。create→update→get→complete。
 *
 * AGENT_E2E_TOKEN が空 (api4#188 の client_credentials/scope 未 landing) のときは
 * フェーズ 1 のみ実行して正常終了する (= CI を赤にしない skip ゲート)。
 *
 * env:
 *   BASE_URL          … 稼働中サーバ (既定 http://127.0.0.1:8000)
 *   AGENT_E2E_TOKEN   … OAuth2 Bearer トークン (acp:checkout)。空ならフェーズ 2 を skip
 *   AGENT_E2E_ITEM_ID … checkout で使う ProductClass id (既定 1)
 *
 * Usage: php e2e/agent/acp-checkout.php
 */

require_once __DIR__.'/../../vendor/autoload.php';

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

$baseUrl = rtrim((string) (getenv('BASE_URL') ?: 'http://127.0.0.1:8000'), '/');
$token = (string) (getenv('AGENT_E2E_TOKEN') ?: '');
$itemId = (int) (getenv('AGENT_E2E_ITEM_ID') ?: '1');
// complete (決済実行) は決済ハンドラ (#3) が要るため、明示的に許可された時のみ実行する。
$paymentReady = 'true' === (string) getenv('AGENT_E2E_PAYMENT_READY');

$client = HttpClient::create(['base_uri' => $baseUrl.'/', 'timeout' => 30]);

$passed = 0;

/** 成功を表示してカウント。 */
function ok(string $msg): void
{
    global $passed;
    ++$passed;
    fwrite(STDOUT, "  \033[32m✓\033[0m {$msg}\n");
}

/** 失敗を表示して非ゼロ終了 (どの要件で落ちたかをログだけで追えるようにする)。 */
function fail(string $msg): never
{
    fwrite(STDERR, "  \033[31m✗ FAIL:\033[0m {$msg}\n");
    exit(1);
}

function assertTrue(bool $cond, string $msg): void
{
    $cond ? ok($msg) : fail($msg);
}

function section(string $title): void
{
    fwrite(STDOUT, "\n\033[1m{$title}\033[0m\n");
}

// ─────────────────────────────────────────────────────────────────────────
// フェーズ 1: discovery スモーク (常時実行)
// ─────────────────────────────────────────────────────────────────────────
section('Phase 1: discovery smoke');

// UCP discovery (/.well-known/ucp・常時公開・RFC 8615)
try {
    $res = $client->request('GET', '.well-known/ucp');
    assertTrue(200 === $res->getStatusCode(), 'GET /.well-known/ucp returns 200');
    $ucp = $res->toArray();
    assertTrue(isset($ucp['ucp']['version']), 'UCP profile has ucp.version (YYYY-MM-DD)');
    assertTrue(isset($ucp['ucp']['capabilities']), 'UCP profile declares ucp.capabilities');
    // signing_keys は公開鍵限定 (秘密鍵パラメータ "d" を含まない・MUST)
    $hasPrivate = false;
    foreach ($ucp['signing_keys'] ?? [] as $jwk) {
        if (isset($jwk['d'])) {
            $hasPrivate = true;
            break;
        }
    }
    assertTrue(!$hasPrivate, 'UCP signing_keys[] contains no private key parameter "d"');
} catch (\Throwable $e) {
    fail('UCP discovery: '.$e->getMessage());
}

// ACP discovery (/.well-known/acp.json・acp_checkout_enabled ゲート)
try {
    $res = $client->request('GET', '.well-known/acp.json');
    assertTrue(200 === $res->getStatusCode(), 'GET /.well-known/acp.json returns 200');
    $acp = $res->toArray();
    assertTrue(($acp['protocol']['name'] ?? null) === 'acp', 'ACP discovery protocol.name == "acp"');
    // merchant_id は MUST NOT (混入していないこと)
    assertTrue(!containsKey($acp, 'merchant_id'), 'ACP discovery MUST NOT contain merchant_id');
} catch (\Throwable $e) {
    fail('ACP discovery: '.$e->getMessage());
}

// ─────────────────────────────────────────────────────────────────────────
// フェーズ 2: checkout フロー (AGENT_E2E_TOKEN がある時のみ)
// ─────────────────────────────────────────────────────────────────────────
if ('' === $token) {
    fwrite(STDOUT, "\n\033[33m⏸ Phase 2 (checkout) skipped:\033[0m AGENT_E2E_TOKEN 未設定 (api4#188 + 決済ハンドラ landing 待ち)\n");
    fwrite(STDOUT, "\n\033[32mPASS\033[0m ({$passed} assertions, discovery-only)\n");
    exit(0);
}

section('Phase 2: ACP checkout flow (OAuth2 authenticated)');
runCheckout($client, $token, $itemId, $paymentReady);

$mode = $paymentReady ? 'auth + session + complete' : 'auth + session (complete は #3 待ち)';
fwrite(STDOUT, "\n\033[32mPASS\033[0m ({$passed} assertions, {$mode})\n");
exit(0);

// ─────────────────────────────────────────────────────────────────────────
// helpers
// ─────────────────────────────────────────────────────────────────────────

/** 連想配列を再帰探索し、指定キーが存在するかを判定する。 */
function containsKey(mixed $data, string $key): bool
{
    if (!is_array($data)) {
        return false;
    }
    if (array_key_exists($key, $data)) {
        return true;
    }
    foreach ($data as $v) {
        if (containsKey($v, $key)) {
            return true;
        }
    }

    return false;
}

/**
 * ACP checkout 5 エンドポイントを順に叩く (create→update→get→complete)。
 *
 * TODO(api4#188 + 決済ハンドラ): トークン発行が有効化されたら payload を
 * {@link \Eccube\Service\AgentCommerce\Acp\AcpCheckoutSessionMapper} の契約に対して
 * 実データで突き合わせる (本関数の payload は ACP 2026-04-17 spec ベースの暫定形)。
 */
function runCheckout(HttpClientInterface $client, string $token, int $itemId, bool $paymentReady): void
{
    $auth = ['Authorization' => 'Bearer '.$token, 'Content-Type' => 'application/json'];
    $idempotencyKey = bin2hex(random_bytes(16));

    // 1. create (POST /acp/checkout_sessions)
    $createBody = [
        'line_items' => [['id' => (string) $itemId, 'quantity' => 1]],
        'fulfillment_details' => [
            'name' => ['first_name' => '太郎', 'last_name' => '山田'],
            'contact' => ['email' => 'agent-e2e@example.com', 'phone' => '0312345678'],
            'address' => [
                'line_one' => '1-1-1',
                'city' => '千代田区',
                'state' => '東京都',
                'postal_code' => '1000001',
                'country' => 'JP',
            ],
        ],
    ];
    $res = $client->request('POST', 'acp/checkout_sessions', [
        'headers' => $auth + ['Idempotency-Key' => $idempotencyKey],
        'json' => $createBody,
    ]);
    assertTrue(in_array($res->getStatusCode(), [200, 201], true), 'POST /acp/checkout_sessions returns 200/201');
    $session = $res->toArray(false);
    $sessionId = $session['id'] ?? null;
    assertTrue(is_string($sessionId) && '' !== $sessionId, 'create response has session id');
    // 金額は minor unit 整数 (JPY はゼロデシマル)
    foreach ($session['totals'] ?? [] as $total) {
        assertTrue(is_int($total['amount'] ?? null), "totals[].amount is minor-unit integer ({$total['type']})");
    }

    // 2. update (POST /acp/checkout_sessions/{id}) — 冪等キーは新規
    $res = $client->request('POST', 'acp/checkout_sessions/'.$sessionId, [
        'headers' => $auth + ['Idempotency-Key' => bin2hex(random_bytes(16))],
        'json' => $createBody,
    ]);
    assertTrue(200 === $res->getStatusCode(), 'POST /acp/checkout_sessions/{id} (update) returns 200');

    // 3. get (GET /acp/checkout_sessions/{id})
    $res = $client->request('GET', 'acp/checkout_sessions/'.$sessionId, ['headers' => $auth]);
    assertTrue(200 === $res->getStatusCode(), 'GET /acp/checkout_sessions/{id} returns 200');
    assertTrue(($res->toArray(false)['id'] ?? null) === $sessionId, 'get returns the same session id');

    // 4. complete (POST /acp/checkout_sessions/{id}/complete)
    // 決済実行は sample-payment 決済ハンドラ (#3) が前提のため、許可された時のみ実行する。
    // 各シナリオは状態が確定するため新規セッションで実行する。
    if (!$paymentReady) {
        fwrite(STDOUT, "  \033[33m⏸\033[0m complete skipped: AGENT_E2E_PAYMENT_READY!=true (#3 決済ハンドラ待ち)\n");

        return;
    }

    section('Phase 2b: ACP complete scenarios (sample-payment ハンドラ経由)');

    // (a) 正常系: handler_id 駆動で sample CreditCard ハンドラが与信→売上→確定する。
    $sid = createSession($client, $auth, $createBody);
    $body = completeAcp($client, $auth, $sid, ['handler_id' => 'card_tokenized', 'token' => 'e2e-acp-spt-ok', 'provider' => 'sample_payment']);
    assertTrue(($body['status'] ?? null) === 'completed', 'complete (ok token) => status "completed"');

    // (b) 3DS: 中断 (authentication_required) → authentication_result 付きで再開 → completed。
    //     silent passthrough (ハンドラ未通過) なら 3DS は発生しないため、これはハンドラ実行の証跡。
    $sid = createSession($client, $auth, $createBody);
    $body = completeAcp($client, $auth, $sid, ['handler_id' => 'card_tokenized', 'token' => 'e2e-acp-spt-3ds', 'provider' => 'sample_payment']);
    assertTrue(($body['status'] ?? null) === 'authentication_required', 'complete (3ds token) => "authentication_required" (handler ran)');
    $body = completeAcp($client, $auth, $sid, ['handler_id' => 'card_tokenized', 'token' => 'e2e-acp-spt-3ds', 'provider' => 'sample_payment', 'authentication_result' => ['outcome' => 'authenticated']]);
    assertTrue(($body['status'] ?? null) === 'completed', 'complete (3ds resume w/ authentication_result) => "completed"');

    // (c) 拒否: ハンドラが FAILED を返し、注文は確定しない (ready_for_payment) + messages[]。
    //     handler_id が無視され既定 Payment ですり抜けると completed になるため、この負ケースが seam を守る。
    $sid = createSession($client, $auth, $createBody);
    $body = completeAcp($client, $auth, $sid, ['handler_id' => 'card_tokenized', 'token' => 'e2e-acp-spt-decline', 'provider' => 'sample_payment']);
    assertTrue(($body['status'] ?? null) !== 'completed', 'complete (decline token) => not "completed" (handler rejected)');
    assertTrue(!empty($body['messages']), 'complete (decline token) => business messages[] present');
}

/** complete シナリオ用に新規セッションを作成し session id を返す。 */
function createSession(HttpClientInterface $client, array $auth, array $createBody): string
{
    $res = $client->request('POST', 'acp/checkout_sessions', [
        'headers' => $auth + ['Idempotency-Key' => bin2hex(random_bytes(16))],
        'json' => $createBody,
    ]);
    assertTrue(in_array($res->getStatusCode(), [200, 201], true), 'scenario: create checkout session');
    $sid = $res->toArray(false)['id'] ?? null;
    assertTrue(is_string($sid) && '' !== $sid, 'scenario: created session has id');

    return $sid;
}

/**
 * complete を呼び、HTTP 200 (2 系統エラーは messages[]) を確認してレスポンス body を返す。
 *
 * @param array<string, mixed> $paymentData
 *
 * @return array<string, mixed>
 */
function completeAcp(HttpClientInterface $client, array $auth, string $sessionId, array $paymentData): array
{
    $res = $client->request('POST', 'acp/checkout_sessions/'.$sessionId.'/complete', [
        'headers' => $auth + ['Idempotency-Key' => bin2hex(random_bytes(16))],
        'json' => ['payment_data' => $paymentData],
    ]);
    $status = $res->getStatusCode();
    $raw = $res->getContent(false);
    if (200 !== $status) {
        // 失敗時は HTTP ステータスとレスポンス本体を出力して原因 (payment_handler_not_found / 500 等) を可視化する。
        fwrite(STDERR, "    \033[33m↳ complete HTTP {$status}\033[0m: ".substr($raw, 0, 1000)."\n");
    }
    assertTrue(200 === $status, 'complete returns HTTP 200 (business outcomes are in messages[])');

    return json_decode($raw, true) ?: [];
}
