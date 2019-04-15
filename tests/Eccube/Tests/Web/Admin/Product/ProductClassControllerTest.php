<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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

        // 商品登録画面に移動する確認
        $crawler = $this->client->followRedirect();
        $csvExportUrl = $crawler->filter('div#edit_box__footer div p a')->selectLink('商品登録に戻る')->link()->getUri();

        $crawler = $this->client->request('GET', $csvExportUrl);
        $panelName = $crawler->filter('div#main h1 span')->text();
        $this->assertContains('商品登録', $panelName);
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

    /**
     * testProductClassSortByRank
     */
    public function testProductClassSortByRank()
    {
        /* @var $ClassCategory \Eccube\Entity\ClassCategory */
        //set 金 rank
        $ClassCategory = $this->app['eccube.repository.class_category']->findOneBy(array('name' => '金'));
        $ClassCategory->setRank(3);
        $this->app['orm.em']->persist($ClassCategory);
        $this->app['orm.em']->flush($ClassCategory);
        //set 銀 rank
        $ClassCategory = $this->app['eccube.repository.class_category']->findOneBy(array('name' => '銀'));
        $ClassCategory->setRank(2);
        $this->app['orm.em']->persist($ClassCategory);
        $this->app['orm.em']->flush($ClassCategory);
        //set プラチナ rank
        $ClassCategory = $this->app['eccube.repository.class_category']->findOneBy(array('name' => 'プラチナ'));
        $ClassCategory->setRank(1);
        $this->app['orm.em']->persist($ClassCategory);
        $this->app['orm.em']->flush($ClassCategory);
        $client = $this->client;
        $crawler = $client->request('GET', $this->app->url('admin_product_product_class', array('id' => 1)));
        $this->assertTrue($client->getResponse()->isSuccessful());
        $classCategory[] = $crawler->filter('#result_box__class_category1--0')->text();
        $classCategory[] = $crawler->filter('#result_box__class_category1--3')->text();
        $classCategory[] = $crawler->filter('#result_box__class_category1--6')->text();
        $class1  = $classCategory[0].$classCategory[1].$classCategory[2];
        //金, 銀, プラチナ sort by rank setup above.
        $this->expected = '金';
        $this->actual = $classCategory[0];
        $this->assertContains( $this->expected, $this->actual);
        $this->expected = '銀';
        $this->actual = $classCategory[1];
        $this->assertContains( $this->expected, $this->actual);
        $this->expected = 'プラチナ';
        $this->actual = $classCategory[2];
        $this->assertContains( $this->expected, $this->actual);
    }
}
