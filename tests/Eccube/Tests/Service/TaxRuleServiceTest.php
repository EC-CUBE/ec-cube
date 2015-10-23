<?php

namespace Eccube\Tests\Service;

use Eccube\Entity\TaxRule;
use Eccube\Service\TaxRuleService;

class TaxRuleServiceTest extends AbstractServiceTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->BaseInfo = $this->app['eccube.repository.base_info']->get();
        $this->BaseInfo->setOptionProductTaxRule(0);
        $this->TaxRule1 = $this->app['eccube.repository.tax_rule']->find(1);
        $this->TaxRule1->setApplyDate(new \DateTime('-1 day'));
        $this->app['orm.em']->flush();
    }

    public function testRoundByCalcRuleWithDefault()
    {
        $input = 100.4;
        $this->expected = 101;
        $this->actual = $this->app['eccube.service.tax_rule']->roundByCalcRule($input, 999);
        $this->verify();

        $input = 100.5;
        $this->expected = 101;
        $this->actual = $this->app['eccube.service.tax_rule']->roundByCalcRule($input, 999);
        $this->verify();

        $input = 100;
        $this->expected = 100;
        $this->actual = $this->app['eccube.service.tax_rule']->roundByCalcRule($input, 999);
        $this->verify();

        $input = 101;
        $this->expected = 101;
        $this->actual = $this->app['eccube.service.tax_rule']->roundByCalcRule($input, 999);
        $this->verify();
    }

    public function testRoundByCalcRuleWithCeil()
    {
        $input = 100.4;
        $this->expected = 101;
        $this->actual = $this->app['eccube.service.tax_rule']->roundByCalcRule($input, \Eccube\Entity\Master\Taxrule::CEIL);
        $this->verify();

        $input = 100.5;
        $this->expected = 101;
        $this->actual = $this->app['eccube.service.tax_rule']->roundByCalcRule($input, \Eccube\Entity\Master\Taxrule::CEIL);
        $this->verify();

        $input = 100;
        $this->expected = 100;
        $this->actual = $this->app['eccube.service.tax_rule']->roundByCalcRule($input, \Eccube\Entity\Master\Taxrule::CEIL);
        $this->verify();

        $input = 101;
        $this->expected = 101;
        $this->actual = $this->app['eccube.service.tax_rule']->roundByCalcRule($input, \Eccube\Entity\Master\Taxrule::CEIL);
        $this->verify();
    }

    public function testRoundByCalcRuleWithRound()
    {
        $input = 100.4;
        $this->expected = 100;
        $this->actual = $this->app['eccube.service.tax_rule']->roundByCalcRule($input, \Eccube\Entity\Master\Taxrule::ROUND);
        $this->verify();

        $input = 100.5;
        $this->expected = 101;
        $this->actual = $this->app['eccube.service.tax_rule']->roundByCalcRule($input, \Eccube\Entity\Master\Taxrule::ROUND);
        $this->verify();

        $input = 100;
        $this->expected = 100;
        $this->actual = $this->app['eccube.service.tax_rule']->roundByCalcRule($input, \Eccube\Entity\Master\Taxrule::ROUND);
        $this->verify();

        $input = 101;
        $this->expected = 101;
        $this->actual = $this->app['eccube.service.tax_rule']->roundByCalcRule($input, \Eccube\Entity\Master\Taxrule::ROUND);
        $this->verify();
    }

    public function testRoundByCalcRuleWithFloor()
    {
        $input = 100.4;
        $this->expected = 100;
        $this->actual = $this->app['eccube.service.tax_rule']->roundByCalcRule($input, \Eccube\Entity\Master\Taxrule::FLOOR);
        $this->verify();

        $input = 100.5;
        $this->expected = 100;
        $this->actual = $this->app['eccube.service.tax_rule']->roundByCalcRule($input, \Eccube\Entity\Master\Taxrule::FLOOR);
        $this->verify();

        $input = 100;
        $this->expected = 100;
        $this->actual = $this->app['eccube.service.tax_rule']->roundByCalcRule($input, \Eccube\Entity\Master\Taxrule::FLOOR);
        $this->verify();

        $input = 101;
        $this->expected = 101;
        $this->actual = $this->app['eccube.service.tax_rule']->roundByCalcRule($input, \Eccube\Entity\Master\Taxrule::FLOOR);
        $this->verify();
    }

    public function testCalcTax()
    {
        $input = 1000;
        $rate = 8;
        $this->expected = 80.0;
        $this->actual = $this->app['eccube.service.tax_rule']->calcTax($input, $rate, \Eccube\Entity\Master\Taxrule::ROUND);
        $this->verify();
    }

    public function testCalcTaxWithAdjust()
    {
        $input = 1008;
        $rate = 8;
        $adjust = -1;
        $this->expected = 80.0;
        $this->actual = $this->app['eccube.service.tax_rule']->calcTax($input, $rate, \Eccube\Entity\Master\Taxrule::ROUND, $adjust);
        $this->verify();
    }

    public function testGetTax()
    {
        $input = 1000;
        $rate = 8;
        $this->expected = 80.0;
        $this->actual = $this->app['eccube.service.tax_rule']->getTax($input);
        $this->verify();
    }

    public function testCalcIncTax()
    {
        $input = 1000;
        $rate = 8;
        $this->expected = 1080.0;
        $this->actual = $this->app['eccube.service.tax_rule']->getPriceIncTax($input);
        $this->verify();
    }
}
