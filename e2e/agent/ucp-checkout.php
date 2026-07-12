#!/usr/bin/env php
<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * UCP (Universal Commerce Protocol) エージェントシミュレータ.
 *
 * 稼働中の EC-CUBE に対し「AI エージェント役」として HTTP で UCP checkout を叩き、
 * 本体 ↔ sample-payment-plugin (UCP 決済ハンドラ) の結合を検証する。
 * UCP のインバウンド認証は RFC 9421 署名で、CI では `requireSignature=false` のため署名なしで通る
 * (ucp_checkout_enabled ゲートのみ)。OAuth2 (api4) には依存しない。
 *
 * 2 フェーズ構成:
 *   1. checkout セッション    … create → get → update (常時実行)。
 *   2. complete シナリオ      … AGENT_E2E_PAYMENT_READY=true のときのみ (成功 / 拒否 / escalation)。
 *
 * env:
 *   BASE_URL                … 稼働中サーバ (既定 http://127.0.0.1:8000)
 *   AGENT_E2E_ITEM_ID       … checkout で使う ProductClass id (既定 2。id=1 は visible=0 の規格)
 *   AGENT_E2E_PAYMENT_READY … complete を実行するか (既定 false)
 *
 * Usage: php e2e/agent/ucp-checkout.php
 */

require_once __DIR__.'/../../vendor/autoload.php';

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

$baseUrl = rtrim((string) (getenv('BASE_URL') ?: 'http://127.0.0.1:8000'), '/');
$itemId = (int) (getenv('AGENT_E2E_ITEM_ID') ?: '2');
$paymentReady = 'true' === (string) getenv('AGENT_E2E_PAYMENT_READY');

$client = HttpClient::create(['base_uri' => $baseUrl.'/', 'timeout' => 30]);

$passed = 0;

function ok(string $msg): void
{
    global $passed;
    ++$passed;
    fwrite(STDOUT, "  \033[32m✓\033[0m {$msg}\n");
}

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
// UCP checkout payload (ucp.dev v2026-04-08)
// ─────────────────────────────────────────────────────────────────────────
$createBody = [
    'currency' => 'JPY',
    'line_items' => [['item' => ['id' => (string) $itemId], 'quantity' => 1]],
    'buyer' => [
        'first_name' => '太郎',
        'last_name' => '山田',
        'email' => 'agent-ucp@example.com',
        'phone_number' => '0312345678',
    ],
    // 送料計算のため配送先 (postal destination) を与える。address_region は都道府県名で解決される。
    'fulfillment' => [
        'destinations' => [[
            'first_name' => '太郎',
            'last_name' => '山田',
            'postal_code' => '1000001',
            'address_region' => '東京都',
            'address_locality' => '千代田区',
            'street_address' => '1-1-1',
            'phone_number' => '0312345678',
        ]],
    ],
];

// ─────────────────────────────────────────────────────────────────────────
// フェーズ 1: checkout セッション (create → get → update)
// ─────────────────────────────────────────────────────────────────────────
section('Phase 1: UCP checkout session (RFC 9421 署名なし・ucp_checkout_enabled ゲート)');

$headers = ['Content-Type' => 'application/json'];

$res = $client->request('POST', 'ucp/checkout-sessions', [
    'headers' => $headers + ['Idempotency-Key' => bin2hex(random_bytes(16))],
    'json' => $createBody,
]);
assertTrue(in_array($res->getStatusCode(), [200, 201], true), 'POST /ucp/checkout-sessions returns 200/201');
$session = $res->toArray(false);
$sessionId = $session['id'] ?? null;
assertTrue(is_string($sessionId) && '' !== $sessionId, 'create response has session id');
assertTrue(isset($session['ucp']['version']), 'response wraps ucp.version');
foreach ($session['totals'] ?? [] as $total) {
    assertTrue(is_int($total['amount'] ?? null), "totals[].amount is minor-unit integer ({$total['type']})");
}

$res = $client->request('GET', 'ucp/checkout-sessions/'.$sessionId, ['headers' => $headers]);
assertTrue(200 === $res->getStatusCode(), 'GET /ucp/checkout-sessions/{id} returns 200');
assertTrue(($res->toArray(false)['id'] ?? null) === $sessionId, 'get returns the same session id');

$res = $client->request('PUT', 'ucp/checkout-sessions/'.$sessionId, [
    'headers' => $headers + ['Idempotency-Key' => bin2hex(random_bytes(16))],
    'json' => $createBody,
]);
assertTrue(200 === $res->getStatusCode(), 'PUT /ucp/checkout-sessions/{id} (update) returns 200');

// ─────────────────────────────────────────────────────────────────────────
// フェーズ 2: complete シナリオ (AGENT_E2E_PAYMENT_READY=true のみ)
// ─────────────────────────────────────────────────────────────────────────
if (!$paymentReady) {
    fwrite(STDOUT, "\n\033[33m⏸ complete skipped:\033[0m AGENT_E2E_PAYMENT_READY!=true\n");
    fwrite(STDOUT, "\n\033[32mPASS\033[0m ({$passed} assertions, session only)\n");
    exit(0);
}

section('Phase 2: UCP complete scenarios (sample-payment ハンドラ経由)');

// (a) 正常系: handler_id 駆動で UCP ハンドラが交換→与信→売上→確定。
$sid = createSession($client, $headers, $createBody);
$body = completeUcp($client, $headers, $sid, 'e2e-ucp-ok');
assertTrue(($body['status'] ?? null) === 'completed', 'complete (ok token) => status "completed"');

// (b) escalation: REQUIRES_ACTION は UCP では requires_escalation。ハンドラが実行された証跡。
$sid = createSession($client, $headers, $createBody);
$body = completeUcp($client, $headers, $sid, 'e2e-ucp-3ds');
assertTrue(($body['status'] ?? null) === 'requires_escalation', 'complete (3ds token) => "requires_escalation" (handler ran)');

// (c) 拒否: ハンドラが FAILED を返し確定しない (ready_for_complete) + messages[]。
$sid = createSession($client, $headers, $createBody);
$body = completeUcp($client, $headers, $sid, 'e2e-ucp-decline');
assertTrue(($body['status'] ?? null) !== 'completed', 'complete (decline token) => not "completed" (handler rejected)');
assertTrue(!empty($body['messages']), 'complete (decline token) => business messages[] present');

fwrite(STDOUT, "\n\033[32mPASS\033[0m ({$passed} assertions, session + complete)\n");
exit(0);

// ─────────────────────────────────────────────────────────────────────────
// helpers
// ─────────────────────────────────────────────────────────────────────────

/**
 * @param array<string, string>  $headers
 * @param array<string, mixed>   $createBody
 */
function createSession(HttpClientInterface $client, array $headers, array $createBody): string
{
    $res = $client->request('POST', 'ucp/checkout-sessions', [
        'headers' => $headers + ['Idempotency-Key' => bin2hex(random_bytes(16))],
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
 * @param array<string, string> $headers
 *
 * @return array<string, mixed>
 */
function completeUcp(HttpClientInterface $client, array $headers, string $sessionId, string $token): array
{
    $res = $client->request('POST', 'ucp/checkout-sessions/'.$sessionId.'/complete', [
        'headers' => $headers + ['Idempotency-Key' => bin2hex(random_bytes(16))],
        'json' => ['payment' => ['instruments' => [[
            'handler_id' => 'dev.ucp.payment.card',
            'credential' => ['type' => 'card', 'token' => $token],
        ]]]],
    ]);
    $status = $res->getStatusCode();
    $raw = $res->getContent(false);
    if (200 !== $status) {
        fwrite(STDERR, "    \033[33m↳ complete HTTP {$status}\033[0m: ".substr($raw, 0, 1000)."\n");
    }
    assertTrue(200 === $status, 'complete returns HTTP 200 (business outcomes are in messages[])');

    return json_decode($raw, true) ?: [];
}
