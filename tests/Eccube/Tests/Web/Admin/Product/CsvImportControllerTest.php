<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Web\Admin\Product;

use Eccube\Entity\Product;
use Eccube\Repository\CategoryRepository;
use Eccube\Repository\ProductRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Faker\Generator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CsvImportControllerTest extends AbstractAdminWebTestCase
{
    /**
     * @var ProductRepository
     */
    protected $productRepo;
    /**
     * @var CategoryRepository
     */
    protected $categoryRepo;
    protected $filepath;

    private $categoriesIdList = [];

    public function setUp()
    {
        parent::setUp();
        $this->productRepo = $this->container->get(ProductRepository::class);
        $this->categoryRepo = $this->container->get(CategoryRepository::class);
        $this->filepath = __DIR__.'/products.csv';
        copy(__DIR__.'/../../../../../Fixtures/products.csv', $this->filepath); // 削除されてしまうのでコピーしておく
    }

    public function tearDown()
    {
        if (file_exists($this->filepath)) {
            unlink($this->filepath);
        }
        parent::tearDown();
    }

    /**
     * CSVを生成するための配列を返す.
     *
     * @param boolean $has_header ヘッダ行を含める場合 true
     *
     * @return array CSVを生成するための配列
     *
     * @see CsvImportController::getProductCsvHeader()
     */
    public function createCsvAsArray($has_header = true)
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

        $csv = [
            '商品ID' => null,
            '公開ステータス(ID)' => 1,
            '商品名' => '商品名'.$faker->word.'商品名',
            'ショップ用メモ欄' => 'ショップ用メモ欄'.$faker->paragraph.'ショップ用メモ欄',
            '商品説明(一覧)' => '商品説明(一覧)'.$faker->paragraph.'商品説明(一覧)',
            '商品説明(詳細)' => '商品説明(詳細)'.$faker->realText().'商品説明(詳細)',
            '検索ワード' => '検索ワード'.$faker->word.'検索ワード',
            'フリーエリア' => 'フリーエリア'.$faker->paragraph.'フリーエリア',
            '商品削除フラグ' => 0,
            '商品画像' => $faker->word.'.jpg,'.$faker->word.'.jpg',
            '商品カテゴリ(ID)' => '5,6',
            'タグ(ID)' => '1,2',
            '販売種別(ID)' => 1,
            '規格分類1(ID)' => 3,
            '規格分類2(ID)' => 6,
            '発送日目安(ID)' => 1,
            '商品コード' => $faker->word,
            '在庫数' => 100,
            '在庫数無制限フラグ' => 0,
            '販売制限数' => null,
            '通常価格' => $price01,
            '販売価格' => $price02,
            '送料' => 0,
        ];
        $result = [];
        if ($has_header) {
            $result[] = array_keys($csv);
        }
        $result[] = array_values($csv);

        return $result;
    }

    /**
     * 引数の配列から CSV を生成し, リソースを返す.
     */
    public function createCsvFromArray(array $csv, $filename = 'products.csv')
    {
        $dir = sys_get_temp_dir();
        $filepath = $dir.'/'.$filename;
        $fp = fopen($filepath, 'w');
        if ($fp !== false) {
            foreach ($csv as $row) {
                fputcsv($fp, $row);
            }
        } else {
            throw new \Exception('create error!');
        }
        fclose($fp);

        return $filepath;
    }

    public function testCsvProduct()
    {
        // 3商品生成
        $csv = $this->createCsvAsArray();
        $csv = array_merge($csv, $this->createCsvAsArray(false));

        // 規格1のみの商品
        $csvClass1Only = $this->createCsvAsArray(false);
        $csvClass1Only[0][14] = null; // 規格分類2(ID)
        $csvClass1Only[0][16] = 'class1-only'; // 商品コード
        $csv = array_merge($csv, $csvClass1Only);

        $this->filepath = $this->createCsvFromArray($csv);

        $crawler = $this->scenario();

        $Products = $this->productRepo->findAll();

        $this->expected = 5;    // 3商品 + 既存2商品
        $this->actual = count($Products);
        $this->verify();

        // ProductCategoryTest
        //カテゴリーIDs
        foreach ($csv as $csvRow) {
            $csvCat[md5($csvRow[2])] = $csvRow[10];
        }
        foreach ($Products as $Product) {
            $nameHash = md5($Product->getName());
            if (!isset($csvCat[$nameHash])) {
                continue;
            }
            // expected categories is
            $expectedIds = $this->getExpectedCategoriesIdList($csvCat[$nameHash]);
            $actualIds = [];
            /* @var $Product \Eccube\Entity\Product */
            foreach ($Product->getProductCategories() as $ProductCategory) {
                /* @var $ProductCategory \Eccube\Entity\ProductCategory */
                $actualIds[$ProductCategory->getCategoryId()] = $ProductCategory->getCategoryId();
                $this->expected = $expectedIds[$ProductCategory->getCategoryId()];
                $this->actual = $ProductCategory->getCategoryId();
                $this->verify();
            }
            foreach ($expectedIds as $catId) {
                $this->expected = $catId;
                $this->actual = $actualIds[$catId];
                $this->verify();
            }
        }

        $this->assertRegexp('/商品登録CSVファイルをアップロードしました。/u',
            $crawler->filter('div.alert-success')->text());

        // 規格1のみ商品の確認
        // dtb_product_class.del_flg = 1 の確認をしたいので PDO で取得
        $pdo = $this->entityManager->getConnection()->getWrappedConnection();
        $sql = "SELECT * FROM dtb_product_class WHERE product_code = 'class1-only' ORDER BY visible ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $this->expected = 2;
        $this->actual = count($result);
        $this->verify('取得できるのは2行');

        $this->expected = null;
        $this->actual = $result[0]['class_category_id1'];
        $this->verify('class_category_id1 は null');

        $this->expected = null;
        $this->actual = $result[0]['class_category_id2'];
        $this->verify('class_category_id2 は null');

        // del_flg = 0 の行の確認
        $this->expected = 1;
        $this->actual = $result[1]['visible'];
        $this->verify('result[1] は visible = 1');

        $this->expected = 3;
        $this->actual = $result[1]['class_category_id1'];
        $this->verify('class_category_id1 は 3');

        $this->expected = null;
        $this->actual = $result[1]['class_category_id2'];
        $this->verify('class_category_id2 は null');
    }

    public function testCsvImportWithExistsProducts()
    {
        $crawler = $this->scenario();

        $Products = $this->productRepo->findAll();

        $this->expected = 12;
        $this->actual = count($Products);
        $this->verify();

        $newCount = 0;
        /** @var Product $Product */
        foreach ($Products as $Product) {
            $ProductClasses = $Product->getProductClasses();
            foreach ($ProductClasses as $ProductClass) {
                if (preg_match('/fork-0[0-9]-new/', $ProductClass->getCode())) {
                    $newCount++;
                }
            }

            // categories
            $dateTimeNow = new \DateTime('-20 minutes');
            // check only new records
            if ($Product->getUpdateDate() > $dateTimeNow) {
                $expectedCategoryIds = [];
                $actualCategoryIds = [];
                foreach ($Product->getProductCategories() as $ProductCategory) {
                    $expectedCategoryIds[$ProductCategory->getCategoryId()] = $ProductCategory->getCategoryId();
                    $actualCategoryIds[$ProductCategory->getCategoryId()] = $ProductCategory->getCategoryId();
                    foreach ($this->getParentsCategoriesId($ProductCategory->getCategoryId()) as $catId) {
                        $expectedCategoryIds[$catId] = $catId;
                    }
                }
                foreach ($expectedCategoryIds as $catId) {
                    $this->expected = $catId;
                    $this->actual = $actualCategoryIds[$catId];
                    $this->verify();
                }
            }
        }

        $this->expected = 3;
        $this->actual = $newCount;
        $this->verify('fork-0[0-9]-new に商品コードを変更したのは '.$this->expected.'商品規格');

        $this->assertRegexp('/商品登録CSVファイルをアップロードしました。/u',
            $crawler->filter('div.alert-success')->text());
    }

    /**
     * 既存の規格なし商品に商品規格を追加する.
     */
    public function testCsvImportWithExistsProductsAddProductClass()
    {
        // 商品生成
        $csv = $this->createCsvAsArray();
        $csv[1][0] = 2;                        // 商品ID = 2 に規格を追加する
        $csv[1][16] = 'add-class';             // 商品コード

        $this->filepath = $this->createCsvFromArray($csv);

        $crawler = $this->scenario();

        $Products = $this->productRepo->findAll();

        $this->expected = 2;    // 既存2商品
        $this->actual = count($Products);
        $this->verify();

        $this->assertRegexp('/商品登録CSVファイルをアップロードしました。/u',
            $crawler->filter('div.alert-success')->text());

        // 規格1のみ商品の確認
        // dtb_product_class.del_flg = 1 の確認をしたいので PDO で取得
        $pdo = $this->entityManager->getConnection()->getWrappedConnection();
        $sql = 'SELECT * FROM dtb_product_class WHERE product_id = 2 ORDER BY visible ASC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $this->expected = 2;
        $this->actual = count($result);
        $this->verify('取得できるのは2行');

        $this->expected = false;
        $this->actual = (bool) $result[0]['visible'];
        $this->verify('result[0] は visible = false');

        $this->expected = null;
        $this->actual = $result[0]['class_category_id1'];
        $this->verify('class_category_id1 は null');

        $this->expected = null;
        $this->actual = $result[0]['class_category_id2'];
        $this->verify('class_category_id2 は null');

        // del_flg = 0 の行の確認
        $this->expected = true;
        $this->actual = $result[1]['visible'];
        $this->verify('result[1] は visible = true');

        $this->expected = 3;
        $this->actual = $result[1]['class_category_id1'];
        $this->verify('class_category_id1 は 3');

        $this->expected = 6;
        $this->actual = $result[1]['class_category_id2'];
        $this->verify('class_category_id2 は 6');

        // ProductCategoryTest
        //カテゴリーIDs
        foreach ($csv as $csvRow) {
            $csvCat[md5($csvRow[2])] = $csvRow[10];
        }
        /** @var Product $Product */
        foreach ($Products as $Product) {
            $nameHash = md5($Product->getName());
            if (!isset($csvCat[$nameHash])) {
                continue;
            }
            // expected categories is
            $expectedIds = $this->getExpectedCategoriesIdList($csvCat[$nameHash]);
            $actualIds = [];
            /* @var $ProductCategory \Eccube\Entity\ProductCategory */
            foreach ($Product->getProductCategories() as $ProductCategory) {
                $actualIds[$ProductCategory->getCategoryId()] = $ProductCategory->getCategoryId();
                $this->expected = $expectedIds[$ProductCategory->getCategoryId()];
                $this->actual = $ProductCategory->getCategoryId();
                $this->verify();
            }
            foreach ($expectedIds as $catId) {
                $this->expected = $catId;
                $this->actual = $actualIds[$catId];
                $this->verify();
            }
        }
    }

    public function testCsvTemplateWithProduct()
    {
        $this->markTestIncomplete('Impossible to call set("eccube.constants") on a frozen ParameterBag. => skip');
        // 一旦別の変数に代入しないと, config 以下の値を書きかえることができない
        $config = $this->eccubeConfig;
        $config['eccube_csv_export_encoding'] = 'UTF-8'; // SJIS だと比較できないので UTF-8 に変更しておく
        $this->container->setParameter('eccube.constants', $config);

        $this->expectOutputString('商品ID,公開ステータス(ID),商品名,ショップ用メモ欄,商品説明(一覧),商品説明(詳細),検索ワード,フリーエリア,商品削除フラグ,商品画像,商品カテゴリ(ID),タグ(ID),販売種別(ID),規格分類1(ID),規格分類2(ID),発送日目安(ID),商品コード,在庫数,在庫数無制限フラグ,販売制限数,通常価格,販売価格,送料'."\n");

        $this->client->request(
            'GET',
            $this->generateUrl('admin_product_csv_template', ['type' => 'product'])
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    //======================================================================
    // CATEGORY Import Test
    //======================================================================

    /**
     * Import csv test
     */
    public function testCsvCategory()
    {
        $this->filepath = __DIR__.'/categories.csv';
        copy(__DIR__.'/../../../../../Fixtures/categories.csv', $this->filepath); // 削除されてしまうのでコピーしておく

        $crawler = $this->scenario('admin_product_category_csv_import', 'categories.csv');

        $Categories = $this->categoryRepo->findAll();

        $this->expected = 6;
        $this->actual = count($Categories);
        $this->verify();

        $this->assertRegexp('/カテゴリ登録CSVファイルをアップロードしました。/u', $crawler->filter('div.alert-success')->text());
    }

    /**
     * Import new csv test
     */
    public function testCsvCategoryWithNew()
    {
        $this->filepath = __DIR__.'/categories.csv';
        copy(__DIR__.'/../../../../../Fixtures/categories.csv', $this->filepath);
        $csv = [
            ['カテゴリID', 'カテゴリ名', '親カテゴリID', 'カテゴリ削除フラグ'],
            ['', '新カテゴリ', '', ''],
        ];
        $this->filepath = $this->createCsvFromArray($csv, 'categories.csv');

        $crawler = $this->scenario('admin_product_category_csv_import', 'categories.csv');

        $Categories = $this->categoryRepo->findBy(['name' => '新カテゴリ']);

        $this->expected = 1;
        $this->actual = count($Categories);
        $this->verify();

        $this->assertRegexp('/カテゴリ登録CSVファイルをアップロードしました。/u', $crawler->filter('div.alert-success')->text());
    }

    /**
     * Import only exist category name.
     */
    public function testCsvCategoryWithOnlyCategoryName()
    {
        $this->filepath = __DIR__.'/categories.csv';
        copy(__DIR__.'/../../../../../Fixtures/categories.csv', $this->filepath); // 削除されてしまうのでコピーしておく

        $csv = [
            ['カテゴリ名'],
            ['新カテゴリ'],
        ];
        $this->filepath = $this->createCsvFromArray($csv, 'categories.csv');

        $crawler = $this->scenario('admin_product_category_csv_import', 'categories.csv');

        $Categories = $this->categoryRepo->findBy(['name' => '新カテゴリ']);

        $this->expected = 1;
        $this->actual = count($Categories);
        $this->verify();

        $this->assertRegexp('/カテゴリ登録CSVファイルをアップロードしました。/u', $crawler->filter('div.alert-success')->text());
    }

    /**
     * Category name is null
     */
    public function testCsvCategoryWithCategoryNameIsNull()
    {
        $this->filepath = __DIR__.'/categories.csv';
        copy(__DIR__.'/../../../../../Fixtures/categories.csv', $this->filepath); // 削除されてしまうのでコピーしておく

        $categories = $this->categoryRepo->findAll();
        $this->expected = count($categories);

        $csv = [
            ['カテゴリID', 'カテゴリ名'],
            [null, null],
        ];
        $this->filepath = $this->createCsvFromArray($csv, 'categories.csv');

        $crawler = $this->scenario('admin_product_category_csv_import', 'categories.csv');

        $arrCategory = $this->categoryRepo->findAll();
        $this->actual = count($arrCategory);
        $this->verify();

        $this->assertRegexp('/2行目のカテゴリ名が設定されていません。/u', $crawler->filter('div#upload_box__error--1')->text());
    }

    /**
     * Import do not exist category name column.
     */
    public function testCsvCategoryWithoutCategoryNameColumn()
    {
        $this->filepath = __DIR__.'/categories.csv';
        copy(__DIR__.'/../../../../../Fixtures/categories.csv', $this->filepath); // 削除されてしまうのでコピーしておく

        $categories = $this->categoryRepo->findAll();
        $this->expected = count($categories);

        $csv = [
            ['カテゴリID'],
            [''],
        ];
        $this->filepath = $this->createCsvFromArray($csv, 'categories.csv');

        $crawler = $this->scenario('admin_product_category_csv_import', 'categories.csv');

        $arrCategory = $this->categoryRepo->findAll();
        $this->actual = count($arrCategory);
        $this->verify();

        $this->assertRegexp('/CSVのフォーマットが一致しません。/u', $crawler->filter('div#upload_box__error--1')->text());
    }

    /**
     * Testing the column was mixed.
     */
    public function testCsvCategoryWithColumnSorted()
    {
        $this->filepath = __DIR__.'/categories.csv';
        copy(__DIR__.'/../../../../../Fixtures/categories.csv', $this->filepath); // 削除されてしまうのでコピーしておく

        /** @var $faker \Faker\Generator */
        $faker = $this->getFaker();
        $categoryName = 'CategoryNameTest';
        $csv = [
            ['カテゴリ名', 'カテゴリID'],
            [$categoryName, ''],
        ];
        $this->filepath = $this->createCsvFromArray($csv, 'categories.csv');

        $crawler = $this->scenario('admin_product_category_csv_import', 'categories.csv');

        $arrCategory = $this->categoryRepo->findBy(['name' => $categoryName]);
        $this->actual = count($arrCategory);
        $this->expected = 1;
        $this->verify();

        $this->assertRegexp('/カテゴリ登録CSVファイルをアップロードしました。/u', $crawler->filter('div.alert-success')->text());
    }

    //======================================================================
//    CSV export template test
    //======================================================================

    public function testCsvTemplateWithCategory()
    {
        $this->markTestIncomplete('Impossible to call set() on a frozen ParameterBag.');
        // 一旦別の変数に代入しないと, config 以下の値を書きかえることができない
        $config = $this->eccubeConfig;
        $config['eccube_csv_export_encoding'] = 'UTF-8'; // SJIS だと比較できないので UTF-8 に変更しておく
        $this->container->setParameter('eccube.constants', $config);

        $this->expectOutputString('カテゴリID,カテゴリ名,親カテゴリID,カテゴリ削除フラグ'."\n");

        $this->client->request(
            'GET',
            $this->generateUrl('admin_product_csv_template', ['type' => 'category'])
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    //======================================================================
    //    CSV import product test
    //======================================================================

    /**
     * Check the imported products with csv column is missed
     */
    public function testImportProductWithColumnIsMissed()
    {
        $Products = $this->productRepo->findAll();
        $this->expected = count($Products) + 1;
        // csv missing id column
        $csv = $this->createCsvAsArray();
        unset($csv[0][0]);
        unset($csv[1][0]);
        $this->filepath = $this->createCsvFromArray($csv);
        $crawler = $this->scenario();
        $Products = $this->productRepo->findAll();
        $this->actual = count($Products);
        $this->verify();
        // ProductCategoryTest
        //カテゴリーIDs
        foreach ($csv as $csvRow) {
            $csvCat[md5($csvRow[2])] = $csvRow[10];
        }
        foreach ($Products as $Product) {
            $nameHash = md5($Product->getName());
            if (!isset($csvCat[$nameHash])) {
                continue;
            }
            // expected categories is
            $expectedIds = $this->getExpectedCategoriesIdList($csvCat[$nameHash]);
            $actualIds = [];
            /* @var $Product \Eccube\Entity\Product */
            foreach ($Product->getProductCategories() as $ProductCategory) {
                /* @var $ProductCategory \Eccube\Entity\ProductCategory */
                $actualIds[$ProductCategory->getCategoryId()] = $ProductCategory->getCategoryId();
                $this->expected = $expectedIds[$ProductCategory->getCategoryId()];
                $this->actual = $ProductCategory->getCategoryId();
                $this->verify();
            }
            foreach ($expectedIds as $catId) {
                $this->expected = $catId;
                $this->actual = $actualIds[$catId];
                $this->verify();
            }
        }
        $this->assertRegexp('/商品登録CSVファイルをアップロードしました。/u', $crawler->filter('div.alert-success')->text());
    }

    /**
     * Imported products tested with just the column is required.
     */
    public function testImportProductWithColumnIsRequiredOnly()
    {
        $Products = $this->productRepo->findAll();
        $this->expected = count($Products) + 1;
        /** @var $faker Generator */
        $faker = $this->getFaker();
        // 1 product case stock_unlimited = true
        $csv[] = ['公開ステータス(ID)', '商品名', '販売種別(ID)', '在庫数無制限フラグ', '販売価格'];
        $csv[] = [1,  '商品名'.$faker->word.'商品名', 1, 1, $faker->randomNumber(5)];
        $this->filepath = $this->createCsvFromArray($csv);
        $crawler = $this->scenario();

        $Products = $this->productRepo->findAll();
        $this->actual = count($Products);
        $this->verify();
        $this->assertRegexp('/商品登録CSVファイルをアップロードしました。/u', $crawler->filter('div.alert-success')->text());

        // 2 case no stock_unlimited
        $this->expected = count($Products) + 1;
        // 1 product case stock_unlimited = true
        $csv = [];
        $csv[] = ['公開ステータス(ID)', '商品名', '販売種別(ID)', '在庫数', '販売価格'];
        $csv[] = [1,  '商品名'.$faker->word.'商品名', 1, 1, $faker->randomNumber(5)];
        $this->filepath = $this->createCsvFromArray($csv);
        $crawler = $this->scenario();

        $Products = $this->productRepo->findAll();
        $this->actual = count($Products);
        $this->verify();

        $this->assertRegexp('/商品登録CSVファイルをアップロードしました。/u', $crawler->filter('div.alert-success')->text());
    }

    /**
     * Imported product ID is incorrect.
     *
     * @param $id
     * @param $expectedMessage
     * @dataProvider dataProductIdProvider
     */
    public function testImportProductWithIdIsWrong($id, $expectedMessage)
    {
        $Products = $this->productRepo->findAll();
        $this->expected = count($Products);
        // 1 product
        $csv = $this->createCsvAsArray();
        $csv[1][0] = $id;
        $this->filepath = $this->createCsvFromArray($csv);
        $crawler = $this->scenario();

        $Products = $this->productRepo->findAll();
        $this->actual = count($Products);
        $this->verify();
        $this->assertRegexp("/$expectedMessage/u", $crawler->filter('form#upload-form')->text());
    }

    /**
     * Imported product status flg is incorrect.
     *
     * @param $status
     * @param $expectedMessage
     * @dataProvider dataStatusProvider
     */
    public function testImportProductWithPublicIdIsIncorrect($status, $expectedMessage)
    {
        /** @var $faker Generator */
        $faker = $this->getFaker();
        // 1 product
        $csv[] = ['公開ステータス(ID)', '商品名', '販売種別(ID)', '在庫数無制限フラグ', '販売価格'];
        $csv[] = [$status, '商品名'.$faker->word.'商品名', 1, 1, $faker->randomNumber(5)];
        $this->filepath = $this->createCsvFromArray($csv);
        $crawler = $this->scenario();

        $this->assertRegexp("/$expectedMessage/u", $crawler->filter('form#upload-form')->text());
    }

    /**
     * Data for case check product id.
     *
     * @return array
     */
    public function dataProductIdProvider()
    {
        return [
            [99999, '2行目の商品IDが存在しません。'],
            ['abc', '2行目の商品IDが存在しません。'],
        ];
    }

    /**
     * Data for case check product status flg.
     *
     * @return array
     */
    public function dataStatusProvider()
    {
        return [
            [99, '2行目の公開ステータス\(ID\)が存在しません'],
            ['abc', '2行目の公開ステータス\(ID\)が存在しません'],
            ['', '2行目の公開ステータス\(ID\)が設定されていません'],
        ];
    }

    /**
     * $this->filepath のファイルを CSV アップロードし, 完了画面の crawler を返す.
     *
     * @param string $bind
     * @param string $original_name
     *
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    protected function scenario($bind = 'admin_product_csv_import', $original_name = 'products.csv')
    {
        $file = new UploadedFile(
            $this->filepath,    // file path
            $original_name,     // original name
            'text/csv',         // mimeType
            null,               // file size
            null,               // error
            true                // test mode
        );

        $crawler = $this->client->request(
            'POST',
            $this->generateUrl($bind),
            [
                'admin_csv_import' => [
                    '_token' => 'dummy',
                    'import_file' => $file,
                ],
            ],
            ['import_file' => $file]
        );

        return $crawler;
    }

    private function getExpectedCategoriesIdList($categoriesStr)
    {
        $catIds = [];
        $tmp = explode(',', $categoriesStr);
        foreach ($tmp as $id) {
            $id = trim($id);
            if (is_numeric($id)) {
                $catIds[$id] = (int) $id;
                foreach ($this->getParentsCategoriesId($id) as $parentCategoryId) {
                    $catIds[$parentCategoryId] = $parentCategoryId;
                }
            }
        }

        return $catIds;
    }

    private function getParentsCategoriesId($categoryId)
    {
        if (!isset($this->categoriesIdList[$categoryId])) {
            $this->categoriesIdList[$categoryId] = [];
            $Category = $this->categoryRepo->find($categoryId);
            if ($Category) {
                $this->categoriesIdList[$categoryId][$Category->getId()] = $Category->getId();
                foreach ($Category->getPath() as $ParentCategory) {
                    $this->categoriesIdList[$categoryId][$ParentCategory->getId()] = $ParentCategory->getId();
                }
            }
        }

        return $this->categoriesIdList[$categoryId];
    }
}
