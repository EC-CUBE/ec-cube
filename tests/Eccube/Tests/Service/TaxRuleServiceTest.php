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

namespace Eccube\Tests\Service;

use Eccube\Entity\BaseInfo;
use Eccube\Entity\Master\RoundingType;
use Eccube\Entity\TaxRule;
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
    protected function setUp(): void
    {
        parent::setUp();
        $this->BaseInfo = $this->entityManager->getRepository(BaseInfo::class)->get();
        $this->BaseInfo->setOptionProductTaxRule(0);
        $this->TaxRule1 = $this->entityManager->getRepository(TaxRule::class)->find(1);
        $this->TaxRule1->setApplyDate(new \DateTime('-1 day'));
        static::getContainer()->get('doctrine')->getManager()->flush();
        $this->taxRuleService = static::getContainer()->get(TaxRuleService::class);
    }

    /**
     * @group decimal
     */
    public function testRoundByCalcRuleWithDefault()
    {
        $input = '100.4';
        $this->expected = '101';
        $this->actual = $this->taxRuleService->roundByRoundingType($input, 999);
        $this->verify();

        $input = '100.5';
        $this->expected = '101';
        $this->actual = $this->taxRuleService->roundByRoundingType($input, 999);
        $this->verify();

        $input = '100';
        $this->expected = '100';
        $this->actual = $this->taxRuleService->roundByRoundingType($input, 999);
        $this->verify();

        $input = '101';
        $this->expected = '101';
        $this->actual = $this->taxRuleService->roundByRoundingType($input, 999);
        $this->verify();
    }

    /**
     * @group decimal
     */
    public function testRoundByRoundingTypeWithCeil()
    {
        $input = '100.4';
        $this->expected = '101';
        $this->actual = $this->taxRuleService->roundByRoundingType($input, RoundingType::CEIL);
        $this->verify();

        $input = '100.5';
        $this->expected = '101';
        $this->actual = $this->taxRuleService->roundByRoundingType($input, RoundingType::CEIL);
        $this->verify();

        $input = '100';
        $this->expected = '100';
        $this->actual = $this->taxRuleService->roundByRoundingType($input, RoundingType::CEIL);
        $this->verify();

        $input = '101';
        $this->expected = '101';
        $this->actual = $this->taxRuleService->roundByRoundingType($input, RoundingType::CEIL);
        $this->verify();
    }

    /**
     * @group decimal
     */
    public function testRoundByRoundingTypeWithRound()
    {
        $input = '100.4';
        $this->expected = '100';
        $this->actual = $this->taxRuleService->roundByRoundingType($input, RoundingType::ROUND);
        $this->verify();

        $input = '100.5';
        $this->expected = '101';
        $this->actual = $this->taxRuleService->roundByRoundingType($input, RoundingType::ROUND);
        $this->verify();

        $input = '100';
        $this->expected = '100';
        $this->actual = $this->taxRuleService->roundByRoundingType($input, RoundingType::ROUND);
        $this->verify();

        $input = '101';
        $this->expected = '101';
        $this->actual = $this->taxRuleService->roundByRoundingType($input, RoundingType::ROUND);
        $this->verify();
    }

    /**
     * @group decimal
     */
    public function testRoundByRoundingTypeWithFloor()
    {
        $input = '100.4';
        $this->expected = '100';
        $this->actual = $this->taxRuleService->roundByRoundingType($input, RoundingType::FLOOR);
        $this->verify();

        $input = '100.5';
        $this->expected = '100';
        $this->actual = $this->taxRuleService->roundByRoundingType($input, RoundingType::FLOOR);
        $this->verify();

        $input = '100';
        $this->expected = '100';
        $this->actual = $this->taxRuleService->roundByRoundingType($input, RoundingType::FLOOR);
        $this->verify();

        $input = '101';
        $this->expected = '101';
        $this->actual = $this->taxRuleService->roundByRoundingType($input, RoundingType::FLOOR);
        $this->verify();
    }

    /**
     * @group decimal
     */
    public function testCalcTax()
    {
        $input = '1000';
        $rate = '8';
        $this->expected = '80.00';
        $this->actual = $this->taxRuleService->calcTax($input, $rate, RoundingType::ROUND);
        $this->verify();
    }

    /**
     * @group decimal
     */
    public function testCalcTaxWithAdjust()
    {
        $input = '1008';
        $rate = '8';
        $adjust = '-1';
        $this->expected = '80.00';
        $this->actual = $this->taxRuleService->calcTax($input, $rate, RoundingType::ROUND, $adjust);
        $this->verify();
    }

    /**
     * @group decimal
     */
    public function testGetTax()
    {
        $input = '1000';
        $this->expected = '100.00';
        $this->actual = $this->taxRuleService->getTax($input);
        $this->verify();
    }

    /**
     * @group decimal
     */
    public function testCalcIncTax()
    {
        $input = '1000';
        $this->expected = '1100.00';
        $this->actual = $this->taxRuleService->getPriceIncTax($input);
        $this->verify();
    }
}
