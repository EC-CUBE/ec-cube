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

namespace Eccube\Tests\Form\Validator;

use Eccube\Form\Validator\AdminRouteDir;
use Eccube\Tests\EccubeTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\SecurityBundle\Security\FirewallConfig;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class AdminRouteDirValidatorTest extends EccubeTestCase
{
    private ?ValidatorInterface $validator = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = static::getContainer()->get('validator');
    }

    /**
     * 検証で拒否する名前と, 実際に認証を掛けないファイアウォールが一致していること.
     */
    #[DataProvider(methodName: 'unsecuredParams')]
    public function testRejectsNameMatchingUnsecuredFirewall(string $value): void
    {
        /** @var FirewallMap $firewallMap */
        $firewallMap = static::getContainer()->get('security.firewall.map');
        $config = $firewallMap->getFirewallConfig(Request::create('/'.$value.'/order'));
        $this->assertInstanceOf(FirewallConfig::class, $config);
        $this->assertFalse($config->isSecurityEnabled(), sprintf('/%s/ は認証を掛けないファイアウォールに一致すること', $value));

        $violations = $this->validator->validate($value, new AdminRouteDir());
        $this->assertCount(1, $violations);
        $this->assertSame('form_error.admin_route_dir_unsecured', $violations[0]->getMessageTemplate());
    }

    public static function unsecuredParams(): \Iterator
    {
        yield ['_profiler'];
        yield ['_wdt'];
        yield ['css'];
        yield ['images'];
        yield ['js'];
    }

    #[DataProvider(methodName: 'acceptedParams')]
    public function testAcceptsName(string $value): void
    {
        /** @var FirewallMap $firewallMap */
        $firewallMap = static::getContainer()->get('security.firewall.map');
        $config = $firewallMap->getFirewallConfig(Request::create('/'.$value.'/order'));
        $this->assertInstanceOf(FirewallConfig::class, $config);
        $this->assertTrue($config->isSecurityEnabled(), sprintf('/%s/ は認証を掛けるファイアウォールに一致すること', $value));

        $this->assertCount(0, $this->validator->validate($value, new AdminRouteDir()));
    }

    public static function acceptedParams(): \Iterator
    {
        yield ['admin'];
        // ファイアウォールは大文字・小文字を区別する
        yield ['IMAGES'];
        yield ['Css'];
        yield ['images_admin'];
        yield ['_profiler_admin'];
    }

    public function testRejectsFrontRoute(): void
    {
        $violations = $this->validator->validate('cart', new AdminRouteDir());
        $this->assertCount(1, $violations);
        $this->assertSame('form_error.admin_route_dir_front_route', $violations[0]->getMessageTemplate());
    }

    public function testAcceptsEmpty(): void
    {
        $this->assertCount(0, $this->validator->validate('', new AdminRouteDir()));
        $this->assertCount(0, $this->validator->validate(null, new AdminRouteDir()));
    }
}
