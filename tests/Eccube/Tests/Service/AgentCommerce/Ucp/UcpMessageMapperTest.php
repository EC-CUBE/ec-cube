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

namespace Eccube\Tests\Service\AgentCommerce\Ucp;

use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutMessage;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutMessageCode;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutMessageLevel;
use Eccube\Service\AgentCommerce\Ucp\UcpMessageMapper;
use PHPUnit\Framework\TestCase;

/**
 * Layer 1 (純ロジック) tests for UcpMessageMapper.
 *
 * 中立メッセージ -> UCP messages[] (type/severity/content) の変換を検証する。
 */
final class UcpMessageMapperTest extends TestCase
{
    private UcpMessageMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new UcpMessageMapper();
    }

    public function testErrorMessageCarriesRecoverableSeverity(): void
    {
        $result = $this->mapper->toUcpMessages([
            new AgentCheckoutMessage(AgentCheckoutMessageLevel::ERROR, '在庫が不足しています'),
        ]);

        $this->assertCount(1, $result);
        $this->assertSame('error', $result[0]['type']);
        $this->assertSame('recoverable', $result[0]['severity'], 'PurchaseFlow 由来のエラーは update で再試行可能なため recoverable');
        $this->assertSame('在庫が不足しています', $result[0]['content']);
        $this->assertSame('plain', $result[0]['content_type']);
    }

    /**
     * message_error.json / message_warning.json は code を required とする (freeform 可).
     *
     * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/v2026-04-08/source/schemas/shopping/types/message_error.json#L6
     * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/v2026-04-08/source/schemas/shopping/types/message_warning.json#L6
     */
    public function testErrorAndWarningCarryCodeWithLevelDefaults(): void
    {
        $result = $this->mapper->toUcpMessages([
            new AgentCheckoutMessage(AgentCheckoutMessageLevel::ERROR, 'エラー'),
            new AgentCheckoutMessage(AgentCheckoutMessageLevel::WARNING, '警告'),
            new AgentCheckoutMessage(AgentCheckoutMessageLevel::INFO, '案内'),
        ]);

        $this->assertSame('invalid', $result[0]['code'], 'MUST: an error message carries a code; code-less errors default to "invalid"');
        $this->assertSame('limited_availability', $result[1]['code'], 'MUST: a warning message carries a code; code-less warnings default to "limited_availability"');
        $this->assertArrayNotHasKey('code', $result[2], 'info messages have no required code');
    }

    public function testExplicitNeutralCodeIsEmittedAsIs(): void
    {
        $result = $this->mapper->toUcpMessages([
            new AgentCheckoutMessage(AgentCheckoutMessageLevel::ERROR, '住所が必要です', AgentCheckoutMessageCode::ADDRESS_REQUIRED),
            new AgentCheckoutMessage(AgentCheckoutMessageLevel::ERROR, '決済失敗', AgentCheckoutMessageCode::PAYMENT_FAILED),
        ]);

        $this->assertSame('address_required', $result[0]['code']);
        $this->assertSame('payment_failed', $result[1]['code'], 'payment_failed is a UCP standard error code (error_code.json examples)');
    }

    public function testWarningAndInfoHaveNoSeverity(): void
    {
        $result = $this->mapper->toUcpMessages([
            new AgentCheckoutMessage(AgentCheckoutMessageLevel::WARNING, '警告'),
            new AgentCheckoutMessage(AgentCheckoutMessageLevel::INFO, '案内'),
        ]);

        $this->assertSame('warning', $result[0]['type']);
        $this->assertArrayNotHasKey('severity', $result[0]);
        $this->assertSame('info', $result[1]['type']);
        $this->assertArrayNotHasKey('severity', $result[1]);
    }

    public function testRequiresEscalationDetectsBuyerInputSeverity(): void
    {
        $this->assertFalse($this->mapper->requiresEscalation([['type' => 'error', 'severity' => 'recoverable']]));
        $this->assertTrue($this->mapper->requiresEscalation([['type' => 'error', 'severity' => 'requires_buyer_input']]));
        $this->assertTrue($this->mapper->requiresEscalation([['type' => 'error', 'severity' => 'requires_buyer_review']]));
    }
}
