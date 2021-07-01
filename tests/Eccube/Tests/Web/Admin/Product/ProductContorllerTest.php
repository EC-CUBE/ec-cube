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

use Eccube\Common\Constant;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\Master\ProductStatus;
use Eccube\Entity\Master\RoundingType;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Entity\ProductImage;
use Eccube\Entity\ProductTag;
use Eccube\Entity\Tag;
use Eccube\Entity\TaxRule;
use Eccube\Repository\Master\ProductStatusRepository;
use Eccube\Repository\ProductRepository;
use Eccube\Repository\ProductTagRepository;
use Eccube\Repository\TaxRuleRepository;
use Eccube\Tests\Fixture\Generator;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Eccube\Util\StringUtil;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class ProductControllerTest extends AbstractAdminWebTestCase
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var ProductTagRepository
     */
    protected $productTagRepository;
    /**
     * @var BaseInfo
     */
    protected $baseInfo;

    /**
     * @var TaxRuleRepository
     */
    protected $taxRuleRepository;

    /**
     * @var ProductStatusRepository
     */
    protected $productStatusRepository;

    /**
     * @var string
     */
    protected $imageDir;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->productRepository = $this->entityManager->getRepository(\Eccube\Entity\Product::class);
        $this->baseInfo = $this->entityManager->find(BaseInfo::class, 1);
        $this->taxRuleRepository = $this->entityManager->getRepository(\Eccube\Entity\TaxRule::class);
        $this->productStatusRepository = $this->entityManager->getRepository(\Eccube\Entity\Master\ProductStatus::class);
        $this->productTagRepository = $this->entityManager->getRepository(\Eccube\Entity\ProductTag::class);

        // 検索時, IDの重複を防ぐため事前に10個生成しておく
        for ($i = 0; $i < 10; $i++) {
            $this->createProduct();
        }

        $this->imageDir = sys_get_temp_dir().'/'.sha1(mt_rand());
        $fs = new Filesystem();
        $fs->mkdir($this->imageDir);
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        $fs = new Filesystem();
        $fs->remove($this->imageDir);
        parent::tearDown();
    }

    public function createFormData()
    {
        $faker = $this->getFaker();

        $price01 = $faker->randomNumber(5);
        if (mt_rand(0, 1)) {
            $price01 = number_format($price01);
        }

        $price02 = $faker->randomNumber(5);
        if (mt_rand(0, 1)) {
            $price02 = number_format($price02);
        }

        $form = [
            'class' => [
                'sale_type' => 1,
                'price01' => $price01,
                'price02' => $price02,
                'stock' => $faker->randomNumber(3),
                'stock_unlimited' => 0,
                'code' => $faker->word,
                'sale_limit' => null,
                'delivery_duration' => '',
            ],
            'name' => $faker->word,
            'product_image' => [],
            'description_detail' => $faker->realText,
            'description_list' => $faker->paragraph,
            'Category' => null,
            'Tag' => [1],
            'search_word' => $faker->word,
            'free_area' => $faker->realText,
            'Status' => 1,
            'note' => $faker->realText,
            'tags' => null,
            'images' => null,
            'add_images' => null,
            'delete_images' => null,
            Constant::TOKEN_NAME => 'dummy',
        ];

        return $form;
    }

    public function testRoutingAdminProductProduct()
    {
        $this->client->request('GET', $this->generateUrl('admin_product'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminProductProductNew()
    {
        $this->client->request('GET', $this->generateUrl('admin_product_product_new'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testProductSearchAll()
    {
        $AllProducts = $this->productRepository->findAll();
        $cnt = count($AllProducts);
        $this->createProduct();
        $cnt++;

        $post = [
            'admin_search_product' => [
                Constant::TOKEN_NAME => 'dummy',
                'id' => '',
                'category_id' => '',
                'create_date_start' => '',
                'create_date_end' => '',
                'update_date_start' => '',
                'update_date_end' => '',
            ],
        ];

        $crawler = $this->client->request('POST', $this->generateUrl('admin_product'), $post);

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->expected = '検索結果：'.$cnt.'件が該当しました';
        $this->actual = $crawler->filter('div.c-outsideBlock__contents.mb-5 > span')->text();
        $this->verify('検索結果件数の確認テスト');

        // デフォルトの表示件数確認テスト
        $this->expected = '50件';
        $this->actual = $crawler->filter('select.custom-select > option:selected')->text();
        $this->verify('デフォルトの表示件数確認テスト');

        // 表示件数100件テスト
        $crawler = $this->client->request('GET', $this->generateUrl('admin_product_page', ['page_no' => 1]), ['page_count' => 100]);
        $this->expected = '100件';
        $this->actual = $crawler->filter('select.custom-select > option:selected')->text();
        $this->verify('表示件数100件テスト');

        // 表示件数入力値は正しくない場合はデフォルトの表示件数になるテスト
        $crawler = $this->client->request('GET', $this->generateUrl('admin_product_page', ['page_no' => 1]), ['page_count' => 999999]);
        $this->expected = '検索結果：13件が該当しました';
        $this->actual = $crawler->filter('#search_form > div:nth-child(4) > span')->text();
        $this->verify('表示件数入力値は正しくない場合はデフォルトの表示件数になるテスト');

        // 表示件数はSESSIONから取得するテスト
        $crawler = $this->client->request('GET', $this->generateUrl('admin_product_page', ['page_no' => 1]), ['status' => 1]);
        $this->expected = '100件';
        $this->actual = $crawler->filter('select.custom-select > option:selected')->text();
        $this->verify('表示件数はSESSIONから取得するテスト');
    }

    public function testProductSearchByName()
    {
        $TestProduct = $this->createProduct();
        $TestProduct->setName(StringUtil::random());
        $this->entityManager->persist($TestProduct);
        $this->entityManager->flush();

        $post = [
            'admin_search_product' => [
                Constant::TOKEN_NAME => 'dummy',
                'id' => $TestProduct->getName(),
                'category_id' => '',
                'create_date_start' => '',
                'create_date_end' => '',
                'update_date_start' => '',
                'update_date_end' => '',
            ],
        ];

        $crawler = $this->client->request('POST', $this->generateUrl('admin_product'), $post);
        $this->expected = '検索結果：1件が該当しました';
        $this->actual = $crawler->filter('div.c-outsideBlock__contents.mb-5 > span')->text();
        $this->verify('検索結果件数の確認テスト');

        // デフォルトの表示件数確認テスト
        $this->expected = '50件';
        $this->actual = $crawler->filter('select.custom-select > option:selected')->text();
        $this->verify('デフォルトの表示件数確認テスト');

        // 表示件数100件テスト
        $crawler = $this->client->request('GET', $this->generateUrl('admin_product_page', ['page_no' => 1]), ['page_count' => 100]);
        $this->expected = '100件';
        $this->actual = $crawler->filter('select.custom-select > option:selected')->text();
        $this->verify('表示件数100件テスト');

        // 表示件数入力値は正しくない場合はデフォルトの表示件数になるテスト
        $crawler = $this->client->request('GET', $this->generateUrl('admin_product_page', ['page_no' => 1]), ['page_count' => 999999]);
        $this->expected = '検索結果：1件が該当しました';
        $this->actual = $crawler->filter('#search_form > div:nth-child(4) > span')->text();
        $this->verify('表示件数入力値は正しくない場合はデフォルトの表示件数になるテスト');

        // 表示件数はSESSIONから取得するテスト
        $crawler = $this->client->request('GET', $this->generateUrl('admin_product_page', ['page_no' => 1]), ['status' => 1]);
        $this->expected = '100件';
        $this->actual = $crawler->filter('select.custom-select > option:selected')->text();
        $this->verify('表示件数はSESSIONから取得するテスト');
    }

    public function testProductSearchById()
    {
        $TestProduct = $this->createProduct();

        $post = [
            'admin_search_product' => [
                Constant::TOKEN_NAME => 'dummy',
                'id' => $TestProduct->getId(),
                'category_id' => '',
                'create_date_start' => '',
                'create_date_end' => '',
                'update_date_start' => '',
                'update_date_end' => '',
            ],
        ];

        $crawler = $this->client->request('POST', $this->generateUrl('admin_product'), $post);
        $this->expected = '検索結果：1件が該当しました';
        $this->actual = $crawler->filter('div.c-outsideBlock__contents.mb-5 > span')->text();
        $this->verify('検索結果件数の確認テスト');

        // デフォルトの表示件数確認テスト
        $this->expected = '50件';
        $this->actual = $crawler->filter('select.custom-select > option:selected')->text();
        $this->verify('デフォルトの表示件数確認テスト');

        // 表示件数100件テスト
        $crawler = $this->client->request('GET', $this->generateUrl('admin_product_page', ['page_no' => 1]), ['page_count' => 100]);
        $this->expected = '100件';
        $this->actual = $crawler->filter('select.custom-select > option:selected')->text();
        $this->verify();

        // 表示件数入力値は正しくない場合はデフォルトのの表示件数になるテスト
        $crawler = $this->client->request('GET', $this->generateUrl('admin_product_page', ['page_no' => 1]), ['page_count' => 999999]);
        $this->expected = '検索結果：1件が該当しました';
        $this->actual = $crawler->filter('#search_form > div:nth-child(4) > span')->text();
        $this->verify();

        // 表示件数はSESSIONから取得するテスト
        $crawler = $this->client->request('GET', $this->generateUrl('admin_product_page', ['page_no' => 1]), ['status' => 1]);

        $this->expected = '100件';
        $this->actual = $crawler->filter('select.custom-select > option:selected')->text();
        $this->verify();
    }

    public function testProductSearchByIdZero()
    {
        $this->createProduct();

        $post = [
            'admin_search_product' => [
                Constant::TOKEN_NAME => 'dummy',
                'id' => 99999999,
                'category_id' => '',
                'create_date_start' => '',
                'create_date_end' => '',
                'update_date_start' => '',
                'update_date_end' => '',
            ],
        ];

        $crawler = $this->client->request('POST', $this->generateUrl('admin_product'), $post);
        $this->expected = '検索条件に合致するデータが見つかりませんでした';
        $this->actual = $crawler->filter('div.text-center.text-muted.mb-4.h5')->text();
        $this->verify();
    }

    public function testProductSearchByNameZero()
    {
        $this->createProduct();

        $post = [
            'admin_search_product' => [
                Constant::TOKEN_NAME => 'dummy',
                'id' => 'not Exists product name',
                'category_id' => '',
                'create_date_start' => '',
                'create_date_end' => '',
                'update_date_start' => '',
                'update_date_end' => '',
            ],
        ];

        $crawler = $this->client->request('POST', $this->generateUrl('admin_product'), $post);
        $this->expected = '検索条件に合致するデータが見つかりませんでした';
        $this->actual = $crawler->filter('div.text-center.text-muted.mb-4.h5')->text();
        $this->verify();
    }

    public function testRoutingAdminProductProductEdit()
    {
        $TestProduct = $this->createProduct();

        $id = $this->productRepository
            ->findOneBy(['name' => $TestProduct->getName()])
            ->getId();

        $this->client->request('GET', $this->generateUrl('admin_product_product_edit', ['id' => $id]));

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testEditWithPost()
    {
        $Product = $this->createProduct(null, 0);
        $formData = $this->createFormData();

        $this->client->request(
            'POST',
            $this->generateUrl('admin_product_product_edit', ['id' => $Product->getId()]),
            ['admin_product' => $formData]
        );

        $rUrl = $this->generateUrl('admin_product_product_edit', ['id' => $Product->getId()]);
        $this->assertTrue($this->client->getResponse()->isRedirect($rUrl));

        // 編集前の更新日時を取得
        /** @var Product $PreProduct */
        $PreProduct = $this->productRepository->findOneBy(['id' => $Product->getId()]);
        $PreUpdateDate = $PreProduct->getUpdateDate();
        $preTimestamp = $PreUpdateDate->getTimestamp();

        // タイムスタンプが変わっていることを確認するために3秒待って更新
        sleep(3);

        $formData['return_link'] = $this->generateUrl('admin_product_category');
        $this->client->request(
            'POST',
            $this->generateUrl('admin_product_product_edit', ['id' => $Product->getId()]),
            ['admin_product' => $formData]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($formData['return_link']));

        $EditedProduct = $this->productRepository->find($Product->getId());
        $this->expected = $formData['name'];
        $this->actual = $EditedProduct->getName();
        $this->verify();

        // 商品の更新日時が更新されているか確認
        /** @var \DateTime $EditedUpdateDate */
        $EditedUpdateDate = $EditedProduct->getUpdateDate();
        $editedTimestamp = $EditedUpdateDate->getTimestamp();

        $this->assertNotSame($preTimestamp, $editedTimestamp);
    }

    public function testDisplayProduct()
    {
        $productClassNum = 0;
        $Product = $this->createProduct('Test', $productClassNum);
        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('admin_product_product_edit', ['id' => $Product->getId()])
        );

        // Only have 1 div button
        $this->expected = 1;
        $this->actual = $crawler->filter('#standardConfig > div > div')->count();
        $this->verify();
    }

    public function testDisplayProductHasClass()
    {
        $productClassNum = 3;
        $Product = $this->createProduct('Test', $productClassNum);
        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('admin_product_product_edit', ['id' => $Product->getId()])
        );

        $expected = '規格1';
        $actual = $crawler->filter('#standardConfig > div > table')->text();
        $this->assertContains($expected, $actual);

        $this->expected = $productClassNum;
        $this->actual = $crawler->filter('#standardConfig > div > table > tbody > tr')->count();
        $this->verify();
    }

    public function testDelete()
    {
        $Product = $this->createProduct();

        $Tag = new Tag();
        $Tag->setName('Tag-102')->setSortNo(999);
        $this->entityManager->persist($Tag);

        $ProductTag = new ProductTag();
        $ProductTag->setProduct($Product);
        $ProductTag->setTag($Tag);
        $this->entityManager->persist($ProductTag);

        $Product->addProductTag($ProductTag);
        $this->entityManager->persist($Product);
        $this->entityManager->flush();

        $params = [
            'id' => $Product->getId(),
            Constant::TOKEN_NAME => 'dummy',
        ];

        $productTagId = $Product->getProductTag()->first()->getId();

        $this->client->request('DELETE', $this->generateUrl('admin_product_product_delete', $params));

        $rUrl = $this->generateUrl('admin_product_page', ['page_no' => 1]).'?resume=1';

        $this->assertTrue($this->client->getResponse()->isRedirect($rUrl));

        $this->assertNull($this->productRepository->find($params['id']));

        $this->assertNull($this->productTagRepository->find($productTagId));
    }

    public function testCopy()
    {
        $Product = $this->createProduct();
        $AllProducts = $this->productRepository->findAll();
        $params = [
            'id' => $Product->getId(),
            Constant::TOKEN_NAME => 'dummy',
        ];

        $this->client->request('POST', $this->generateUrl('admin_product_product_copy', $params));

        $this->assertTrue($this->client->getResponse()->isRedirect());

        $AllProducts2 = $this->productRepository->findAll();
        $this->expected = count($AllProducts) + 1;
        $this->actual = count($AllProducts2);
        $this->verify();
    }

    /**
     * @param $taxRate
     * @param $expected
     * @dataProvider dataNewProductProvider
     */
    public function testNewWithPostTaxRate($taxRate, $expected)
    {
        // Give
        $this->baseInfo->setOptionProductTaxRule(true);
        $formData = $this->createFormData();

        $formData['class']['tax_rate'] = $taxRate;
        // When
        $this->client->request(
            'POST',
            $this->generateUrl('admin_product_product_new'),
            ['admin_product' => $formData]
        );

        // Then
        $this->assertTrue($this->client->getResponse()->isRedirection());

        $arrTmp = explode('/', $this->client->getResponse()->getTargetUrl());
        $productId = $arrTmp[count($arrTmp) - 2];
        $Product = $this->productRepository->find($productId);

        $this->expected = $expected;
        $Taxrule = $this->taxRuleRepository->findOneBy(['Product' => $Product]);
        $taxRate = is_null($taxRate) ? null : $Taxrule->getTaxRate();
        $this->actual = $taxRate;
        $this->assertTrue($this->actual === $this->expected);
    }

    /**
     * Test search + export product no stock
     */
    public function testExportWithFilterNoStock()
    {
        $this->expectOutputRegex('/Product with stock 01/');
        $testProduct = $this->createProduct('Product with stock 01');
        $this->createProduct('Product with stock 02', 1);
        /** @var $ProductClass ProductClass */
        $ProductClass = $testProduct->getProductClasses()->first();
        $ProductClass->setStock(0);
        $ProductClass->getProductStock()->setStock(0);
        $this->entityManager->flush();

        $searchForm = $this->createSearchForm();

        $searchForm['id'] = 'Product with stock';

        /* @var $crawler Crawler*/
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_product'),
            ['admin_search_product' => $searchForm]
        );
        $this->expected = '検索結果：2件が該当しました';
        $this->actual = $crawler->filter('div.c-outsideBlock__contents.mb-5 > span')->text();
        $this->verify('検索結果件数の確認テスト');

        // TODO
        $this->markTestIncomplete('検索項目(公開・非公開・在庫内)の実装完了後に実施');

        // No stock click button
        $noStockUrl = $crawler->selectLink('在庫なし')->link()->getUri();
        $crawler = $this->client->request('GET', $noStockUrl);
        $this->expected = '検索結果 1 件 が該当しました';
        $this->actual = $crawler->filter('div.c-outsideBlock__contents.mb-5 > span')->text();
        $this->verify();

        $csvExportUrl = $crawler->filter('ul.dropdown-menu')->selectLink('CSVダウンロード')->link()->getUri();
        $this->client->request('GET', $csvExportUrl);
    }

    /**
     * Test search + export product with filter private.
     */
    public function testExportWithFilterPrivate()
    {
        $this->expectOutputRegex('/Product with status 01/');
        $testProduct = $this->createProduct('Product with status 01', 0);
        $this->createProduct('Product with status 02', 1);
        $display = $this->productStatusRepository->find(ProductStatus::DISPLAY_HIDE);
        $testProduct->setStatus($display);
        $this->entityManager->flush();

        $searchForm = $this->createSearchForm();
        $searchForm['id'] = 'Product with status';

        /* @var $crawler Crawler*/
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_product'),
            ['admin_search_product' => $searchForm]
        );
        $this->expected = '検索結果：2件が該当しました';
        $this->actual = $crawler->filter('div.c-outsideBlock__contents.mb-5 > span')->text();
        $this->verify('検索結果件数の確認テスト');

        // TODO
        $this->markTestIncomplete('検索項目(公開・非公開・在庫内)の実装完了後に実施');

        // private click button
        $privateUrl = $crawler->selectLink('非公開')->link()->getUri();
        $crawler = $this->client->request('GET', $privateUrl);
        $this->expected = '検索結果 1 件 が該当しました';
        $this->actual = $crawler->filter('div.c-outsideBlock__contents.mb-5 > span')->text();
        $this->verify();

        $csvExportUrl = $crawler->filter('ul.dropdown-menu')->selectLink('CSVダウンロード')->link()->getUri();
        $this->client->request('GET', $csvExportUrl);
    }

    /**
     * Test search + export product with filter public.
     */
    public function testExportWithFilterPublic()
    {
        $this->expectOutputRegex('/[Product with status 01]{1}/');
        $this->createProduct('Product with status 01', 0);
        $testProduct02 = $this->createProduct('Product with status 02', 1);
        $display = $this->productStatusRepository->find(ProductStatus::DISPLAY_HIDE);
        $testProduct02->setStatus($display);
        $this->entityManager->flush();

        $searchForm = $this->createSearchForm();
        $searchForm['id'] = 'Product with status';

        /* @var $crawler Crawler*/
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_product'),
            ['admin_search_product' => $searchForm]
        );
        $this->expected = '検索結果：2件が該当しました';
        $this->actual = $crawler->filter('div.c-outsideBlock__contents.mb-5 > span')->text();
        $this->verify('検索結果件数の確認テスト');

        // TODO
        $this->markTestIncomplete('検索項目(公開・非公開・在庫内)の実装完了後に実施');

        // public click button
        $privateUrl = $crawler->selectLink('公開')->link()->getUri();
        $crawler = $this->client->request('GET', $privateUrl);
        $this->expected = '検索結果 1 件 が該当しました';
        $this->actual = $crawler->filter('div.c-outsideBlock__contents.mb-5 > span')->text();
        $this->verify();

        $csvExportUrl = $crawler->filter('ul.dropdown-menu')->selectLink('CSVダウンロード')->link()->getUri();
        $this->client->request('GET', $csvExportUrl);
    }

    /**
     * Test search + export product with all
     */
    public function testExportWithAll()
    {
        $this->markTestIncomplete('FIXME expectOutputRegex');
        $this->expectOutputRegex('/[Product with status]{1}[Product with status 02]{2}/');
        $this->createProduct('Product with status 01', 0);
        $testProduct02 = $this->createProduct('Product with status 02', 1);
        $display = $this->productStatusRepository->find(ProductStatus::DISPLAY_HIDE);
        $testProduct02->setStatus($display);
        $this->entityManager->flush();

        $searchForm = $this->createSearchForm();
        $searchForm['id'] = 'Product with status';

        /* @var $crawler Crawler*/
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_product'),
            ['admin_search_product' => $searchForm]
        );
        $this->expected = '検索結果：2件が該当しました';
        $this->actual = $crawler->filter('div.c-outsideBlock__contents.mb-5 > span')->text();
        $this->verify('検索結果件数の確認テスト');

        // TODO
        $this->markTestIncomplete('検索項目(公開・非公開・在庫内)の実装完了後に実施');

        // private click button
        $privateUrl = $crawler->selectLink('非公開')->link()->getUri();
        $crawler = $this->client->request('GET', $privateUrl);
        $this->expected = '検索結果 1 件 が該当しました';
        $this->actual = $crawler->filter('div.c-outsideBlock__contents.mb-5 > span')->text();
        $this->verify();

        $csvExportUrl = $crawler->filter('ul.dropdown-menu')->selectLink('CSVダウンロード')->link()->getUri();
        $this->client->request('GET', $csvExportUrl);
    }

    public function dataNewProductProvider()
    {
        return [
            [null, null],
            ['0', '0'],
            ['1', '1'],
        ];
    }

    /**
     * 個別税率設定のテストケース
     * 個別税率設定を有効にし、商品編集時に更新されることを確認する
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/1547
     *
     * @param string|null $before 更新前の税率
     * @param string|null $after POST値
     * @param string|null $expected 期待値
     *
     * @dataProvider dataEditProductProvider
     */
    public function testEditWithPostTaxRate($before, $after, $expected)
    {
        // Give
        $this->baseInfo->setOptionProductTaxRule(true);
        $Product = $this->createProduct(null, 0);
        $ProductClasses = $Product->getProductClasses();
        $ProductClass = $ProductClasses[0];
        $formData = $this->createFormData();

        if ($after !== null) {
            $formData['class']['tax_rate'] = $after;
        }
        if ($before !== null) {
            $RoundingType = $this->entityManager->find(RoundingType::class, RoundingType::ROUND);
            $TaxRule = new TaxRule();
            $TaxRule->setProductClass($ProductClass)
                ->setCreator($Product->getCreator())
                ->setProduct($Product)
                ->setRoundingType($RoundingType)
                ->setTaxRate($before)
                ->setTaxAdjust(0)
                ->setApplyDate(new \DateTime());
            $ProductClass->setTaxRule($TaxRule);
            $this->entityManager->persist($TaxRule);
            $this->entityManager->flush();
        }

        // When
        $this->client->request(
            'POST',
            $this->generateUrl('admin_product_product_edit', ['id' => $Product->getId()]),
            ['admin_product' => $formData]
        );

        // Then
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_product_product_edit', ['id' => $Product->getId()])));

        $this->expected = $expected;
        $TaxRule = $this->taxRuleRepository->findOneBy(['Product' => $Product, 'ProductClass' => $ProductClass]);

        if (is_null($TaxRule)) {
            $this->actual = null;
            $this->assertNull($TaxRule);
        } else {
            $this->actual = $TaxRule->getTaxRate();
        }

        $this->assertSame($this->expected, $this->actual);
    }

    /**
     * 個別税率設定をした場合の RoundingType のテストケース
     *
     * @param string|null $tax_rate 個別税率
     * @param string|null $currentRoundingTypeId 現在の RoundingType ID
     * @param string|null $expected RoundingType ID の期待値
     * @param bool $isNew 商品を新規作成の場合 true
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/2114
     *
     * @dataProvider dataEditRoundingTypeProvider
     */
    public function testEditWithCurrnetRoundingType($tax_rate, $currentRoundingTypeId, $expected, $isNew)
    {
        // Give
        $this->baseInfo->setOptionProductTaxRule(true);
        $Product = $this->createProduct(null, 0);
        $ProductClasses = $Product->getProductClasses();
        $ProductClass = $ProductClasses[0];
        $formData = $this->createFormData();

        if ($tax_rate !== null) {
            $formData['class']['tax_rate'] = $tax_rate;
        }
        if ($currentRoundingTypeId !== null) {
            $RoundingType = $this->entityManager->find(RoundingType::class, $currentRoundingTypeId);
            $TaxRule = new TaxRule();
            $TaxRule->setProductClass(null)
                ->setCreator($Product->getCreator())
                ->setProduct(null)
                ->setRoundingType($RoundingType)
                ->setTaxRate($tax_rate)
                ->setTaxAdjust(0)
                ->setApplyDate(new \DateTime('-1 days'));
            $this->entityManager->persist($TaxRule);
            $this->entityManager->flush();
        }
        $url = $isNew ? $this->generateUrl('admin_product_product_new') :
            $this->generateUrl('admin_product_product_edit', ['id' => $Product->getId()]);
        // When
        $this->client->request(
            'POST',
            $url,
            ['admin_product' => $formData]
        );

        // Then
        $this->assertTrue($this->client->getResponse()->isRedirection());

        $arrTmp = explode('/', $this->client->getResponse()->getTargetUrl());
        $productId = $arrTmp[count($arrTmp) - 2];
        $EditProduct = $this->productRepository->find($productId);

        $TaxRule = $this->taxRuleRepository->getByRule($EditProduct);
        if ($tax_rate !== null) {
            $this->assertInstanceOf(TaxRule::class, $TaxRule);
            $this->expected = $expected;
            $this->actual = $TaxRule->getRoundingType()->getId();
            $this->verify('tax_rate が設定されている場合は税率設定と RoundingType が取得できる');
        } else {
            $this->expected = $expected;
            $this->actual = RoundingType::ROUND;
            $this->verify('tax_rate が設定されていない場合は初期設定の RoundingType');
        }
    }

    /**
     * Product export test
     */
    public function testProductExport()
    {
        $this->markTestIncomplete('FIXME expectOutputRegex');
        $productName = 'test01';
        $this->expectOutputRegex("/$productName/");
        $this->createProduct($productName);

        $this->client->request('POST', $this->generateUrl('admin_product'), ['admin_search_product' => $this->createSearchForm()]);
        $this->client->request('GET', $this->generateUrl('admin_product_export'));

        $this->expected = 'application/octet-stream';
        $this->actual = $this->client->getResponse()->headers->get('Content-Type');
        $this->verify();
    }

    /**
     * Test for bulk action update product status
     */
    public function testProductBulkProductStatus()
    {
        // case invalid method
        $this->client->request(
            'GET',
            $this->generateUrl('admin_product_bulk_product_status', ['id' => ProductStatus::DISPLAY_SHOW]),
            []
        );
        $this->assertEquals(405, $this->client->getResponse()->getStatusCode());

        // case invalid product status id
        $this->client->request(
            'POST',
            $this->generateUrl('admin_product_bulk_product_status', ['id' => 0]),
            []
        );
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());

        // case true
        $productIds = [];
        /** @var Product[] $Products */
        $Products = $this->productRepository->findBy([], [], 5);
        foreach ($Products as $Product) {
            $productIds[] = $Product->getId();
        }

        $productStatuses = [
            ProductStatus::DISPLAY_SHOW,
            ProductStatus::DISPLAY_HIDE,
            ProductStatus::DISPLAY_ABOLISHED,
        ];
        foreach ($productStatuses as $productStatusId) {
            $ProductStatus = $this->productStatusRepository->find($productStatusId);
            $this->client->request(
                'POST',
                $this->generateUrl('admin_product_bulk_product_status', ['id' => $productStatusId]),
                ['ids' => $productIds]
            );
            $result = $this->productRepository->findBy(['id' => $productIds, 'Status' => $ProductStatus]);
            $this->assertEquals(count($productIds), count($result));
        }
    }

    public function testLoadProductClass()
    {
        $this->client->request(
            'GET',
            $this->generateUrl('admin_product_classes_load', ['id' => 1]),
            [],
            [],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
            ]
        );

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testAddImage()
    {
        $formData = $this->createFormData();

        copy(
            __DIR__.'/../../../../../../html/upload/save_image/sand-1.png',
            $this->imageDir.'/sand-1.png'
        );
        $image = new UploadedFile(
            $this->imageDir.'/sand-1.png',
            'sand-1.png',
            'image/png',
            null, null, true
        );
        $this->client->request('POST',
            $this->generateUrl('admin_product_image_add'),
            [
                'admin_product' => $formData,
            ],
            [
                'admin_product' => ['product_image' => [$image]],
            ],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testAddImageWithUppercaseSuffix()
    {
        $formData = $this->createFormData();
        copy(
            __DIR__.'/../../../../../../html/upload/save_image/sand-1.png',
            $this->imageDir.'/sand-1.PNG'
        );
        $image = new UploadedFile(
            $this->imageDir.'/sand-1.PNG',
            'sand-1.PNG',
            'image/png',
            null, null, true
        );

        $this->client->request('POST',
            $this->generateUrl('admin_product_image_add'),
            [
                'admin_product' => $formData,
            ],
            [
                'admin_product' => ['product_image' => [$image]],
            ],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testAddImageNotAjax()
    {
        $formData = $this->createFormData();

        $this->client->request('POST',
            $this->generateUrl('admin_product_image_add'),
            [
                'admin_product' => $formData,
            ],
            []
        );
        $this->assertSame(400, $this->client->getResponse()->getStatusCode());
    }

    public function testAddImageMineNotSupported()
    {
        $formData = $this->createFormData();
        copy(
            __DIR__.'/../../../../../Fixtures/categories.csv',
            $this->imageDir.'/categories.png'
        );
        $image = new UploadedFile(
            $this->imageDir.'/categories.png',
            'categories.png',
            'image/png',
            null, null, true
        );

        $crawler = $this->client->request('POST',
           $this->generateUrl('admin_product_image_add'),
            [
                'admin_product' => $formData,
            ],
            [
                'admin_product' => ['product_image' => [$image]],
            ],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
            ]
        );
        $this->assertFalse($this->client->getResponse()->isSuccessful());
    }

    /**
     * 個別税率編集時のテストデータ
     * 更新前の税率 / POST値 / 期待値の配列を返す
     *
     * @return array
     */
    public function dataEditProductProvider()
    {
        return [
            ['0', '0', '0'],
            ['0', '1', '1'],
            ['0', null, null],
            ['1', '0', '0'],
            ['1', '1', '1'],
            ['1', null, null],
            [null, '0', '0'],
            [null, '1', '1'],
            [null, null, null],
        ];
    }

    /**
     * 個別税率編集時のテストデータ
     * 個別税率 / 現在の RoundingType / RoundingType 期待値 / 新規商品 の配列を返す
     *
     * @return array
     */
    public function dataEditRoundingTypeProvider()
    {
        return [
            [null, null, RoundingType::ROUND, false],
            ['10', null, RoundingType::ROUND, false],
            ['10', RoundingType::CEIL, RoundingType::CEIL, false],
            ['10', RoundingType::CEIL, RoundingType::CEIL, true],
        ];
    }

    /**
     * @return array
     */
    private function createSearchForm()
    {
        $post = [
            Constant::TOKEN_NAME => 'dummy',
            'id' => '',
            'category_id' => '',
            'create_date_start' => '',
            'create_date_end' => '',
            'update_date_start' => '',
            'update_date_end' => '',
        ];

        return $post;
    }

    /**
     * 商品画像を削除する際に、他の商品画像が参照しているファイルは削除せず、それ以外は削除することをテスト
     */
    public function testDeleteImage()
    {
        /** @var Generator $generator */
        $generator = self::$container->get(Generator::class);
        $Product1 = $generator->createProduct(null, 0, 'abstract');
        $Product2 = $generator->createProduct(null, 0, 'abstract');

        $DuplicatedImage = $Product1->getProductImage()->first();
        assert($DuplicatedImage instanceof ProductImage);

        $NotDuplicatedImage = $Product1->getProductImage()->last();
        assert($NotDuplicatedImage instanceof ProductImage);

        $NewProduct2Image = new ProductImage();
        $NewProduct2Image
            ->setProduct($Product2)
            ->setFileName($DuplicatedImage->getFileName())
            ->setSortNo(999)
        ;
        $Product2->addProductImage($NewProduct2Image);
        $this->entityManager->persist($NewProduct2Image);
        $this->entityManager->flush();

        $data = $this->createFormData();
        $data['delete_images'] = $Product1->getProductImage()->map(static function (ProductImage $ProductImage) {
            return $ProductImage->getFileName();
        })->toArray();
        $this->client->request(
            'POST',
            $this->generateUrl('admin_product_product_edit', ['id' => $Product1->getId()]),
            ['admin_product' => $data]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect());

        $dir = __DIR__.'/../../../../../../html/upload/save_image/';
        $this->assertTrue(file_exists($dir.$DuplicatedImage->getFileName()));
        $this->assertFalse(file_exists($dir.$NotDuplicatedImage->getFileName()));
    }

    public function testDeleteAndDeleteProductImage()
    {
        /** @var Generator $generator */
        $generator = self::$container->get(Generator::class);
        $Product1 = $generator->createProduct(null, 0, 'abstract');
        $Product2 = $generator->createProduct(null, 0, 'abstract');

        $DuplicatedImage = $Product1->getProductImage()->first();
        assert($DuplicatedImage instanceof ProductImage);

        $NotDuplicatedImage = $Product1->getProductImage()->last();
        assert($NotDuplicatedImage instanceof ProductImage);

        $NewProduct2Image = new ProductImage();
        $NewProduct2Image
            ->setProduct($Product2)
            ->setFileName($DuplicatedImage->getFileName())
            ->setSortNo(999)
        ;
        $Product2->addProductImage($NewProduct2Image);
        $this->entityManager->persist($NewProduct2Image);
        $this->entityManager->flush();

        $params = [
            'id' => $Product1->getId(),
            Constant::TOKEN_NAME => 'dummy',
        ];

        $this->client->request('DELETE', $this->generateUrl('admin_product_product_delete', $params));

        $rUrl = $this->generateUrl('admin_product_page', ['page_no' => 1]).'?resume=1';

        $this->assertTrue($this->client->getResponse()->isRedirect($rUrl));

        $dir = __DIR__.'/../../../../../../html/upload/save_image/';
        $this->assertTrue(file_exists($dir.$DuplicatedImage->getFileName()));
        $this->assertFalse(file_exists($dir.$NotDuplicatedImage->getFileName()));
    }
}
