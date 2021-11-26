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

namespace Eccube\Tests\Web\Admin\Product;

use Eccube\Entity\BaseInfo;
use Eccube\Entity\Master\RoundingType;
use Eccube\Entity\TaxRule;
use Eccube\Repository\ClassCategoryRepository;
use Eccube\Repository\ProductRepository;
use Eccube\Repository\TaxRuleRepository;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;

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
     * @var ClassCategoryRepository
     */
    protected $classCategoryRepository;

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

        $this->BaseInfo = $this->entityManager->find(BaseInfo::class, 1);
        $this->productRepository = $this->entityManager->getRepository(\Eccube\Entity\Product::class);
        $this->taxRuleRepository = $this->entityManager->getRepository(\Eccube\Entity\TaxRule::class);
        $this->classCategoryRepository = $this->entityManager->getRepository(\Eccube\Entity\ClassCategory::class);
    }

    /**
     * 規格あり商品の初期表示
     */
    public function testRoutingProductClass()
    {
        $Product = $this->createProduct();
        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('admin_product_product_class', ['id' => $Product->getId()])
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        // 初期化ボタンが表示されている
        $this->assertCount(1, $crawler->selectButton('商品規格の初期化'));
        // 更新ボタンが表示されている
        $this->assertCount(1, $crawler->selectButton('登録'));
    }

    /**
     * 規格なし商品の初期表示
     */
    public function testRoutingNonProductClass()
    {
        $Product = $this->createProduct(null, 0);
        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('admin_product_product_class', ['id' => $Product->getId()])
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        // 設定ボタンが表示されている
        $this->assertCount(1, $crawler->selectButton('商品規格の設定'));
        // 登録ボタンは表示されていない
        $this->assertCount(0, $crawler->selectButton('登録'));
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
        $form['product_class_matrix[class_name1]'] = $className->getId();
        $crawler = $this->client->submit($form);
        // select class category without tax
        /* @var \Symfony\Component\DomCrawler\Form $form */
        $form = $crawler->selectButton('登録')->form();
        $form['product_class_matrix[product_classes][0][checked]']->tick();
        $form['product_class_matrix[product_classes][0][tax_rate]'] = -2;
        $crawler = $this->client->submit($form);

        // THEN
        // check submit
        $htmlMessage = $crawler->filter('body')->html();
        // FIXME 以下のメッセージが翻訳されない
        // https://github.com/symfony/validator/blob/4.4/Resources/translations/validators.ja.xlf#L366
        // $this->assertContains('0以上でなければなりません。', $htmlMessage);
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
        /*
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
        $form['product_class_matrix[class_name1]'] = $className->getId();
        $crawler = $this->client->submit($form);

        // select class category without tax
        /* @var \Symfony\Component\DomCrawler\Form $form */
        $form = $crawler->selectButton('登録')->form();
        $form['product_class_matrix[product_classes][0][checked]']->tick();
        $form['product_class_matrix[product_classes][0][stock]'] = 1;
        $form['product_class_matrix[product_classes][0][price02]'] = 1;

        $this->client->submit($form);

        // THEN
        // check submit
        $crawler = $this->client->followRedirect();
        $htmlMessage = $crawler->filter('body')->html();
        $this->assertContains('保存しました', $htmlMessage);

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
        $form['product_class_matrix[class_name1]'] = $className->getId();
        $crawler = $this->client->submit($form);

        // select class category with tax = 0;
        $taxRate = 0;
        /* @var \Symfony\Component\DomCrawler\Form $form */
        $form = $crawler->selectButton('登録')->form();
        $form['product_class_matrix[product_classes][0][checked]']->tick();
        $form['product_class_matrix[product_classes][0][stock]'] = 1;
        $form['product_class_matrix[product_classes][0][price02]'] = 1;
        $form['product_class_matrix[product_classes][0][tax_rate]'] = $taxRate;
        $this->client->submit($form);

        // THEN
        // check submit
        $crawler = $this->client->followRedirect();
        $htmlMessage = $crawler->filter('body')->html();
        $this->assertContains('保存しました', $htmlMessage);

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
        $form['product_class_matrix[class_name1]'] = $className->getId();
        $crawler = $this->client->submit($form);

        // select class category without tax
        /* @var \Symfony\Component\DomCrawler\Form $form */
        $form = $crawler->selectButton('登録')->form();
        $form['product_class_matrix[product_classes][0][checked]']->tick();
        $form['product_class_matrix[product_classes][0][stock]'] = 1;
        $form['product_class_matrix[product_classes][0][price02]'] = 1;
        $form['product_class_matrix[product_classes][0][tax_rate]'] = $this->faker->randomNumber(2);
        $this->client->submit($form);

        // THEN
        // check submit
        $crawler = $this->client->followRedirect();
        $htmlMessage = $crawler->filter('body')->html();
        $this->assertContains('保存しました', $htmlMessage);

        // check database
        /* @var TaxRule $taxRule */
        $taxRule = $this->taxRuleRepository->findOneBy(['Product' => $product]);

        $this->assertEquals($form['product_class_matrix[product_classes][0][tax_rate]']->getValue(), $taxRule->getTaxRate());
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
        $form = $crawler->selectButton('登録')->form();
        $form['product_class_matrix[product_classes][0][checked]']->tick();
        $form['product_class_matrix[product_classes][0][stock]'] = 1;
        $form['product_class_matrix[product_classes][0][price02]'] = 1;
        $form['product_class_matrix[product_classes][0][tax_rate]'] = -1;
        $crawler = $this->client->submit($form);

        // THEN
        // check submit
        $htmlMessage = $crawler->filter('body')->html();
        // FIXME 以下のメッセージが翻訳されない
        // https://github.com/symfony/validator/blob/4.4/Resources/translations/validators.ja.xlf#L366
        // $this->assertContains('0以上でなければなりません。', $htmlMessage);
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
            $this->generateUrl('admin_product_product_class', ['id' => $id])
        );

        // edit class category with tax = 0
        /* @var Form $form */
        $form = $crawler->selectButton('登録')->form();
        $form['product_class_matrix[product_classes][0][checked]']->tick();
        $form['product_class_matrix[product_classes][0][stock]'] = 1;
        $form['product_class_matrix[product_classes][0][price02]'] = 1;
        $form['product_class_matrix[product_classes][0][tax_rate]'] = 0;
        $this->client->submit($form);

        // THEN
        // check submit
        $crawler = $this->client->followRedirect();
        $htmlMessage = $crawler->filter('body .c-contentsArea')->html();
        $this->assertContains('保存しました', $htmlMessage);

        // check database
        $product = $this->productRepository->find($id);
        /* @var TaxRule $taxRule */
        $taxRule = $this->taxRuleRepository->findOneBy(['Product' => $product]);
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
            $this->generateUrl('admin_product_product_class', ['id' => $id])
        );

        // edit class category without tax
        /* @var Form $form */
        $form = $crawler->selectButton('登録')->form();
        $form['product_class_matrix[product_classes][0][checked]']->tick();
        $form['product_class_matrix[product_classes][0][stock]'] = 1;
        $form['product_class_matrix[product_classes][0][price02]'] = 1;
        $form['product_class_matrix[product_classes][0][tax_rate]'] = '';
        $this->client->submit($form);

        // THEN
        // check submit
        $crawler = $this->client->followRedirect();
        $htmlMessage = $crawler->filter('body .c-contentsArea')->html();
        $this->assertContains('保存しました', $htmlMessage);

        // check database
        $product = $this->productRepository->find($id);
        /* @var TaxRule $taxRule */
        $taxRule = $this->taxRuleRepository->findOneBy(['Product' => $product]);
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
            $this->generateUrl('admin_product_product_class', ['id' => $id])
        );

        /* @var Form $form */
        $form = $crawler->selectButton('登録')->form();
        $form['product_class_matrix[product_classes][0][checked]']->tick();
        $form['product_class_matrix[product_classes][0][stock]'] = 1;
        $form['product_class_matrix[product_classes][0][price02]'] = 1;
        $form['product_class_matrix[product_classes][0][tax_rate]'] = $this->faker->randomNumber(2);
        $this->client->submit($form);

        // THEN
        // check submit
        $crawler = $this->client->followRedirect();
        $htmlMessage = $crawler->filter('body .c-contentsArea')->html();
        $this->assertContains('保存しました', $htmlMessage);

        // check database
        $product = $this->productRepository->find($id);
        /* @var TaxRule $taxRule */
        $taxRule = $this->taxRuleRepository->findOneBy(['Product' => $product]);
        $this->assertNotNull($taxRule);
    }

    /**
     * Test product class edit.
     * Test when product tax rule enable.
     */
    public function testProductClassEditWhenProductTaxRuleEnableAndAddNewClass()
    {
        // GIVE
        /*
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
            $this->generateUrl('admin_product_product_class', ['id' => $product->getId()])
        );

        // edit class category with tax
        /* @var Form $form */
        $form = $crawler->selectButton('登録')->form();
        $form['product_class_matrix[product_classes][2][checked]']->tick();
        $form['product_class_matrix[product_classes][2][stock]'] = 1;
        $form['product_class_matrix[product_classes][2][price02]'] = 1;
        $form['product_class_matrix[product_classes][2][tax_rate]'] = $this->faker->randomNumber(2);
        $form['product_class_matrix[product_classes][0][tax_rate]'] = $this->faker->randomNumber(2);

        $this->client->submit($form);

        // THEN
        // check submit
        $crawler = $this->client->followRedirect();
        $htmlMessage = $crawler->filter('body .c-contentsArea')->html();
        $this->assertContains('保存しました', $htmlMessage);

        // check database
        /* @var TaxRule $taxRule */
        $taxRule = $this->taxRuleRepository->findBy(['Product' => $product]);
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
            $this->generateUrl('admin_product_product_class', ['id' => $id])
        );

        // edit class category with tax
        /* @var Form $form */
        $form = $crawler->selectButton('登録')->form();
        $form['product_class_matrix[product_classes][0][checked]']->untick();
        $this->client->submit($form);

        // THEN
        // check submit

        $crawler = $this->client->followRedirect();
        $htmlMessage = $crawler->filter('body .c-contentsArea')->html();
        $this->assertContains('保存しました', $htmlMessage);
        // check database
        $product = $this->productRepository->find($id);
        /* @var TaxRule $taxRule */
        $taxRule = $this->taxRuleRepository->findBy(['Product' => $product]);
        $this->assertCount(0, $taxRule);
    }

    /**
     * 個別税率設定をした場合に現在適用されている丸め規則が設定される
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/2114
     */
    public function testProductClassEditWhenProductTaxRuleEnableAndCurrentRoundingType()
    {
        // GIVE
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

        $TaxRule = $this->taxRuleRepository->newTaxRule();
        $TaxRule->setApplyDate(new \DateTime('-1 days'))
            ->setRoundingType($this->entityManager->find(RoundingType::class, RoundingType::CEIL));
        $this->entityManager->persist($TaxRule);
        $this->entityManager->flush($TaxRule);

        $id = $product->getId();

        // WHEN
        // select class name
        /* @var Crawler $crawler */
        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('admin_product_product_class', ['id' => $id])
        );

        // edit class category with tax
        /* @var Form $form */
        $form = $crawler->selectButton('登録')->form();
        $form['product_class_matrix[product_classes][2][checked]']->tick();
        $form['product_class_matrix[product_classes][2][stock]'] = 1;
        $form['product_class_matrix[product_classes][2][price02]'] = 1;
        $form['product_class_matrix[product_classes][2][tax_rate]'] = $this->faker->randomNumber(2);
        $this->client->submit($form);

        // THEN
        // check submit

        $crawler = $this->client->followRedirect();
        $htmlMessage = $crawler->filter('body .c-contentsArea')->html();
        $this->assertContains('保存しました', $htmlMessage);
        // check database
        $product = $this->productRepository->find($id);
        /* @var ProductTaxRule $taxRule */
        $ProductTaxRule = $this->taxRuleRepository->findOneBy(['Product' => $product]);

        $this->expected = RoundingType::CEIL;
        $this->actual = $ProductTaxRule->getRoundingType()->getId();
        $this->verify();
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
        //set チョコ rank
        $ClassCategory = $this->classCategoryRepository->findOneBy(['name' => 'チョコ']);
        $ClassCategory->setSortNo(3);
        $this->entityManager->persist($ClassCategory);
        $this->entityManager->flush($ClassCategory);
        //set 抹茶 rank
        $ClassCategory = $this->classCategoryRepository->findOneBy(['name' => '抹茶']);
        $ClassCategory->setSortNo(2);
        $this->entityManager->persist($ClassCategory);
        $this->entityManager->flush($ClassCategory);
        //set バニラ rank
        $ClassCategory = $this->classCategoryRepository->findOneBy(['name' => 'バニラ']);
        $ClassCategory->setSortNo(1);
        $this->entityManager->persist($ClassCategory);
        $this->entityManager->flush($ClassCategory);
        $client = $this->client;
        $crawler = $client->request('GET', $this->generateUrl('admin_product_product_class', ['id' => 1]));
        $this->assertTrue($client->getResponse()->isSuccessful());

        $classCategories = [];
        foreach ($crawler->filterXPath('//table/tr') as $i => $tr) {
            $crawler = new Crawler($tr);
            foreach ($crawler->filter('td') as $j => $td) {
                if ($j === 1) {
                    $classCategories[] = trim($td->nodeValue);
                }
            }
        }

        //チョコ, 抹茶, バニラ sort by rank setup above.
        $this->expected = 'チョコ';
        $this->actual = $classCategories[1];
        $this->assertContains($this->expected, $this->actual);
        $this->expected = '抹茶';
        $this->actual = $classCategories[4];
        $this->assertContains($this->expected, $this->actual);
        $this->expected = 'バニラ';
        $this->actual = $classCategories[7];
        $this->assertContains($this->expected, $this->actual);
    }
}
