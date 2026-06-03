<?php

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
 * Verifies that the five enable flags default to false and that
 * google_pay_merchant_id defaults to null for the persisted BaseInfo (id=1),
 * matching the "default false / off by default" contract from the base plan.
 */
class BaseInfoAgentCommerceFlagsTest extends EccubeTestCase
{
    /** @var string[] The five enable flags that MUST default to false. */
    private const FLAG_PROPERTIES = [
        'acp_enabled',
        'ucp_enabled',
        'acp_feed_enabled',
        'ucp_catalog_api_enabled',
        'ucp_catalog_requires_auth',
    ];

    public function testPersistedBaseInfoFlagsDefaultToFalse(): void
    {
        $BaseInfo = static::getContainer()->get(BaseInfoRepository::class)->get();

        foreach (self::FLAG_PROPERTIES as $property) {
            $value = $this->readBooleanFlag($BaseInfo, $property);
            self::assertFalse($value, sprintf('BaseInfo flag "%s" must default to false (off by default)', $property));
        }
    }

    public function testPersistedBaseInfoGooglePayMerchantIdDefaultsToNull(): void
    {
        $BaseInfo = static::getContainer()->get(BaseInfoRepository::class)->get();

        self::assertNull($this->readGooglePayMerchantId($BaseInfo), 'BaseInfo google_pay_merchant_id must default to null');
    }

    public function testNewBaseInfoFlagsDefaultToFalse(): void
    {
        $BaseInfo = new BaseInfo();

        foreach (self::FLAG_PROPERTIES as $property) {
            $value = $this->readBooleanFlag($BaseInfo, $property);
            self::assertFalse((bool) $value, sprintf('A freshly constructed BaseInfo must report "%s" as false', $property));
        }

        self::assertNull($this->readGooglePayMerchantId($BaseInfo), 'A freshly constructed BaseInfo must report google_pay_merchant_id as null');
    }

    private function readBooleanFlag(BaseInfo $BaseInfo, string $property): bool
    {
        $studly = str_replace('_', '', ucwords($property, '_'));
        foreach (['is'.$studly, 'get'.$studly] as $getter) {
            if (method_exists($BaseInfo, $getter)) {
                return (bool) $BaseInfo->{$getter}();
            }
        }

        self::fail(sprintf('BaseInfo must expose a getter (is%1$s or get%1$s) for the "%2$s" flag', $studly, $property));
    }

    private function readGooglePayMerchantId(BaseInfo $BaseInfo): ?string
    {
        if (method_exists($BaseInfo, 'getGooglePayMerchantId')) {
            return $BaseInfo->getGooglePayMerchantId();
        }

        self::fail('BaseInfo must expose getGooglePayMerchantId() for the google_pay_merchant_id column');
    }
}
