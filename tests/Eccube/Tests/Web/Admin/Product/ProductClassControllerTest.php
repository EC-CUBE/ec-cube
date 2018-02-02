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

use Eccube\Entity\BaseInfo;
use Eccube\Repository\ProductRepository;
use Eccube\Repository\TaxRuleRepository;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;
use Eccube\Entity\TaxRule;

/**
 * Class ProductClassControllerTest
 */
class ProductClassControllerTest extends AbstractProductCommonTestCase
{
    /**
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var TaxRuleRepository
     */
    protected $taxRuleRepository;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->BaseInfo = $this->container->get(BaseInfo::class);
        $this->productRepository = $this->container->get(ProductRepository::class);
        $this->taxRuleRepository = $this->container->get(TaxRuleRepository::class);
    }

    /**
     * Render test
     */
    public function testRoutingAdminProductProductClassEdit()
    {
        $Product = $this->createProduct();
        // Main
        $redirectUrl = $this->generateUrl('admin_product_product_class', ['id' => $Product->getId()]);
        $this->client->request(
            'POST',
            $this->generateUrl('admin_product_product_class_edit', ['id' => $Product->getId()])
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
        $this->BaseInfo->setOptionProductTaxRule(true);
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
            $this->generateUrl('admin_product_product_class', ['id' => $product->getId()])
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
        /**
         * @var BaseInfo $baseInfo
         */
        $this->BaseInfo->setOptionProductTaxRule(true);
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
            $this->generateUrl('admin_product_product_class', ['id' => $product->getId()])
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
        $taxRule = $this->taxRuleRepository->findBy(['Product' => $product]);

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
        $this->BaseInfo->setOptionProductTaxRule(true);
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
            $this->generateUrl('admin_product_product_class', ['id' => $product->getId()])
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
        $taxRule = $this->taxRuleRepository->findOneBy(['Product' => $product]);
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
        $this->BaseInfo->setOptionProductTaxRule(true);
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
            $this->generateUrl('admin_product_product_class', ['id' => $product->getId()])
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
        $taxRule = $this->taxRuleRepository->findOneBy(['Product' => $product]);

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
        $this->enableProductTaxRule();

        $id = 1;
        // WHEN
        // select class name
        /* @var Crawler $crawler */
        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('admin_product_product_class', ['id' => $id])
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
        $this->enableProductTaxRule();

        $id = 1;
        // WHEN
        // select class name
        /* @var Crawler $crawler */
        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('admin_product_product_class', array('id' => $id))
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
        $product = $this->productRepository->find($id);
        /* @var TaxRule $taxRule */
        $taxRule = $this->taxRuleRepository->findOneBy(array('Product' => $product));
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
        $this->BaseInfo->setOptionProductTaxRule(true);
        $id = 1;
        // WHEN
        // select class name
        /* @var Crawler $crawler */
        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('admin_product_product_class', array('id' => $id))
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
        $product = $this->productRepository->find($id);
        /* @var TaxRule $taxRule */
        $taxRule = $this->taxRuleRepository->findOneBy(array('Product' => $product));
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
        $this->enableProductTaxRule();

        $id = 1;

        /* @var Crawler $crawler */
        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('admin_product_product_class', array('id' => $id))
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
        $product = $this->productRepository->find($id);
        /* @var TaxRule $taxRule */
        $taxRule = $this->taxRuleRepository->findOneBy(array('Product' => $product));
        $this->assertNotNull($taxRule);
    }

    /**
     * Test product class edit.
     * Test when product tax rule enable.
     */
    public function testProductClassEditWhenProductTaxRuleEnableAndAddNewClass()
    {
        // GIVE
        /**
         * @var BaseInfo $baseInfo
         */
        $this->BaseInfo->setOptionProductTaxRule(true);
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
            $this->generateUrl('admin_product_product_class', array('id' => $product->getId()))
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
        $taxRule = $this->taxRuleRepository->findBy(array('Product' => $product));
        $this->assertCount(2, $taxRule);
    }

    /**
     * Test product class edit.
     * Test when product tax rule enable.
     */
    public function testProductClassEditWhenProductTaxRuleEnableAndRemoveClass()
    {
        // GIVE
        $this->BaseInfo->setOptionProductTaxRule(true);
        $id = 1;

        // WHEN
        // select class name
        /* @var Crawler $crawler */
        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('admin_product_product_class', array('id' => $id))
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
        $product = $this->productRepository->find($id);
        /* @var TaxRule $taxRule */
        $taxRule = $this->taxRuleRepository->findBy(array('Product' => $product));
        $this->assertCount(0, $taxRule);
    }

    protected function enableProductTaxRule()
    {
        $this->BaseInfo->setOptionProductTaxRule(true);
        $this->entityManager->persist($this->BaseInfo);
        $this->entityManager->flush();
    }

    /**
     * testProductClassSortByRank
     */
    public function testProductClassSortByRank()
    {
        /* @var $ClassCategory \Eccube\Entity\ClassCategory */
        //set 金 rank
        $ClassCategory = $this->classCategoryRepository->findOneBy(array('name' => '金'));
        $ClassCategory->setSortNo(3);
        $this->entityManager->persist($ClassCategory);
        $this->entityManager->flush($ClassCategory);
        //set 銀 rank
        $ClassCategory = $this->classCategoryRepository->findOneBy(array('name' => '銀'));
        $ClassCategory->setSortNo(2);
        $this->entityManager->persist($ClassCategory);
        $this->entityManager->flush($ClassCategory);
        //set プラチナ rank
        $ClassCategory = $this->classCategoryRepository->findOneBy(array('name' => 'プラチナ'));
        $ClassCategory->setSortNo(1);
        $this->entityManager->persist($ClassCategory);
        $this->entityManager->flush($ClassCategory);
        $client = $this->client;
        $crawler = $client->request('GET', $this->generateUrl('admin_product_product_class', array('id' => 1)));
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
