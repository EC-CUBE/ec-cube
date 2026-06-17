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

use Eccube\Service\AgentCommerce\Security\FilesystemKeyStore;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Layer 1 tests for FilesystemKeyStore (purpose のパストラバーサル防御).
 */
final class FilesystemKeyStoreTest extends TestCase
{
    /**
     * @return array<string, array{string}>
     */
    public static function invalidPurposeProvider(): array
    {
        return [
            'parent traversal' => ['../../../etc/passwd'],
            'slash' => ['foo/bar'],
            'dot' => ['ucp.signing'],
            'uppercase' => ['UcpSigning'],
            'empty' => [''],
        ];
    }

    #[DataProvider('invalidPurposeProvider')]
    public function testReadRejectsInvalidPurpose(string $purpose): void
    {
        $store = new FilesystemKeyStore(sys_get_temp_dir());

        $this->expectException(\InvalidArgumentException::class);
        $store->read($purpose);
    }

    public function testValidPurposeIsAccepted(): void
    {
        // 許可文字のみの purpose は例外を投げず、未生成キーは null を返す。
        $store = new FilesystemKeyStore(sys_get_temp_dir().'/eccube-keystore-test-'.bin2hex(random_bytes(4)));

        $this->assertNull($store->read('ucp_signing'), '未生成の鍵は null を返す');
    }
}
