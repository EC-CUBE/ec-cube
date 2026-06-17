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

use Eccube\Entity\BaseInfo;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Tests\EccubeTestCase;

/**
 * Layer 2 (Doctrine) tests for the agent-commerce BaseInfo flags.
 *
 * Verifies that the enable flags (acp_checkout_enabled / ucp_checkout_enabled /
 * ucp_catalog_requires_auth) default to false for both the persisted BaseInfo
 * (id=1) and a freshly constructed instance, matching the "default false / off
 * by default" contract from the base plan.
 *
 * Discovery / catalog are always public (no flag); only checkout and the future
 * catalog auth mode are gated by these flags.
 */
final class BaseInfoAgentCommerceFlagsTest extends EccubeTestCase
{
    /** @var string[] The enable flags that MUST default to false. */
    private const FLAG_PROPERTIES = [
        'acp_checkout_enabled',
        'ucp_checkout_enabled',
        'ucp_catalog_requires_auth',
    ];

    public function testPersistedBaseInfoFlagsDefaultToFalse(): void
    {
        $BaseInfo = static::getContainer()->get(BaseInfoRepository::class)->get();

        foreach (self::FLAG_PROPERTIES as $property) {
            $value = $this->readBooleanFlag($BaseInfo, $property);
            $this->assertFalse($value, sprintf('BaseInfo flag "%s" must default to false (off by default)', $property));
        }
    }

    public function testNewBaseInfoFlagsDefaultToFalse(): void
    {
        $BaseInfo = new BaseInfo();

        foreach (self::FLAG_PROPERTIES as $property) {
            $value = $this->readBooleanFlag($BaseInfo, $property);
            $this->assertFalse($value, sprintf('A freshly constructed BaseInfo must report "%s" as false', $property));
        }
    }

    private function readBooleanFlag(BaseInfo $BaseInfo, string $property): bool
    {
        $studly = str_replace('_', '', ucwords($property, '_'));
        foreach (['is'.$studly, 'get'.$studly] as $getter) {
            if (method_exists($BaseInfo, $getter)) {
                $value = $BaseInfo->{$getter}();
                // null を (bool) で false に丸めると「default false」契約を取りこぼすため bool 型を厳密に要求する。
                self::assertIsBool($value, sprintf('%s() must return bool (got %s)', $getter, get_debug_type($value)));

                return $value;
            }
        }

        self::fail(sprintf('BaseInfo must expose a getter (is%1$s or get%1$s) for the "%2$s" flag', $studly, $property));
    }
}
