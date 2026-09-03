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

namespace Eccube\Tests\Service\Mcp;

use Eccube\Service\Mcp\AllowListResolver;
use PHPUnit\Framework\TestCase;

/**
 * `AllowListResolver` の純粋ロジックのユニットテスト。 DB 不要。
 *
 * Api44 (`Plugin\Api44\GraphQL\AllowList`) の `$allows` プロパティと同型の private プロパティを
 * 持つダブル (`FakeAllowList`) を使って、 タグ集約・ union・形式不正の安全処理を検証する。
 */
final class AllowListResolverTest extends TestCase
{
    public function testReturnsEmptyWhenNoAllowList(): void
    {
        $resolver = new AllowListResolver([]);

        $this->assertSame([], $resolver->getAllowedProperties(\stdClass::class));
        $this->assertFalse($resolver->isAllowed(\stdClass::class, 'anything'));
    }

    public function testReadsPropertiesFromSingleAllowList(): void
    {
        $resolver = new AllowListResolver([
            new FakeAllowList([
                \stdClass::class => ['id', 'name'],
            ]),
        ]);

        $this->assertSame(['id', 'name'], $resolver->getAllowedProperties(\stdClass::class));
        $this->assertTrue($resolver->isAllowed(\stdClass::class, 'name'));
        $this->assertFalse($resolver->isAllowed(\stdClass::class, 'secret'));
    }

    public function testUnionsMultipleAllowLists(): void
    {
        $resolver = new AllowListResolver([
            new FakeAllowList([\stdClass::class => ['id', 'name']]),
            new FakeAllowList([\stdClass::class => ['name', 'email']]),
        ]);

        $merged = $resolver->getAllowedProperties(\stdClass::class);
        sort($merged);
        $this->assertSame(['email', 'id', 'name'], $merged);
    }

    public function testAcceptsArrayObjectBackedAllows(): void
    {
        $allows = new \ArrayObject([\stdClass::class => ['id']]);
        $resolver = new AllowListResolver([new FakeAllowList($allows)]);

        $this->assertSame(['id'], $resolver->getAllowedProperties(\stdClass::class));
    }

    public function testIgnoresMalformedAllowsEntries(): void
    {
        $resolver = new AllowListResolver([
            new FakeAllowList([
                \stdClass::class => ['id'],
                42 => ['ignored'],            // 数値キー → 無視
                'no-such-class' => 'oops',     // 値が配列じゃない → 無視
            ]),
            new \stdClass(),                   // `$allows` プロパティ無し → 無視
        ]);

        $this->assertSame(['id'], $resolver->getAllowedProperties(\stdClass::class));
    }
}
