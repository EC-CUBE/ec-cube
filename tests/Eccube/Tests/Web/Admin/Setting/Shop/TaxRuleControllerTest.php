<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Tests\Web\Admin\Setting\Shop;

use Eccube\Common\Constant;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

class TaxRuleControllerTest extends AbstractAdminWebTestCase
{
    public function createTaxRule()
    {
        $faker = $this->getFaker();
        $TargetTaxRule = $this->app['eccube.repository.tax_rule']->newTaxRule();
        $TargetTaxRule->setTaxRate($faker->randomNumber(2));
        $now = new \DateTime();
        $TargetTaxRule->setApplyDate($now);
        $this->app['orm.em']->persist($TargetTaxRule);
        $this->app['orm.em']->flush();

        return $TargetTaxRule;
    }

    public function testRoutingAdminBasisTaxIndex()
    {
        $this->client->request(
            'GET',
            $this->app->url('admin_setting_shop_tax')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminBasisTaxEdit()
    {
        $this->client->request(
            'GET',
            $this->app->url('admin_setting_shop_tax_edit', array('id' => 1))
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminBasisTaxDelete()
    {
        $redirectUrl = $this->app->url('admin_setting_shop_tax');

        $this->client->request(
            'DELETE',
            $this->app->url('admin_setting_shop_tax_delete', array('id' => 1))
        );

        $actual = $this->client->getResponse()->isRedirect($redirectUrl);

        $this->assertSame(true, $actual);
    }

    public function testRoutingEditParam()
    {
        $this->client->request(
            'GET',
            $this->app->url('admin_setting_shop_tax_edit_param', array('id' => 1))
        );
        $redirectUrl = $this->app->url('admin_setting_shop_tax');
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));
    }

    public function testEditParam()
    {
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $taxRule = $BaseInfo->getOptionProductTaxRule();
        $newTaxRule = ($taxRule) ? 0 : 1;

        $this->client->request(
            'POST',
            $this->app->url('admin_setting_shop_tax_edit_param', array('id' => 1)),
            array(
                'tax_rule' => array(
                    '_token' => 'dummy',
                    'option_product_tax_rule' => $newTaxRule
                )
            )
        );

        $redirectUrl = $this->app->url('admin_setting_shop_tax');
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        $this->expected = $newTaxRule;
        $this->actual = $BaseInfo->getOptionProductTaxRule();
        $this->verify();
    }

    public function testEditParamFail()
    {
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $taxRule = $BaseInfo->getOptionProductTaxRule();

        $this->client->request(
            'POST',
            $this->app->url('admin_setting_shop_tax_edit_param', array('id' => 1)),
            array(
                'tax_rule' => array(
                    '_token' => 'dummy',
                    'option_product_tax_rule' => 9999
                )
            )
        );

        $redirectUrl = $this->app->url('admin_setting_shop_tax');
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        $this->expected = $taxRule;
        $this->actual = $BaseInfo->getOptionProductTaxRule();
        $this->verify();
    }

    public function testEdit()
    {
        $TaxRule = $this->createTaxRule();
        $tid = $TaxRule->getId();
        $now = new \DateTime();
        $form = array(
            '_token' => 'dummy',
            'tax_rate' => 10,
            'calc_rule' => rand(1,3),
            'apply_date' => $now->format('Y-m-d H:i')
        );

        $this->client->request(
            'POST',
            $this->app->url('admin_setting_shop_tax_edit', array('id' => $tid)),
            array(
                'tax_rule' => $form
            )
        );

        $redirectUrl = $this->app->url('admin_setting_shop_tax');
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        $this->expected = $form['tax_rate'];
        $this->actual = $TaxRule->getTaxRate();
        $this->verify();
    }

    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testEditException()
    {
        $tid = 99999;
        $form = array(
            '_token' => 'dummy',
            'tax_rate' => 10,
            'calc_rule' => rand(1,3)
        );

        $this->client->request(
            'POST',
            $this->app->url('admin_setting_shop_tax_edit', array('id' => $tid)),
            array(
                'tax_rule' => $form
            )
        );

        $redirectUrl = $this->app->url('admin_setting_shop_tax');
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        $this->expected = $form['tax_rate'];
        $TargetTaxRule = $this->app['eccube.repository.tax_rule']->find($tid);
        $this->actual = $TargetTaxRule->getTaxRate();
        $this->verify();
    }

    public function testTaxDeleteSuccess()
    {
        $TaxRule = $this->createTaxRule();

        $redirectUrl = $this->app->url('admin_setting_shop_tax');

        $this->client->request(
            'DELETE',
            $this->app->url('admin_setting_shop_tax_delete', array('id' => $TaxRule->getId()))
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        $this->actual = $TaxRule->getDelFlg();
        $this->expected = Constant::ENABLED;
        $this->verify();
    }

    public function testTaxDeleteFail()
    {
        $tid = 99999;
        $redirectUrl = $this->app->url('admin_setting_shop_tax');

        $this->client->request(
            'DELETE',
            $this->app->url('admin_setting_shop_tax_delete', array('id' => $tid))
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));
    }
}
