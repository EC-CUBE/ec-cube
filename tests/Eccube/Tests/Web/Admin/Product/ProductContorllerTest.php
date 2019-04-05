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

use Eccube\Common\Constant;
use Eccube\Entity\Master\Disp;
use Eccube\Entity\ProductClass;
use Eccube\Entity\TaxRule;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Eccube\Util\Str;
use Symfony\Component\DomCrawler\Crawler;

class ProductControllerTest extends AbstractAdminWebTestCase
{

    public function setUp()
    {
        parent::setUp();
        // 検索時, IDの重複を防ぐため事前に10個生成しておく
        for ($i = 0; $i < 10; $i++) {
            $this->createProduct();
        }
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

        $form = array(
            'class' => array(
                'product_type' => 1,
                'price01' => $price01,
                'price02' => $price02,
                'stock' => $faker->randomNumber(3),
                'stock_unlimited' => 0,
                'code' => $faker->word,
                'sale_limit' => null,
                'delivery_date' => ''
            ),
            'name' => $faker->word,
            'product_image' => null,
            'description_detail' => $faker->text,
            'description_list' => $faker->paragraph,
            'Category' => null,
            'Tag' => 1,
            'search_word' => $faker->word,
            'free_area' => $faker->text,
            'Status' => 1,
            'note' => $faker->text,
            'tags' => null,
            'images' => null,
            'add_images' => null,
            'delete_images' => null,
            '_token' => 'dummy',
        );
        return $form;
    }

