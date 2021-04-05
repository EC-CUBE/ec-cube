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
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Entity\ProductImage;
use Eccube\Repository\CategoryRepository;
use Eccube\Repository\ProductRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Faker\Generator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
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
        $this->productRepo = $this->entityManager->getRepository(\Eccube\Entity\Product::class);
        $this->categoryRepo = $this->entityManager->getRepository(\Eccube\Entity\Category::class);
        $this->filepath = __DIR__.'/products.csv';
        copy(__DIR__.'/../../../../../Fixtures/products.csv', $this->filepath); // 削除されてしまうのでコピーしておく

        $fs = new Filesystem();
        $fs->mkdir($this->eccubeConfig['eccube_csv_temp_realdir']);
        $fs->remove($this->getCsvTempFiles());
    }

    public function tearDown()
    {
        if (file_exists($this->filepath)) {
            unlink($this->filepath);
        }

        $fs = new Filesystem();
        $fs->remove($this->getCsvTempFiles());

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
            '税率' => 0,
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

        $this->assertRegexp('/CSVファイルをアップロードしました/u',
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

        $this->assertRegexp('/CSVファイルをアップロードしました/u',
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

        $this->assertRegexp('/CSVファイルをアップロードしました/u',
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
        self::$container->setParameter('eccube.constants', $config);

        $this->expectOutputString('商品ID,公開ステータス(ID),商品名,ショップ用メモ欄,商品説明(一覧),商品説明(詳細),検索ワード,フリーエリア,商品削除フラグ,商品画像,商品カテゴリ(ID),タグ(ID),販売種別(ID),規格分類1(ID),規格分類2(ID),発送日目安(ID),商品コード,在庫数,在庫数無制限フラグ,販売制限数,通常価格,販売価格,送料,税率'."\n");

        $this->client->request(
            'GET',
            $this->generateUrl('admin_product_csv_template', ['type' => 'product'])
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * 省略可能なカラムが省略されている際に更新されないことを確認する
     */
    public function testCsvImportWithExistsProductsExceptOptionalColumns()
    {
        $Products = $this->productRepo->findAll();
        $beforeProduct = $this->productRepo->findOneBy([], ['id' => 'ASC']);
        $csv = [[
            '商品ID',
            '公開ステータス(ID)',
            '商品名',
            '販売種別(ID)',
            '規格分類1(ID)',
            '規格分類2(ID)',
            '発送日目安(ID)',
            '商品コード',
            '在庫数',
            '在庫数無制限フラグ',
            '販売制限数',
            '通常価格',
            '販売価格',
            '送料',
        ]];
        foreach ($Products as $Product) {
            foreach ($Product->getProductClasses() as $ProductClass) {
                $csv[] = [
                    $Product->getId(),
                    $Product->getStatus()->getId(),
                    $Product->getName(),
                    $ProductClass->getSaleType()->getId(),
                    $ProductClass->getClassCategory1() == null ? null : $ProductClass->getClassCategory1()->getId(),
                    $ProductClass->getClassCategory2() == null ? null : $ProductClass->getClassCategory2()->getId(),
                    $ProductClass->getDeliveryDuration() == null ? null : $ProductClass->getDeliveryDuration()->getId(),
                    $ProductClass->getCode(),
                    $ProductClass->getStock(),
                    $ProductClass->isStockUnlimited(),
                    $ProductClass->getSaleLimit(),
                    (int) $ProductClass->getPrice01(),
                    (int) $ProductClass->getPrice02(),
                    $ProductClass->getDeliveryFee(),
                ];
            }
        }
        $this->filepath = $this->createCsvFromArray($csv);
        $crawler = $this->scenario();
        $this->assertRegexp('/CSVファイルをアップロードしました/u',
            $crawler->filter('div.alert-success')->text());
        $afterProduct = $this->productRepo->findOneBy([], ['id' => 'ASC']);
        $this->assertEquals($beforeProduct->getDescriptionDetail(), $afterProduct->getDescriptionDetail());
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

        $this->assertRegexp('/CSVファイルをアップロードしました/u', $crawler->filter('div.alert-success')->text());
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

        $this->assertRegexp('/CSVファイルをアップロードしました/u', $crawler->filter('div.alert-success')->text());
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

        $this->assertRegexp('/CSVファイルをアップロードしました/u', $crawler->filter('div.alert-success')->text());
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

        $this->assertRegexp('/2行目のカテゴリ名が設定されていません。/u', $crawler->filter('#upload-form > div:nth-child(4)')->text());
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

        $this->assertRegexp('/CSVのフォーマットが一致しません/u', $crawler->filter('#upload-form > div:nth-child(4)')->text());
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

        $this->assertRegexp('/CSVファイルをアップロードしました/u', $crawler->filter('div.alert-success')->text());
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
        self::$container->setParameter('eccube.constants', $config);

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
        $this->assertRegexp('/CSVファイルをアップロードしました/u', $crawler->filter('div.alert-success')->text());
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
        $this->assertRegexp('/CSVファイルをアップロードしました/u', $crawler->filter('div.alert-success')->text());

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

        $this->assertRegexp('/CSVファイルをアップロードしました/u', $crawler->filter('div.alert-success')->text());
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
     * @see https://github.com/EC-CUBE/ec-cube/pull/4177
     *
     * @dataProvider dataDeliveryFeeProvider
     */
    public function testImportDeliveryFee($optionDeliveryFee, $expected)
    {
        /** @var BaseInfo $BaseInfo */
        $BaseInfo = $this->entityManager->find(BaseInfo::class, 1);
        $BaseInfo->setOptionProductDeliveryFee($optionDeliveryFee);
        $this->entityManager->flush($BaseInfo);
        $this->entityManager->clear();

        $csv[] = ['公開ステータス(ID)', '商品名', '販売種別(ID)', '在庫数無制限フラグ', '販売価格', '送料'];
        $csv[] = [1, '送料更新用', 1, 1, 1, 5000];
        $this->filepath = $this->createCsvFromArray($csv);

        $crawler = $this->scenario();
        $this->assertRegexp('/CSVファイルをアップロードしました/u', $crawler->filter('div.alert-success')->text());

        $Product = $this->productRepo->findOneBy(['name' => '送料更新用']);
        $ProductClass = $Product->getProductClasses()[0];
        $this->expected = $expected;
        $this->actual = $ProductClass->getDeliveryFee();
        $this->verify();
    }

    public function dataDeliveryFeeProvider()
    {
        return [
            [true, 5000],   // 送料オプション有効時は更新
            [false, null],  // 送料オプション無効時はスキップ
        ];
    }

    /**
     * Data for case check product id.
     *
     * @return array
     */
    public function dataProductIdProvider()
    {
        return [
            [99999, '2行目の商品IDが存在しません'],
            ['abc', '2行目の商品IDが存在しません'],
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
    protected function scenario($bind = 'admin_product_csv_import', $original_name = 'products.csv', $isXmlHttpRequest = false)
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
            ['import_file' => $file],
            $isXmlHttpRequest ? ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'] : []
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

    /**
     * @dataProvider dataDescriptionDetailProvider
     *
     * @see https://github.com/EC-CUBE/ec-cube/pull/4218
     */
    public function testImportDescriptionetail($length, $selector, $pattern)
    {
        $csv = [];
        $csv[] = ['公開ステータス(ID)', '商品名', '販売種別(ID)', '在庫数無制限フラグ', '販売価格', '商品説明(詳細)'];
        $csv[] = [1, '商品詳細テスト用', 1, 1, 1, str_repeat('a', $length)];
        $this->filepath = $this->createCsvFromArray($csv);

        $crawler = $this->scenario();
        $this->assertRegexp($pattern, $crawler->filter($selector)->text());
    }

    public function dataDescriptionDetailProvider()
    {
        return [
            [2999, 'div.alert-success', '/CSVファイルをアップロードしました/u'],
            [3000, 'div.alert-success', '/CSVファイルをアップロードしました/u'],
            [3001, 'div.text-danger', '/2行目の商品説明\(詳細\)は3000文字以下の文字列を指定してください。/u'],
        ];
    }

    /**
     * @see https://github.com/EC-CUBE/ec-cube/pull/4281
     *
     * @dataProvider dataTaxRuleProvider
     *
     * @param $optionTaxRule
     * @param $preTaxRate
     * @param $postTaxRate
     *
     * @throws \Exception
     */
    public function testImportTaxRule($optionTaxRule, $preTaxRate, $postTaxRate)
    {
        /** @var BaseInfo $BaseInfo */
        $BaseInfo = $this->entityManager->find(BaseInfo::class, 1);
        $BaseInfo->setOptionProductTaxRule($optionTaxRule);
        $this->entityManager->flush($BaseInfo);
        $this->entityManager->clear();

        $csv[] = ['公開ステータス(ID)', '商品名', '販売種別(ID)', '在庫数無制限フラグ', '販売価格', '税率'];
        $csv[] = [1, '商品別税率テスト用', 1, 1, 1, $preTaxRate];
        $this->filepath = $this->createCsvFromArray($csv);

        $crawler = $this->scenario();
        $this->assertRegexp('/CSVファイルをアップロードしました/u', $crawler->filter('div.alert-success')->text());

        $Product = $this->productRepo->findOneBy(['name' => '商品別税率テスト用']);
        /** @var ProductClass $ProductClass */
        $ProductClass = $Product->getProductClasses()[0];
        $this->expected = $postTaxRate;
        if ($ProductClass->getTaxRule() == null) {
            $this->actual = $ProductClass->getTaxRule();
        } else {
            $this->actual = $ProductClass->getTaxRule()->getTaxRate();
        }
        $this->verify();
    }

    public function dataTaxRuleProvider()
    {
        return [
            [true, 0, 0],
            [true, 12, 12],
            [true, '', null],
            [false, 0, null],
            [false, 12, null],
            [false, '', null],
        ];
    }

    /**
     * 商品を削除する際に、他の商品画像が参照しているファイルは削除せず、それ以外は削除することをテスト
     */
    public function testDeleteImage()
    {
        /** @var \Eccube\Tests\Fixture\Generator $generator */
        $generator = self::$container->get(\Eccube\Tests\Fixture\Generator::class);
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
            ->setSortNo(999);
        $Product2->addProductImage($NewProduct2Image);
        $this->entityManager->persist($NewProduct2Image);
        $this->entityManager->flush();

        $csv[] = ['商品ID', '公開ステータス(ID)', '商品名', '商品削除フラグ', '販売種別(ID)', '販売価格'];
        $csv[] = [$Product1->getId(), '1', 'hoge', '1', '1', '1000'];
        $this->filepath = $this->createCsvFromArray($csv);
        $this->scenario();

        $dir = __DIR__.'/../../../../../../html/upload/save_image/';
        $this->assertTrue(file_exists($dir.$DuplicatedImage->getFileName()));
        $this->assertFalse(file_exists($dir.$NotDuplicatedImage->getFileName()));
    }

    /**
     * @see https://github.com/EC-CUBE/ec-cube/issues/4781
     */
    public function testSjisWinCsvTest()
    {
        // CSV生成
        $csv = $this->createCsvAsArray();
        $csv[1][2] = 'テスト①'; // 商品名：機種依存文字で設定
        $csv[1][3] = 'sjis-win-test';
        $this->filepath = $this->createCsvFromArray($csv);

        // sjis-winに変換
        $content = file_get_contents($this->filepath);
        $content = mb_convert_encoding($content, 'sjis-win', 'UTF-8');
        file_put_contents($this->filepath, $content);

        $this->scenario();

        $Product = $this->productRepo->findOneBy(['note' => 'sjis-win-test']);

        // 文字化けしないことを確認
        $this->expected = 'テスト①';
        $this->actual = $Product->getName();
        $this->verify();
    }

    /**
     * @dataProvider splitCsvDataProvider
     */
    public function testSplitCsv($lineNo, $expecedFileNo)
    {
        list($header, $row) = $this->createCsvAsArray();
        $csv = [$header];
        for ($i = 0; $i < $lineNo; $i++) {
            $csv[] = $row;
        }
        $this->filepath = $this->createCsvFromArray($csv);

        $this->scenario('admin_product_csv_split', 'products.csv', true);

        $response = $this->client->getResponse();
        $this->assertTrue($response->isSuccessful());

        $json = \json_decode($response->getContent(), true);
        $this->assertTrue($json['success']);
        $this->assertNotEmpty($json['file_name']);
        $this->assertEquals($expecedFileNo, $json['max_file_no']);

        $files = $this->getCsvTempFiles();
        $this->assertEquals($expecedFileNo, count($files), $expecedFileNo.'ファイル生成されているはず');
    }

    public function splitCsvDataProvider()
    {
        return [
            [0, 1],
            [1, 1],
            [99, 1],
            [100, 1],
            [101, 2],
            [199, 2],
            [200, 2],
            [201, 3],
        ];
    }

    public function testImportCsv()
    {
        $fileName = 'product.csv';
        $fileNo = 1;

        $this->filepath = $this->createCsvFromArray($this->createCsvAsArray());
        copy($this->filepath, $this->eccubeConfig['eccube_csv_temp_realdir'].'/'.$fileName);

        $this->client->request(
            'POST',
            $this->generateUrl('admin_product_csv_split_import'),
            [
                'file_name' => $fileName,
                'file_no' => $fileNo,
            ],
            [],
            ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']
        );

        $response = $this->client->getResponse();
        $this->assertTrue($response->isSuccessful());

        $json = \json_decode($response->getContent(), true);
        $this->assertTrue($json['success']);
        $this->assertEquals('2行目〜2行目を登録しました', $json['success_message']);
    }

    public function testCleanupCsv()
    {
        $fileName = 'product.csv';
        touch($this->eccubeConfig['eccube_csv_temp_realdir'].'/'.$fileName);

        $this->client->request(
            'POST',
            $this->generateUrl('admin_product_csv_split_cleanup'),
            [
                'files' => [$fileName],
            ],
            [],
            ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']
        );

        $response = $this->client->getResponse();
        $this->assertTrue($response->isSuccessful());

        $json = \json_decode($response->getContent(), true);
        $this->assertTrue($json['success']);
        $this->assertFalse(file_exists($this->eccubeConfig['eccube_csv_temp_realdir'].'/'.$fileName));
    }

    private function getCsvTempFiles()
    {
        return Finder::create()
            ->in($this->eccubeConfig['eccube_csv_temp_realdir'])
            ->name('*.csv')
            ->files();
    }
}
