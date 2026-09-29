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

namespace Eccube\Tests\Service\AgentCommerce\Security;

use Eccube\Service\AgentCommerce\Security\AcpWebhookKeyPurpose;
use Eccube\Service\AgentCommerce\Security\KeyPurposeInterface;
use Eccube\Service\AgentCommerce\Security\KeyPurposeRegistry;
use Eccube\Service\AgentCommerce\Security\UcpSigningKeyPurpose;
use PHPUnit\Framework\TestCase;

final class KeyPurposeRegistryTest extends TestCase
{
    public function testAllIsIndexedByPurposeAndSorted(): void
    {
        $registry = new KeyPurposeRegistry([new UcpSigningKeyPurpose(), new AcpWebhookKeyPurpose()]);

        $this->assertSame(['acp_webhook', 'ucp_signing'], array_keys($registry->all()));
        $this->assertInstanceOf(UcpSigningKeyPurpose::class, $registry->all()['ucp_signing']);
    }

    public function testHas(): void
    {
        $registry = new KeyPurposeRegistry([new UcpSigningKeyPurpose()]);

        $this->assertTrue($registry->has('ucp_signing'));
        $this->assertFalse($registry->has('acp_webhook'));
    }

    public function testGetListsAvailablePurposesWhenUnknown(): void
    {
        $registry = new KeyPurposeRegistry([new UcpSigningKeyPurpose()]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/ucp_signing/');
        $registry->get('unknown_purpose');
    }

    /**
     * 同じ purpose を 2 つの実装が宣言すると, どちらが使われるかが登録順で決まってしまう.
     * 署名鍵の差し替えを許すことになるため, 沈黙せず失敗させる.
     */
    public function testDuplicatedPurposeIsRejected(): void
    {
        $duplicate = new class implements KeyPurposeInterface {
            public function getPurpose(): string
            {
                return 'ucp_signing';
            }

            public function getLabel(): string
            {
                return 'なりすまし';
            }

            public function generate(): string
            {
                return 'x';
            }

            public function describe(string $material): array
            {
                return [];
            }
        };

        $registry = new KeyPurposeRegistry([new UcpSigningKeyPurpose(), $duplicate]);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessageMatches('/ucp_signing/');
        $registry->all();
    }

    public function testEmptyRegistry(): void
    {
        $registry = new KeyPurposeRegistry([]);

        $this->assertSame([], $registry->all());
        $this->expectException(\InvalidArgumentException::class);
        $registry->get('ucp_signing');
    }
}
