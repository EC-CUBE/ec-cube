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
namespace Eccube\Tests\Web\Admin\Product;

use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\TaxRule;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;

/**
 * Class ProductClassControllerTest
 */
class ProductClassControllerTest extends AbstractProductCommonTestCase
{
    /**
     * Render test
     */
    public function testRoutingAdminProductProductClassEdit()
    {
        // Before
        /* @var Application $app */
        $app = $this->app;
        $Product = $this->createProduct();

        // Main
        $redirectUrl = $app->url('admin_product_product_class', array('id' => $Product->getId()));
        $this->client->request(
            'POST',
            $app->url('admin_product_product_class_edit', array('id' => $Product->getId()))
        );

        // Then
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));
    }

    /**
     * Test product class new.
     * Test when product tax rule enable.
     * Case: Tax rule invalid.
     */
    public function testProductClassNewWhenProductTaxRuleEnableAndEditTaxRuleIsInvalid()
    {
        // GIVE
        /* @var Application $app */
        $app = $this->app;
        /**
         * @var BaseInfo $baseInfo
         */
        $baseInfo = $app['eccube.repository.base_info']->get();
        $baseInfo->setOptionProductTaxRule(Constant::ENABLED);
        $member = $this->createMember();
        $product = $this->createTestProduct($member);
        $className = $this->createClassName($member);
        $this->createClassCategory($member, $className);
        $this->createClassCategory($member, $className);

        // WHEN
        // select class name
        /* @var Crawler $crawler */
        $crawler = $this->client->request(
            'GET',
            $app->url('admin_product_product_class', array('id' => $product->getId()))
        );
        $form = $crawler->selectButton('商品規格の設定')->form();
        $form['form[class_name1]'] = $className->getId();
        $crawler = $this->client->submit($form);

        // select class category without tax
        /* @var \Symfony\Component\DomCrawler\Form $form */
        $form = $crawler->selectButton('登録')->form();
        $form['form[product_classes][0][add]']->tick();
        $form['form[product_classes][0][tax_rate]'] = -2;
        $crawler = $this->client->submit($form);

        // THEN
        // check submit
        $htmlMessage = $crawler->filter('body')->html();
        $this->assertContains('0以上でなければなりません。', $htmlMessage);
        $this->assertContains('数字と小数点のみ入力できます。', $htmlMessage);
    }

    /**
     * Test product class new.
     * Test when product tax rule enable.
     * Case: Tax rule is empty.
     */
    public function testProductClassNewWhenProductTaxRuleEnableAndEditTaxRuleIsEmpty()
    {
        // GIVE
        /* @var Application $app */
        $app = $this->app;
        /**
         * @var BaseInfo $baseInfo
         */
        $baseInfo = $app['eccube.repository.base_info']->get();
        $baseInfo->setOptionProductTaxRule(Constant::ENABLED);
        $member = $this->createMember();
        $product = $this->createTestProduct($member);
        $className = $this->createClassName($member);
        $this->createClassCategory($member, $className);
        $this->createClassCategory($member, $className);

        // WHEN
        // select class name
        /* @var Crawler $crawler */
        $crawler = $this->client->request(
            'GET',
            $app->url('admin_product_product_class', array('id' => $product->getId()))
        );
        $form = $crawler->selectButton('商品規格の設定')->form();
        $form['form[class_name1]'] = $className->getId();
        $crawler = $this->client->submit($form);

        // select class category without tax
        /* @var \Symfony\Component\DomCrawler\Form $form */
        $form = $crawler->selectButton('登録')->form();
        $form['form[product_classes][0][add]']->tick();
        $this->client->submit($form);

        // THEN
        // check submit
        $crawler = $this->client->followRedirect();
        $htmlMessage = $crawler->filter('body')->html();
        $this->assertContains('商品規格を登録しました。', $htmlMessage);

        // check database
        $taxRule = $app['eccube.repository.tax_rule']->findBy(array('Product' => $product));

        $this->assertCount(0, $taxRule);
    }

    /**
     * Test product class new.
     * Test when product tax rule enable.
     * Case: Tax rule is zero.
     */
    public function testProductClassNewWhenProductTaxRuleEnableAndEditTaxRuleIsZero()
    {
        // GIVE
        /* @var Application $app */
        $app = $this->app;
        /**
         * @var BaseInfo $baseInfo
         */
        $baseInfo = $app['eccube.repository.base_info']->get();
        $baseInfo->setOptionProductTaxRule(Constant::ENABLED);
        $member = $this->createMember();
        $product = $this->createTestProduct($member);
        $className = $this->createClassName($member);
        $this->createClassCategory($member, $className);
        $this->createClassCategory($member, $className);

        // WHEN
        // select class name
        /* @var Crawler $crawler */
        $crawler = $this->client->request(
            'GET',
            $app->url('admin_product_product_class', array('id' => $product->getId()))
        );
        $form = $crawler->selectButton('商品規格の設定')->form();
        $form['form[class_name1]'] = $className->getId();
        $crawler = $this->client->submit($form);

        // select class category with tax = 0;
        $taxRate = 0;
        /* @var \Symfony\Component\DomCrawler\Form $form */
        $form = $crawler->selectButton('登録')->form();
        $form['form[product_classes][0][add]']->tick();
        $form['form[product_classes][0][tax_rate]'] = $taxRate;
        $this->client->submit($form);

        // THEN
        // check submit
        $crawler = $this->client->followRedirect();
        $htmlMessage = $crawler->filter('body')->html();
        $this->assertContains('商品規格を登録しました。', $htmlMessage);

        // check database
        $taxRule = $app['eccube.repository.tax_rule']->findOneBy(array('Product' => $product));
        $this->assertEquals($taxRate, $taxRule->getTaxRate());
    }

    /**
     * Test product class new.
     * Test when product tax rule enable.
     * Case: Tax rule is not empty.
     */
    public function testProductClassNewWhenProductTaxRuleEnableAndEditTaxRuleIsNotEmpty()
    {
        // GIVE
        /* @var Application $app */
        $app = $this->app;
        /**
         * @var BaseInfo $baseInfo
         */
        $baseInfo = $app['eccube.repository.base_info']->get();
        $baseInfo->setOptionProductTaxRule(Constant::ENABLED);
        $member = $this->createMember();
        $product = $this->createTestProduct($member);
        $className = $this->createClassName($member);
        $this->createClassCategory($member, $className);
        $this->createClassCategory($member, $className);

        // WHEN
        // select class name
        /* @var Crawler $crawler */
        $crawler = $this->client->request(
            'GET',
            $app->url('admin_product_product_class', array('id' => $product->getId()))
        );
        $form = $crawler->selectButton('商品規格の設定')->form();
        $form['form[class_name1]'] = $className->getId();
        $crawler = $this->client->submit($form);

        // select class category without tax
        /* @var \Symfony\Component\DomCrawler\Form $form */
        $form = $crawler->selectButton('登録')->form();
        $form['form[product_classes][0][add]']->tick();
        $form['form[product_classes][0][tax_rate]'] = $this->faker->randomNumber(2);
        $this->client->submit($form);

        // THEN
        // check submit
        $crawler = $this->client->followRedirect();
        $htmlMessage = $crawler->filter('body')->html();
        $this->assertContains('商品規格を登録しました。', $htmlMessage);

        // check database
        /* @var TaxRule $taxRule */
        $taxRule = $app['eccube.repository.tax_rule']->findOneBy(array('Product' => $product));

        $this->assertEquals($form['form[product_classes][0][tax_rate]']->getValue(), $taxRule->getTaxRate());
    }

    /**
     * Test product class edit.
     * Test when product tax rule enable.
     * Case: Tax rule invalid.
     */
    public function testProductClassEditWhenProductTaxRuleEnableAndEditTaxRuleInvalid()
    {
        // GIVE
        /* @var Application $app */
        $app = $this->app;
        /**
         * @var BaseInfo $baseInfo
         */
        $baseInfo = $app['eccube.repository.base_info']->get();
        $baseInfo->setOptionProductTaxRule(Constant::ENABLED);
        $id = 1;
        // WHEN
        // select class name
        /* @var Crawler $crawler */
        $crawler = $this->client->request(
            'GET',
            $app->url('admin_product_product_class', array('id' => $id))
        );

        // edit class category with tax rate invalid
        /* @var Form $form */
        $form = $crawler->selectButton('更新')->form();
        $form['form[product_classes][0][tax_rate]'] = -1;
        $form['mode'] = 'update';
        $crawler = $this->client->submit($form);

        // THEN
        // check submit
        $htmlMessage = $crawler->filter('body')->html();
        $this->assertContains('0以上でなければなりません。', $htmlMessage);
        $this->assertContains('数字と小数点のみ入力できます。', $htmlMessage);
    }

    /**
     * Test product class edit.
     * Test when product tax rule enable.
     * Case: Tax rule is zero.
     */
    public function testProductClassEditWhenProductTaxRuleEnableAndEditTaxRuleIsZero()
    {
        // GIVE
        /* @var Application $app */
        $app = $this->app;
        /**
         * @var BaseInfo $baseInfo
         */
        $baseInfo = $app['eccube.repository.base_info']->get();
        $baseInfo->setOptionProductTaxRule(Constant::ENABLED);
        $id = 1;
        // WHEN
        // select class name
        /* @var Crawler $crawler */
        $crawler = $this->client->request(
            'GET',
            $app->url('admin_product_product_class', array('id' => $id))
        );

        // edit class category with tax = 0
        /* @var Form $form */
        $form = $crawler->selectButton('更新')->form();
        $form['form[product_classes][0][tax_rate]'] = 0;
        $form['mode'] = 'update';
        $this->client->submit($form);

        // THEN
        // check submit
        $crawler = $this->client->followRedirect();
        $htmlMessage = $crawler->filter('body .container-fluid')->html();
        $this->assertContains('商品規格を更新しました。', $htmlMessage);

        // check database
        $product = $app['eccube.repository.product']->find($id);
        /* @var TaxRule $taxRule */
        $taxRule = $app['eccube.repository.tax_rule']->findOneBy(array('Product' => $product));
        $this->assertEquals(0, $taxRule->getTaxRate());
    }

    /**
     * Test product class edit.
     * Test when product tax rule enable.
     * Case: Tax rule is empty.
     */
    public function testProductClassEditWhenProductTaxRuleEnableAndEditTaxRuleIsEmpty()
    {
        // GIVE
        /* @var Application $app */
        $app = $this->app;
        /**
         * @var BaseInfo $baseInfo
         */
        $baseInfo = $app['eccube.repository.base_info']->get();
        $baseInfo->setOptionProductTaxRule(Constant::ENABLED);
        $id = 1;
        // WHEN
        // select class name
        /* @var Crawler $crawler */
        $crawler = $this->client->request(
            'GET',
            $app->url('admin_product_product_class', array('id' => $id))
        );

        // edit class category without tax
        /* @var Form $form */
        $form = $crawler->selectButton('更新')->form();
        $form['form[product_classes][0][tax_rate]'] = '';
        $form['mode'] = 'update';
        $this->client->submit($form);

        // THEN
        // check submit
        $crawler = $this->client->followRedirect();
        $htmlMessage = $crawler->filter('body .container-fluid')->html();
        $this->assertContains('商品規格を更新しました。', $htmlMessage);

        // check database
        $product = $app['eccube.repository.product']->find($id);
        /* @var TaxRule $taxRule */
        $taxRule = $app['eccube.repository.tax_rule']->findOneBy(array('Product' => $product));
        $this->assertNull($taxRule);
    }

    /**
     * Test product class edit.
     * Test when product tax rule enable.
     * Case: Tax rule is not empty.
     */
    public function testProductClassEditWhenProductTaxRuleEnableAndEditTaxRuleIsNotEmpty()
    {
        // GIVE
        /* @var Application $app */
        $app = $this->app;
        /**
         * @var BaseInfo $baseInfo
         */
        $baseInfo = $app['eccube.repository.base_info']->get();
        $baseInfo->setOptionProductTaxRule(Constant::ENABLED);
        $id = 1;

        /* @var Crawler $crawler */
        $crawler = $this->client->request(
            'GET',
            $app->url('admin_product_product_class', array('id' => $id))
        );

        /* @var Form $form */
        $form = $crawler->selectButton('更新')->form();
        $form['form[product_classes][0][tax_rate]'] = $this->faker->randomNumber(2);
        $form['mode'] = 'update';
        $this->client->submit($form);

        // THEN
        // check submit
        $crawler = $this->client->followRedirect();
        $htmlMessage = $crawler->filter('body .container-fluid')->html();
        $this->assertContains('商品規格を更新しました。', $htmlMessage);

        // check database
        $product = $app['eccube.repository.product']->find($id);
        /* @var TaxRule $taxRule */
        $taxRule = $app['eccube.repository.tax_rule']->findOneBy(array('Product' => $product));
        $this->assertEquals(0, $taxRule->getDelFlg());
    }

    /**
     * Test product class edit.
     * Test when product tax rule enable.
     */
    public function testProductClassEditWhenProductTaxRuleEnableAndAddNewClass()
    {
        // GIVE
        /* @var Application $app */
        $app = $this->app;
        /**
         * @var BaseInfo $baseInfo
         */
        $baseInfo = $app['eccube.repository.base_info']->get();
        $baseInfo->setOptionProductTaxRule(Constant::ENABLED);
        $member = $this->createMember();
        $product = $this->createProduct();
        // class 1
        $className1 = $this->createClassName($member);
        $classCate1 = $this->createClassCategory($member, $className1);
        // class 2
        $className2 = $this->createClassName($member);
        $classCate2 = $this->createClassCategory($member, $className2);
        $this->createClassCategory($member, $className2);
        $this->createClassCategory($member, $className2);
        $this->createClassCategory($member, $className2);

        // create product class
        $this->createProductClass($member, $product, $classCate1, $classCate2);

        // WHEN
        // select class name
        /* @var Crawler $crawler */
        $crawler = $this->client->request(
            'GET',
            $app->url('admin_product_product_class', array('id' => $product->getId()))
        );

        // edit class category with tax
        /* @var Form $form */
        $form = $crawler->selectButton('更新')->form();
        $form['form[product_classes][2][add]']->tick();
        $form['form[product_classes][0][tax_rate]'] = $this->faker->randomNumber(2);
        $form['form[product_classes][2][tax_rate]'] = $this->faker->randomNumber(2);
        $form['mode'] = 'update';
        $this->client->submit($form);

        // THEN
        // check submit
        $crawler = $this->client->followRedirect();
        $htmlMessage = $crawler->filter('body .container-fluid')->html();
        $this->assertContains('商品規格を更新しました。', $htmlMessage);

        // check database
        /* @var TaxRule $taxRule */
        $taxRule = $app['eccube.repository.tax_rule']->findBy(array('Product' => $product));
        $this->assertCount(2, $taxRule);
    }

    /**
     * Test product class edit.
     * Test when product tax rule enable.
     */
    public function testProductClassEditWhenProductTaxRuleEnableAndRemoveClass()
    {
        // GIVE
        /* @var Application $app */
        $app = $this->app;
        /**
         * @var BaseInfo $baseInfo
         */
        $baseInfo = $app['eccube.repository.base_info']->get();
        $baseInfo->setOptionProductTaxRule(Constant::ENABLED);
        $id = 1;

        // WHEN
        // select class name
        /* @var Crawler $crawler */
        $crawler = $this->client->request(
            'GET',
            $app->url('admin_product_product_class', array('id' => $id))
        );

        // edit class category with tax
        /* @var Form $form */
        $form = $crawler->selectButton('更新')->form();
        $form['form[product_classes][0][add]']->untick();
        $form['mode'] = 'delete';
        $this->client->submit($form);

        // THEN
        // check submit
        $crawler = $this->client->followRedirect();
        $htmlMessage = $crawler->filter('body .container-fluid')->html();
        $this->assertContains('商品規格を削除しました。', $htmlMessage);

        // check database
        $product = $app['eccube.repository.product']->find($id);
        /* @var TaxRule $taxRule */
        $taxRule = $app['eccube.repository.tax_rule']->findBy(array('Product' => $product));
        $this->assertCount(0, $taxRule);
    }
}
