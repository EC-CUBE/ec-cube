<?php

namespace Eccube\Tests\Repository;

use Eccube\Tests\EccubeTestCase;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Entity\TaxRule;
use Doctrine\ORM\NoResultException;

/**
 * TaxRuleRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
class TaxRuleRepositoryTest extends EccubeTestCase
{

    protected $Product;
    protected $TaxRule2;
    protected $TaxRule3;

    public function setUp()
    {
        parent::setUp();
        $this->Product = $this->createProduct('生活必需品');
        // 2017-04-01とか指定すると, 2017年以降で結果が変わってしまうので1年後の日付を指定する
        $ApplyDate = new \DateTime('+1 years');
        $this->TaxRule2 = $this->createTaxRule(10, $ApplyDate);
        $this->TaxRule3 = $this->createTaxRule(8, $ApplyDate);
        $this->TaxRule3->setProduct($this->Product);
        $this->app['orm.em']->flush();
    }

    public function createTaxRule($tax_rate = 8, $apply_date = null)
    {
        $TaxRule = new TaxRule();
        $CalcRule = $this->app['orm.em']
            ->getRepository('Eccube\Entity\Master\Taxrule')
            ->find(1);
        $Member = $this->app['eccube.repository.member']->find(2);
        if (is_null($apply_date)) {
            $apply_date = new \DateTime();
        }
        $TaxRule
            ->setTaxRate($tax_rate)
            ->setApplyDate($apply_date)
            ->setCalcRule($CalcRule)
            ->setTaxAdjust(0)
            ->setCreator($Member)
            ->setDelFlg(0);
        $this->app['orm.em']->persist($TaxRule);
        $this->app['orm.em']->flush();
        return $TaxRule;
    }

    public function testGetById()
    {
        $Result = $this->app['eccube.repository.tax_rule']->getById(1);

        $this->expected = 1;
        $this->actual = $Result->getId();
        $this->verify();
    }

    public function testGetList()
    {
        $TaxRules = $this->app['eccube.repository.tax_rule']->getList();

        $this->expected = 2;
        $this->actual = count($TaxRules);
        $this->verify();
    }

    public function testDelete()
    {
        $this->app['eccube.repository.tax_rule']->delete($this->TaxRule2);
        $Results = $this->app['eccube.repository.tax_rule']->findAll();

        $this->expected = 2;
        $this->actual = count($Results);
        $this->verify();
    }

    public function testDeleteWithId()
    {
        $this->app['eccube.repository.tax_rule']->delete($this->TaxRule2->getId());

        $Results = $this->app['eccube.repository.tax_rule']->findAll();

        $this->expected = 2;
        $this->actual = count($Results);
        $this->verify();
    }

    public function testGetByRule()
    {
        // デフォルトルールを取得
        $TaxRule = $this->app['eccube.repository.tax_rule']->getByRule();

        $this->expected = 1;
        $this->actual = $TaxRule->getId();
        $this->verify();
    }

    /* TODO 以下 issues の修正時に実装する
     * https://github.com/EC-CUBE/ec-cube/issues/1005
     */
    public function testGetByRuleWithPref()
    {
        self::markTestSkipped();
        $Pref = $this->app['eccube.repository.master.pref']->find(1);
        $this->TaxRule2->setApplyDate(new \DateTime('-1 days'));
        $this->TaxRule3
            ->setApplyDate(new \DateTime('-1 days'))
            ->setPref($Pref);
        $this->app['orm.em']->flush();

        $TaxRule = $this->app['eccube.repository.tax_rule']->getByRule(
            null,               // Product
            null,               // ProductClass
            $Pref,              // Pref
            null                // Country
        );

        $All = $this->app['eccube.repository.tax_rule']->findAll();
        $this->expected = $this->TaxRule3->getId();
        $this->actual = $TaxRule->getId();
        $this->verify();
    }
}
