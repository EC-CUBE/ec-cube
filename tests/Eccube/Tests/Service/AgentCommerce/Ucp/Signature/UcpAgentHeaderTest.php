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

namespace Eccube\Tests\Service\AgentCommerce\Ucp\Signature;

use Eccube\Service\AgentCommerce\Ucp\Signature\UcpAgentHeader;
use PHPUnit\Framework\TestCase;

/**
 * Layer 1 (純ロジック) tests for UcpAgentHeader.
 *
 * UCP-Agent ヘッダ (RFC 8941 Dictionary) の profile 抽出と HTTPS 必須を検証する。
 *
 * @see https://github.com/Universal-Commerce-Protocol/ucp UCP signatures.md (UCP-Agent header)
 */
final class UcpAgentHeaderTest extends TestCase
{
    public function testParsesProfileUrl(): void
    {
        $header = UcpAgentHeader::parse('profile="https://agent.example/.well-known/ucp"');

        $this->assertSame('https://agent.example/.well-known/ucp', $header->profileUrl);
        $this->assertSame('agent.example', $header->host());
    }

    public function testRejectsMissingProfile(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        UcpAgentHeader::parse('version="1"');
    }

    public function testRejectsNonHttpsProfile(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('HTTPS');
        UcpAgentHeader::parse('profile="http://agent.example/.well-known/ucp"');
    }
}