    public function testRoutingAdminProductProduct()
    {
        $this->client->request('GET',
            $this->app->url('admin_product')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminProductProductNew()
    {
        $this->client->request('GET',
            $this->app->url('admin_product_product_new')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testProductSearchAll()
    {
        $AllProducts = $this->app['eccube.repository.product']->findAll();
        $cnt = count($AllProducts);
        $TestProduct = $this->createProduct();
        $cnt++;

        $post = array('admin_search_product' =>
            array(
                '_token' => 'dummy',
                'id' => '',
                'category_id' => '',
                'create_date_start' => '',
                'create_date_end' => '',
                'update_date_start' => '',
                'update_date_end' => '',
                'link_status' => '',
        ));
        $crawler = $this->client->request('POST', $this->app->url('admin_product'), $post);
        $this->expected = '検索結果 ' . $cnt . ' 件 が該当しました';
        $this->actual = $crawler->filter('h3.box-title')->text();
        $this->verify();

        // デフォルトのの表示件数確認テスト
        $this->expected = '10件';
        $this->actual = $crawler->filter('li#result_list__pagemax_menu a')->text();
        $this->verify();

        // 表示件数20件テスト
        $crawler = $this->client->request('GET', $this->app->url('admin_product_page', array('page_no' => 1)), array('page_count' => 20));
        $this->expected = '20件';
        $this->actual = $crawler->filter('li#result_list__pagemax_menu a')->text();
        $this->verify();

        // 表示件数入力値は正しくない場合はデフォルトのの表示件数になるテスト
        $crawler = $this->client->request('GET', $this->app->url('admin_product_page', array('page_no' => 1)), array('page_count' => 999999));
        $this->expected = '13 件';
        $this->actual = $crawler->filter('#result_list__header h3 span strong')->text();
        $this->verify();

        // 表示件数はSESSIONから取得するテスト
        $crawler = $this->client->request('GET', $this->app->url('admin_product_page', array('page_no' => 1)), array('status' => 1));
        $this->expected = '20件';
        $this->actual = $crawler->filter('li#result_list__pagemax_menu a')->text();
        $this->verify();
    }

    public function testProductSearchByName()
    {
        $TestProduct = $this->createProduct();
        $TestProduct->setName(Str::random());
        $this->app['orm.em']->flush($TestProduct);

        $post = array('admin_search_product' =>
            array(
                '_token' => 'dummy',
                'id' => $TestProduct->getName(),
                'category_id' => '',
                'create_date_start' => '',
                'create_date_end' => '',
                'update_date_start' => '',
                'update_date_end' => '',
                'link_status' => '',
        ));
        $crawler = $this->client->request('POST', $this->app->url('admin_product'), $post);
        $this->expected = '検索結果 1 件 が該当しました';
        $this->actual = $crawler->filter('h3.box-title')->text();
        $this->verify();

        // デフォルトのの表示件数確認テスト
        $this->expected = '10件';
        $this->actual = $crawler->filter('li#result_list__pagemax_menu a')->text();
        $this->verify();

        // 表示件数20件テスト
        $crawler = $this->client->request('GET', $this->app->url('admin_product_page', array('page_no' => 1)), array('page_count' => 40));
        $this->expected = '40件';
        $this->actual = $crawler->filter('li#result_list__pagemax_menu a')->text();
        $this->verify();

        // 表示件数入力値は正しくない場合はデフォルトのの表示件数になるテスト
        $crawler = $this->client->request('GET', $this->app->url('admin_product_page', array('page_no' => 1)), array('page_count' => 999999));
        $this->expected = '1 件';
        $this->actual = $crawler->filter('#result_list__header h3 span strong')->text();
        $this->verify();

        // 表示件数はSESSIONから取得するテスト
        $crawler = $this->client->request('GET', $this->app->url('admin_product_page', array('page_no' => 1)), array('status' => 1));
        $this->expected = '40件';
        $this->actual = $crawler->filter('li#result_list__pagemax_menu a')->text();
        $this->verify();
    }

    public function testProductSearchById()
    {
        $TestProduct = $this->createProduct();

        $post = array('admin_search_product' =>
            array(
                '_token' => 'dummy',
                'id' => $TestProduct->getId(),
                'category_id' => '',
                'create_date_start' => '',
                'create_date_end' => '',
                'update_date_start' => '',
                'update_date_end' => '',
                'link_status' => '',
        ));
        $crawler = $this->client->request('POST', $this->app->url('admin_product'), $post);
        $this->expected = '検索結果 1 件 が該当しました';
        $this->actual = $crawler->filter('h3.box-title')->text();
        $this->verify();

        // デフォルトのの表示件数確認テスト
        $this->expected = '10件';
        $this->actual = $crawler->filter('li#result_list__pagemax_menu a')->text();
        $this->verify();

        // 表示件数20件テスト
        $crawler = $this->client->request('GET', $this->app->url('admin_product_page', array('page_no' => 1)), array('page_count' => 30));
        $this->expected = '30件';
        $this->actual = $crawler->filter('li#result_list__pagemax_menu a')->text();
        $this->verify();

        // 表示件数入力値は正しくない場合はデフォルトのの表示件数になるテスト
        $crawler = $this->client->request('GET', $this->app->url('admin_product_page', array('page_no' => 1)), array('page_count' => 999999));
        $this->expected = '1 件';
        $this->actual = $crawler->filter('#result_list__header h3 span strong')->text();
        $this->verify();

        // 表示件数はSESSIONから取得するテスト
        $crawler = $this->client->request('GET', $this->app->url('admin_product_page', array('page_no' => 1)), array('status' => 1));
        $this->expected = '30件';
        $this->actual = $crawler->filter('li#result_list__pagemax_menu a')->text();
        $this->verify();
    }

    public function testProductSearchByIdZero()
    {
        $TestProduct = $this->createProduct();

        $post = array('admin_search_product' =>
            array(
                '_token' => 'dummy',
                'id' => 99999999,
                'category_id' => '',
                'create_date_start' => '',
                'create_date_end' => '',
                'update_date_start' => '',
                'update_date_end' => '',
                'link_status' => '',
        ));
        $crawler = $this->client->request('POST', $this->app->url('admin_product'), $post);
        $this->expected = '検索条件に該当するデータがありませんでした。';
        $this->actual = $crawler->filter('h3.box-title')->text();
        $this->verify();
    }

    public function testProductSearchByNameZero()
    {
        $TestProduct = $this->createProduct();

        $post = array('admin_search_product' =>
            array(
                '_token' => 'dummy',
                'id' => 'not Exists product name',
                'category_id' => '',
                'create_date_start' => '',
                'create_date_end' => '',
                'update_date_start' => '',
                'update_date_end' => '',
                'link_status' => '',
        ));
        $crawler = $this->client->request('POST', $this->app->url('admin_product'), $post);
        $this->expected = '検索条件に該当するデータがありませんでした。';
        $this->actual = $crawler->filter('h3.box-title')->text();
        $this->verify();
    }

    public function testRoutingAdminProductProductEdit()
    {

        $TestProduct = $this->createProduct();

        $test_product_id = $this->app['eccube.repository.product']
            ->findOneBy(array(
                'name' => $TestProduct->getName()
            ))
            ->getId();

        $crawler = $this->client->request('GET',
            $this->app->url('admin_product_product_edit', array('id' => $test_product_id))
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testEditWithPost()
    {
        $Product = $this->createProduct(null, 0);
        $formData = $this->createFormData();
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_product_product_edit', array('id' => $Product->getId())),
            array('admin_product' => $formData)
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_product_product_edit', array('id' => $Product->getId()))));

        $EditedProduct = $this->app['eccube.repository.product']->find($Product->getId());
        $this->expected = $formData['name'];
        $this->actual = $EditedProduct->getName();
        $this->verify();
    }

    public function testDelete()
    {
        $Product = $this->createProduct();
        $crawler = $this->client->request(
            'DELETE',
            $this->app->url('admin_product_product_delete', array('id' => $Product->getId()))
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_product_page', array('page_no' => 1)).'?resume=1'));

        $DeletedProduct = $this->app['eccube.repository.product']->find($Product->getId());
        $this->expected = 1;
        $this->actual = $DeletedProduct->getDelFlg();
        $this->verify();
    }

    public function testCopy()
    {
        $Product = $this->createProduct();
        $AllProducts = $this->app['eccube.repository.product']->findAll();
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_product_product_copy', array('id' => $Product->getId()))
        );

        $this->assertTrue($this->client->getResponse()->isRedirect());

        $AllProducts2 = $this->app['eccube.repository.product']->findAll();
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
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setOptionProductTaxRule(Constant::ENABLED);
        $formData = $this->createFormData();

        $formData['class']['tax_rate'] = $taxRate;
        // When
        $this->client->request(
            'POST',
            $this->app->url('admin_product_product_new'),
            array('admin_product' => $formData)
        );

        // Then
        $this->assertTrue($this->client->getResponse()->isRedirection());

        $arrTmp = explode('/', $this->client->getResponse()->getTargetUrl());
        $productId = $arrTmp[count($arrTmp)-2];
        $Product = $this->app['eccube.repository.product']->find($productId);

        $this->expected = $expected;
        $Taxrule = $this->app['eccube.repository.tax_rule']->findOneBy(array('Product' => $Product));
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
        /** @var $ProductClass ProductClass*/
        $ProductClass = $testProduct->getProductClasses()->first();
        $ProductClass->setStock(0);
        $ProductClass->getProductStock()->setStock(0);
        $this->app['orm.em']->flush();

        $searchForm = $this->createSearchForm();
        $searchForm['id'] = 'Product with stock';

        /* @var $crawler Crawler*/
        $crawler = $this->client->request('POST', $this->app->url('admin_product'), array('admin_search_product' => $searchForm));
        $this->expected = '検索結果 2 件 が該当しました';
        $this->actual = $crawler->filter('h3.box-title')->text();
        $this->verify();

        // No stock click button
        $noStockUrl = $crawler->selectLink('在庫なし')->link()->getUri();
        $crawler = $this->client->request('GET', $noStockUrl);
        $this->expected = '検索結果 1 件 が該当しました';
        $this->actual = $crawler->filter('h3.box-title')->text();
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
        $display = $this->app['eccube.repository.master.disp']->find(Disp::DISPLAY_HIDE);
        $testProduct->setStatus($display);
        $this->app['orm.em']->flush();

        $searchForm = $this->createSearchForm();
        $searchForm['id'] = 'Product with status';

        /* @var $crawler Crawler*/
        $crawler = $this->client->request('POST', $this->app->url('admin_product'), array('admin_search_product' => $searchForm));
        $this->expected = '検索結果 2 件 が該当しました';
        $this->actual = $crawler->filter('h3.box-title')->text();
        $this->verify();

        // private click button
        $privateUrl = $crawler->selectLink('非公開')->link()->getUri();
        $crawler = $this->client->request('GET', $privateUrl);
        $this->expected = '検索結果 1 件 が該当しました';
        $this->actual = $crawler->filter('h3.box-title')->text();
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
        $display = $this->app['eccube.repository.master.disp']->find(Disp::DISPLAY_HIDE);
        $testProduct02->setStatus($display);
        $this->app['orm.em']->flush();

        $searchForm = $this->createSearchForm();
        $searchForm['id'] = 'Product with status';

        /* @var $crawler Crawler*/
        $crawler = $this->client->request('POST', $this->app->url('admin_product'), array('admin_search_product' => $searchForm));
        $this->expected = '検索結果 2 件 が該当しました';
        $this->actual = $crawler->filter('h3.box-title')->text();
        $this->verify();

        // public click button
        $privateUrl = $crawler->selectLink('公開')->link()->getUri();
        $crawler = $this->client->request('GET', $privateUrl);
        $this->expected = '検索結果 1 件 が該当しました';
        $this->actual = $crawler->filter('h3.box-title')->text();
        $this->verify();

        $csvExportUrl = $crawler->filter('ul.dropdown-menu')->selectLink('CSVダウンロード')->link()->getUri();
        $this->client->request('GET', $csvExportUrl);
    }

    /**
     * Test search + export product with all
     */
    public function testExportWithAll()
    {
        $this->expectOutputRegex('/[Product with status 01]{1}[Product with status 02]{2}/');
        $this->createProduct('Product with status 01', 0);
        $testProduct02 = $this->createProduct('Product with status 02', 1);
        $display = $this->app['eccube.repository.master.disp']->find(Disp::DISPLAY_HIDE);
        $testProduct02->setStatus($display);
        $this->app['orm.em']->flush();

        $searchForm = $this->createSearchForm();
        $searchForm['id'] = 'Product with status';

        /* @var $crawler Crawler*/
        $crawler = $this->client->request('POST', $this->app->url('admin_product'), array('admin_search_product' => $searchForm));
        $this->expected = '検索結果 2 件 が該当しました';
        $this->actual = $crawler->filter('h3.box-title')->text();
        $this->verify();

        $csvExportUrl = $crawler->filter('ul.dropdown-menu')->selectLink('CSVダウンロード')->link()->getUri();
        $this->client->request('GET', $csvExportUrl);
    }

    public function dataNewProductProvider()
    {
        return array(
            array(null, null),
            array("0", "0"),
            array("1", "1"),
        );
    }

    /**
     * 個別税率設定のテストケース
     * 個別税率設定を有効にし、商品編集時に更新されることを確認する
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/1547
     * @param $before 更新前の税率
     * @param $after POST値
     * @param $expected 期待値
     *
     * @dataProvider dataEditProductProvider
     */
    public function testEditWithPostTaxRate($before, $after, $expected)
    {
        // Give
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setOptionProductTaxRule(Constant::ENABLED);
        $Product = $this->createProduct(null, 0);
        $ProductClasses = $Product->getProductClasses();
        $ProductClass = $ProductClasses[0];
        $formData = $this->createFormData();

        if (!is_null($after)) {
            $formData['class']['tax_rate'] = $after;
        }
        if (!is_null($before)) {
            $DefaultTaxRule = $this->app['eccube.repository.tax_rule']->find(\Eccube\Entity\TaxRule::DEFAULT_TAX_RULE_ID);

            $TaxRule = new TaxRule();
            $TaxRule->setProductClass($ProductClass)
                ->setCreator($Product->getCreator())
                ->setProduct($Product)
                ->setCalcRule($DefaultTaxRule->getCalcRule())
                ->setTaxRate($before)
                ->setTaxAdjust(0)
                ->setApplyDate(new \DateTime())
                ->setDelFlg(Constant::DISABLED);
            $ProductClass->setTaxRule($TaxRule);
            $this->app['orm.em']->persist($TaxRule);
            $this->app['orm.em']->flush();
        }

        // When
        $this->client->request(
            'POST',
            $this->app->url('admin_product_product_edit', array('id' => $Product->getId())),
            array('admin_product' => $formData)
        );

        // Then
        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_product_product_edit', array('id' => $Product->getId()))));

        $this->expected = $expected;
        $TaxRule = $this->app['eccube.repository.tax_rule']->findOneBy(array('Product' => $Product, 'ProductClass' => $ProductClass));

        if (is_null($TaxRule)) {
            $this->actual = null;
        } else {
            $this->actual = $TaxRule->getTaxRate();
        }

        $this->assertTrue($this->actual === $this->expected);
    }

    /**
     * Product export test
     */
    public function testProductExport()
    {
        $productName = 'test01';
        $this->expectOutputRegex("/$productName/");
        $this->createProduct($productName);
        $post = array('admin_search_product' =>
            array(
                '_token' => 'dummy',
                'id' => '',
                'category_id' => '',
                'create_date_start' => '',
                'create_date_end' => '',
                'update_date_start' => '',
                'update_date_end' => '',
                'link_status' => '',
            ));
        $this->client->request('POST', $this->app->url('admin_product'), $post);
        $this->client->request(
            'GET',
            $this->app->url('admin_product_export')
        );

        $this->expected = 'application/octet-stream';
        $this->actual = $this->client->getResponse()->headers->get('Content-Type');
        $this->verify();
    }

    /**
     * 個別税率編集時のテストデータ
     * 更新前の税率 / POST値 / 期待値の配列を返す
     *
     * @return array
     */
    public function dataEditProductProvider()
    {
        return array(
            array('0', '0', '0'),
            array('0', '1', '1'),
            array('0', null, null),
            array('1', '0', '0'),
            array('1', '1', '1'),
            array('1', null, null),
            array(null, '0', '0'),
            array(null, '1', '1'),
            array(null, null, null),
        );
    }

    /**
     * @return array
     */
    private function createSearchForm()
    {
        $post = array(
            '_token' => 'dummy',
            'id' => '',
            'category_id' => '',
            'create_date_start' => '',
            'create_date_end' => '',
            'update_date_start' => '',
            'update_date_end' => '',
            'link_status' => '',
        );

        return $post;
    }
}
