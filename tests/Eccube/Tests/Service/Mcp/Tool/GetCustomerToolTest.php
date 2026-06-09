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

namespace Eccube\Tests\Service\Mcp\Tool;

use Eccube\Service\Mcp\Tool\GetCustomerTool;
use Eccube\Tests\EccubeTestCase;

/**
 * `GetCustomerTool` の DB 結合テスト。
 */
final class GetCustomerToolTest extends EccubeTestCase
{
    private ?GetCustomerTool $tool = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->tool = static::getContainer()->get(GetCustomerTool::class);
    }

    public function testReturnsCustomerById(): void
    {
        $customer = $this->createCustomer('mcp-getcustomer@example.com');

        $result = $this->tool->get(id: $customer->getId());

        $this->assertSame($customer->getId(), $result['id']);
        $this->assertSame('mcp-getcustomer@example.com', $result['email']);
    }

    public function testReturnsEmptyWhenNotFound(): void
    {
        $result = $this->tool->get(id: 99999999);

        $this->assertSame(['found' => false], $result);
    }
}
