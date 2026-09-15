<?php

declare(strict_types=1);

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

namespace Eccube\Tests\Service\AgentCommerce;

use Eccube\Service\AgentCommerce\JsonObjectFieldNormalizer;
use PHPUnit\Framework\TestCase;

/**
 * Layer 1 (純ロジック): 空配列を JSON object {} として配信するための正規化.
 */
final class JsonObjectFieldNormalizerTest extends TestCase
{
    public function testEmptyArrayAtPathBecomesObject(): void
    {
        $body = ['ucp' => ['version' => '2026-04-08', 'payment_handlers' => [], 'capabilities' => []], 'id' => 'x'];

        $json = json_encode(JsonObjectFieldNormalizer::normalize($body, ['ucp.payment_handlers', 'ucp.capabilities']));

        $this->assertSame('{"ucp":{"version":"2026-04-08","payment_handlers":{},"capabilities":{}},"id":"x"}', $json);
    }

    public function testNonEmptyValuesAreLeftUntouched(): void
    {
        $body = ['ucp' => ['payment_handlers' => ['dev.ucp.payment.card' => [['id' => 'dev.ucp.payment.card']]]]];

        $this->assertSame($body, JsonObjectFieldNormalizer::normalize($body, ['ucp.payment_handlers']));
    }

    public function testMissingPathsAreIgnored(): void
    {
        $body = ['code' => 'idempotency_conflict', 'content' => 'x'];

        $this->assertSame($body, JsonObjectFieldNormalizer::normalize($body, ['ucp.payment_handlers', 'capabilities']));
    }

    public function testTopLevelPath(): void
    {
        $json = json_encode(JsonObjectFieldNormalizer::normalize(['capabilities' => [], 'id' => 'x'], ['capabilities']));

        $this->assertSame('{"capabilities":{},"id":"x"}', $json);
    }

    /**
     * Idempotency リプレイ: DB の json 列から assoc decode した本文 ({} が [] に化けた状態) を配信形へ戻せる.
     */
    public function testRoundTripThroughAssocDecodeIsRepaired(): void
    {
        $fresh = ['ucp' => ['payment_handlers' => new \stdClass()], 'capabilities' => new \stdClass()];
        $replayed = json_decode((string) json_encode($fresh), true);
        $this->assertSame([], $replayed['ucp']['payment_handlers'], 'assoc decode turns {} into [] (the regression being guarded)');

        $json = json_encode(JsonObjectFieldNormalizer::normalize($replayed, ['ucp.payment_handlers', 'capabilities']));

        $this->assertSame('{"ucp":{"payment_handlers":{}},"capabilities":{}}', $json);
    }
}
