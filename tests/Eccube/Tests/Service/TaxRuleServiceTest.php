<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Service;

use Eccube\Entity\BaseInfo;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\TaxRuleRepository;
use Eccube\Service\TaxRuleService;

class TaxRuleServiceTest extends AbstractServiceTestCase
{
    /**
     * @var TaxRuleService
     */
    private $taxRuleService;

    /**
     * @var  TaxRuleRepository
     */
    protected $TaxRule1;

    /**
     * @var  BaseInfo
     */
    protected $BaseInfo;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->BaseInfo = $this->container->get(BaseInfoRepository::class)->get();
        $this->BaseInfo->setOptionProductTaxRule(0);
        $this->TaxRule1 = $this->container->get(TaxRuleRepository::class)->find(1);
        $this->TaxRule1->setApplyDate(new \DateTime('-1 day'));
        $this->container->get('doctrine')->getManager()->flush();
        $this->taxRuleService = $this->container->get(TaxRuleService::class);
    }

    public function testRoundByCalcRuleWithDefault()
    {
        $input = 100.4;
        $this->expected = 101;
        $this->actual = $this->taxRuleService->roundByRoundingType($input, 999);
        $this->verify();

        $input = 100.5;
        $this->expected = 101;
        $this->actual = $this->taxRuleService->roundByRoundingType($input, 999);
        $this->verify();

        $input = 100;
        $this->expected = 100;
        $this->actual = $this->taxRuleService->roundByRoundingType($input, 999);
        $this->verify();

        $input = 101;
        $this->expected = 101;
        $this->actual = $this->taxRuleService->roundByRoundingType($input, 999);
        $this->verify();
    }

    public function testRoundByRoundingTypeWithCeil()
    {
        $input = 100.4;
        $this->expected = 101;
        $this->actual = $this->taxRuleService->roundByRoundingType($input, \Eccube\Entity\Master\RoundingType::CEIL);
        $this->verify();

        $input = 100.5;
        $this->expected = 101;
        $this->actual = $this->taxRuleService->roundByRoundingType($input, \Eccube\Entity\Master\RoundingType::CEIL);
        $this->verify();

        $input = 100;
        $this->expected = 100;
        $this->actual = $this->taxRuleService->roundByRoundingType($input, \Eccube\Entity\Master\RoundingType::CEIL);
        $this->verify();

        $input = 101;
        $this->expected = 101;
        $this->actual = $this->taxRuleService->roundByRoundingType($input, \Eccube\Entity\Master\RoundingType::CEIL);
        $this->verify();
    }

    public function testRoundByRoundingTypeWithRound()
    {
        $input = 100.4;
        $this->expected = 100;
        $this->actual = $this->taxRuleService->roundByRoundingType($input, \Eccube\Entity\Master\RoundingType::ROUND);
        $this->verify();

        $input = 100.5;
        $this->expected = 101;
        $this->actual = $this->taxRuleService->roundByRoundingType($input, \Eccube\Entity\Master\RoundingType::ROUND);
        $this->verify();

        $input = 100;
        $this->expected = 100;
        $this->actual = $this->taxRuleService->roundByRoundingType($input, \Eccube\Entity\Master\RoundingType::ROUND);
        $this->verify();

        $input = 101;
        $this->expected = 101;
        $this->actual = $this->taxRuleService->roundByRoundingType($input, \Eccube\Entity\Master\RoundingType::ROUND);
        $this->verify();
    }

    public function testRoundByRoundingTypeWithFloor()
    {
        $input = 100.4;
        $this->expected = 100;
        $this->actual = $this->taxRuleService->roundByRoundingType($input, \Eccube\Entity\Master\RoundingType::FLOOR);
        $this->verify();

        $input = 100.5;
        $this->expected = 100;
        $this->actual = $this->taxRuleService->roundByRoundingType($input, \Eccube\Entity\Master\RoundingType::FLOOR);
        $this->verify();

        $input = 100;
        $this->expected = 100;
        $this->actual = $this->taxRuleService->roundByRoundingType($input, \Eccube\Entity\Master\RoundingType::FLOOR);
        $this->verify();

        $input = 101;
        $this->expected = 101;
        $this->actual = $this->taxRuleService->roundByRoundingType($input, \Eccube\Entity\Master\RoundingType::FLOOR);
        $this->verify();
    }

    public function testCalcTax()
    {
        $input = 1000;
        $rate = 8;
        $this->expected = 80.0;
        $this->actual = $this->taxRuleService->calcTax($input, $rate, \Eccube\Entity\Master\RoundingType::ROUND);
        $this->verify();
    }

    public function testCalcTaxWithAdjust()
    {
        $input = 1008;
        $rate = 8;
        $adjust = -1;
        $this->expected = 80.0;
        $this->actual = $this->taxRuleService->calcTax($input, $rate, \Eccube\Entity\Master\RoundingType::ROUND, $adjust);
        $this->verify();
    }

    public function testGetTax()
    {
        $input = 1000;
        $rate = 8;
        $this->expected = 80.0;
        $this->actual = $this->taxRuleService->getTax($input);
        $this->verify();
    }

    public function testCalcIncTax()
    {
        $input = 1000;
        $rate = 8;
        $this->expected = 1080.0;
        $this->actual = $this->taxRuleService->getPriceIncTax($input);
        $this->verify();
    }
}
