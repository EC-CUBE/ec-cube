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

namespace Eccube\Tests\Service\Mcp\Contract;

use Eccube\Entity\Plugin;
use Eccube\Repository\PluginRepository;
use Eccube\Service\Mcp\McpScope;
use Eccube\Tests\EccubeTestCase;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\SecurityBundle\Security\FirewallConfig;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\Request;

/**
 * 設計 §8 #7 「api44 の有効化で領域別 read scope と ^/admin/mcp firewall が有効化され、 無効化で消える」
 * の半契約テスト。
 *
 * Symfony の firewall は kernel boot 時に bundles.php から組み立てられるため、 「無効化 → 消える」 を
 * 同一 process 内で完全自動化するには kernel reboot + cache 再生成が必要となり、 規模が大きすぎる。
 *
 * 本テストでは「Api44 が install + enabled 状態で MCP 依存リソース (firewall, 領域別 scope の role
 * 定数) が確実に container に登録されている」 こと、 つまり **AC #7 の前半 (有効化で動く)** を半契約として
 * 担保する。 後半「無効化で消える」 は手動確認 (release checklist) で代替する。
 */
#[Group('mcp')]
final class Api44LifecycleContractTest extends EccubeTestCase
{
    private ?PluginRepository $pluginRepository = null;
    private ?FirewallMap $firewallMap = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pluginRepository = static::getContainer()->get(PluginRepository::class);
        // 'security.firewall.map' は private service ID で、 test container では class FQCN 解決できない
        // ため文字列 ID で取得する (Symfony FrameworkBundle TestContainer の制約)。 Rector の
        // ContainerGetNameToTypeInTestsRector は rector.php で本ファイルだけ skip 指定済み。
        $this->firewallMap = static::getContainer()->get('security.firewall.map');
    }

    public function testApi44IsInstalledAndEnabled(): void
    {
        $plugin = $this->pluginRepository->findByCode('Api44');

        $this->assertInstanceOf(Plugin::class, $plugin, 'Api44 が install されている (テスト DB に dtb_plugin レコードあり)');
        $this->assertTrue($plugin->isEnabled(), 'Api44 が enabled');
        $this->assertTrue($plugin->isInitialized(), 'Api44 が initialized');
    }

    public function testMcpFirewallIsMappedForAdminMcpPath(): void
    {
        $request = Request::create('/'.$this->getAdminRoute().'/mcp');
        $config = $this->firewallMap->getFirewallConfig($request);

        $this->assertInstanceOf(FirewallConfig::class, $config, '/admin/mcp に対する firewall が解決される');
        $this->assertSame('mcp', $config->getName(), 'mcp firewall (Api44 が prepend) が当たる');
        $this->assertTrue($config->isStateless(), 'stateless = OAuth2 resource server 動作');
    }

    public function testNonMcpAdminPathStillUsesAdminFirewall(): void
    {
        // /admin/dashboard 等の通常 admin パスは admin firewall に当たる (cookie based)
        $request = Request::create('/'.$this->getAdminRoute().'/');
        $config = $this->firewallMap->getFirewallConfig($request);

        $this->assertInstanceOf(FirewallConfig::class, $config);
        $this->assertSame('admin', $config->getName(), '通常 admin path は cookie based admin firewall');
    }

    public function testMcpRoleConstantsAreDefinedForAllDomains(): void
    {
        // 設計 §4.1: 4 領域 (product / order / customer / plugin) の read scope に対応する role 定数
        $expected = [
            McpScope::ROLE_PRODUCT_READ => 'ROLE_OAUTH2_MCP:PRODUCT:READ',
            McpScope::ROLE_ORDER_READ => 'ROLE_OAUTH2_MCP:ORDER:READ',
            McpScope::ROLE_CUSTOMER_READ => 'ROLE_OAUTH2_MCP:CUSTOMER:READ',
            McpScope::ROLE_PLUGIN_READ => 'ROLE_OAUTH2_MCP:PLUGIN:READ',
        ];

        foreach ($expected as $constantValue => $expectedString) {
            $this->assertSame($expectedString, $constantValue);
        }
    }

    private function getAdminRoute(): string
    {
        $route = static::getContainer()->getParameter('eccube_admin_route');
        \assert(\is_string($route));

        return $route;
    }
}
