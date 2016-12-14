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

    protected $BaseInfo;
    protected $Product;
    protected $TaxRule2;
    protected $TaxRule3;
    
    private $DateTimeNow = null;

    public function setUp()
    {
        $this->DateTimeNow = new \DateTime('+1 minutes');
        parent::setUp();
        $this->BaseInfo = $this->app['eccube.repository.base_info']->get();
        $this->BaseInfo->setOptionProductTaxRule(0);
        $this->Product = $this->createProduct('生活必需品');
        // 2017-04-01とか指定すると, 2017年以降で結果が変わってしまうので1年後の日付を指定する
        $ApplyDate = new \DateTime('+1 years');
        $this->TaxRule1 = $this->app['eccube.repository.tax_rule']->find(1);
        $this->TaxRule1->setApplyDate($this->DateTimeNow);
        $this->TaxRule2 = $this->createTaxRule(10, $ApplyDate);
        $this->TaxRule3 = $this->createTaxRule(8, $ApplyDate);
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
            $apply_date = $this->DateTimeNow;
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
        $this->TaxRule2
            ->setProduct($this->Product);
        $this->app['orm.em']->flush();

        // 商品別税率以外を取得
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
        // デフォルトルールを取得(キャッシュから取得)
        $TaxRule = $this->app['eccube.repository.tax_rule']->getByRule();

        $this->expected = 1;
        $this->actual = $TaxRule->getId();
        $this->verify();
    }

    public function testGetByRule2()
    {
        $this->TaxRule1->setApplyDate(new \DateTime('+5 days'));
        $this->TaxRule2->setApplyDate(new \DateTime('-1 days'));
        $this->TaxRule3->setApplyDate(new \DateTime('-2 days'));
        $this->app['orm.em']->flush();

        $this->app['eccube.repository.tax_rule']->clearCache();
        $TaxRule = $this->app['eccube.repository.tax_rule']->getByRule();

        // TaxRule1 は無視され, TaxRule2 が適用される
        $this->expected = $this->TaxRule2->getId();
        $this->actual = $TaxRule->getId();
        $this->verify();
    }

    public function testGetByRuleWithPref()
    {
        $Pref = $this->app['eccube.repository.master.pref']->find(26);
        $oneDayBefore = new \DateTime('-1 days');
        
        $this->TaxRule2->setApplyDate($oneDayBefore);
        $this->TaxRule3
            ->setApplyDate($oneDayBefore)
            ->setPref($Pref);
        $this->app['orm.em']->flush();

        $this->app['eccube.repository.tax_rule']->clearCache();
        $TaxRule = $this->app['eccube.repository.tax_rule']->getByRule(
            null,               // Product
            null,               // ProductClass
            $Pref,              // Pref
            null                // Country
        );

        $this->expected = $this->TaxRule3->getId();
        $this->actual = $TaxRule->getId();
        $this->verify();
    }

    public function testGetByRuleWithCountry()
    {
        $Country = $this->app['orm.em']->getRepository('\Eccube\Entity\Master\Country')->find(300);
        $oneDayBefore = new \DateTime('-1 days');
        
        $this->TaxRule2
            ->setApplyDate($oneDayBefore)
            ->setCountry($Country);
        $this->TaxRule3
            ->setApplyDate($oneDayBefore);

        $this->app['orm.em']->flush();

        $this->app['eccube.repository.tax_rule']->clearCache();
        $TaxRule = $this->app['eccube.repository.tax_rule']->getByRule(
            null,               // Product
            null,               // ProductClass
            null,               // Pref
            $Country            // Country
        );

        $this->expected = $this->TaxRule2->getId();
        $this->actual = $TaxRule->getId();
        $this->verify();
    }

    public function testGetByRuleWithProduct()
    {
        $this->BaseInfo->setOptionProductTaxRule(1); // 商品別税率ON
        $this->app['orm.em']->flush();
        $oneDayBefore = new \DateTime('-1 days');

        $this->TaxRule2
            ->setApplyDate($oneDayBefore)
            ->setProduct($this->Product);
        $this->TaxRule3
            ->setApplyDate($oneDayBefore);

        $this->app['orm.em']->flush();

        $this->app['eccube.repository.tax_rule']->clearCache();
        $TaxRule = $this->app['eccube.repository.tax_rule']->getByRule(
            $this->Product,     // Product
            null,               // ProductClass
            null,               // Pref
            null                // Country
        );

        $this->expected = $this->TaxRule2->getId();
        $this->actual = $TaxRule->getId();
        $this->verify();
    }

    public function testGetByRuleWithProductClass()
    {
        $this->BaseInfo->setOptionProductTaxRule(1); // 商品別税率ON
        $this->app['orm.em']->flush();
        $oneDayBefore = new \DateTime('-1 days');

        $ProductClasses = $this->Product->getProductClasses();
        $ProductClass = $ProductClasses[1];
        $this->TaxRule2
            ->setApplyDate($oneDayBefore)
            ->setProductClass($ProductClass);
        $this->TaxRule3
            ->setApplyDate($oneDayBefore);

        $this->app['orm.em']->flush();

        $this->app['eccube.repository.tax_rule']->clearCache();
        $TaxRule = $this->app['eccube.repository.tax_rule']->getByRule(
            null,               // Product
            $ProductClass,      // ProductClass
            null,               // Pref
            null                // Country
        );

        $this->expected = $this->TaxRule2->getId();
        $this->actual = $TaxRule->getId();
        $this->verify();
    }

    public function testGetByRuleWithMulti()
    {
        $this->BaseInfo->setOptionProductTaxRule(1); // 商品別税率ON
        $this->app['orm.em']->flush();
        $oneDayBefore = new \DateTime('-1 days');

        $Country = $this->app['orm.em']->getRepository('\Eccube\Entity\Master\Country')->find(300);

        // 国別設定
        $this->TaxRule2
            ->setApplyDate($oneDayBefore)
            ->setCountry($Country);
        // 商品別設定
        $this->TaxRule3
            ->setApplyDate($oneDayBefore)
            ->setProduct($this->Product);

        $this->app['orm.em']->flush();

        $this->app['eccube.repository.tax_rule']->clearCache();
        $TaxRule = $this->app['eccube.repository.tax_rule']->getByRule(
            $this->Product,     // Product
            null,               // ProductClass
            null,               // Pref
            $Country            // Country
        );

        // 国別設定の方が優先される
        $this->expected = $this->TaxRule2->getId();
        $this->actual = $TaxRule->getId();
        $this->verify();
    }

    /**
     * TaxRuleEventSubscriber の確認用テストケース.
     *
     * @link https://github.com/EC-CUBE/ec-cube/issues/1029
     */
    public function testShipmentItem()
    {
        $this->BaseInfo->setOptionProductTaxRule(1); // 商品別税率ON
        $this->app['orm.em']->flush();
        $fiveDaysBefore = new \DateTime('-5 days');

        $this->TaxRule1->setApplyDate($fiveDaysBefore);
        $this->TaxRule2->setApplyDate($fiveDaysBefore);
        $this->TaxRule3->setApplyDate(new \DateTime('-2 days'));
        $this->app['orm.em']->flush();

        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);

        $this->app['eccube.repository.tax_rule']->clearCache();
        $Shippings = $Order->getShippings();

        foreach ($Shippings as $Shipping) {
            $ShipmentItems = $Shipping->getShipmentItems();

            foreach ($ShipmentItems as $Shipment) {
                $this->expected = round($Shipment->getPrice() + $Shipment->getPrice() * $this->TaxRule1->getTaxRate() / 100, 0);
                $this->actual = $Shipment->getPriceIncTax();
                $this->verify('ShipmentItem で TaxRuleEventSubscriber が正常にコールされるか');
            }
        }
    }
}
