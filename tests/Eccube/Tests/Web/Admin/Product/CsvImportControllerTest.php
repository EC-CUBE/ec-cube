<?php

namespace Eccube\Tests\Web\Admin\Product;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CsvImportControllerTest extends AbstractAdminWebTestCase
{
    protected $Products;
    protected $filepath;
    
    private $categoriesIdList = array();

    public function setUp()
    {
        parent::setUp();
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
     * @return array CSVを生成するための配列
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

        $csv = array(
            '商品ID' => null,
            '公開ステータス(ID)' => 1,
            '商品名' => "商品名".$faker->word."商品名",
            'ショップ用メモ欄' => "ショップ用メモ欄".$faker->paragraph."ショップ用メモ欄",
            '商品説明(一覧)' => "商品説明(一覧)".$faker->paragraph."商品説明(一覧)",
            '商品説明(詳細)' => "商品説明(詳細)".$faker->text."商品説明(詳細)",
            '検索ワード' => "検索ワード".$faker->word."検索ワード",
            'フリーエリア' => "フリーエリア".$faker->paragraph."フリーエリア",
            '商品削除フラグ' => 0,
            '商品画像' => $faker->word.'.jpg,'.$faker->word.'.jpg',
            '商品カテゴリ(ID)' => '5,6',
            'タグ(ID)' => '1,2',
            '商品種別(ID)' => 1,
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
            '商品規格削除フラグ' => 0
        );
        $result = array();
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

        $Products = $this->app['eccube.repository.product']->findAll();

        $this->expected = 5;    // 3商品 + 既存2商品
        $this->actual = count($Products);
        $this->verify();

        // ProductCategoryTest
        //カテゴリーIDs
        foreach($csv as $csvRow){
            $csvCat[md5($csvRow[2])] = $csvRow[10];
        }
        foreach ($Products as $Product){
            $nameHash = md5($Product->getName());
            if(!isset($csvCat[$nameHash])){
                continue;
            }
            // expected categories is
            $expectedIds = $this->getExpectedCategoriesIdList($csvCat[$nameHash]);
            $actualIds = array();
            /* @var $Product \Eccube\Entity\Product */
            foreach ($Product->getProductCategories() as $ProductCategory){
                /* @var $ProductCategory \Eccube\Entity\ProductCategory */
                $actualIds[$ProductCategory->getCategoryId()] = $ProductCategory->getCategoryId();
                $this->expected = $expectedIds[$ProductCategory->getCategoryId()];
                $this->actual = $ProductCategory->getCategoryId();
                $this->verify();
            }
            foreach($expectedIds as $catId){
                $this->expected = $catId;
                $this->actual = $actualIds[$catId];
                $this->verify();
            }
        }

        $this->assertRegexp('/商品登録CSVファイルをアップロードしました。/u',
            $crawler->filter('div.alert-success')->text());

        // 規格1のみ商品の確認
        // dtb_product_class.del_flg = 1 の確認をしたいので PDO で取得
        $pdo = $this->app['orm.em']->getConnection()->getWrappedConnection();
        $sql = "SELECT * FROM dtb_product_class WHERE product_code = 'class1-only' ORDER BY del_flg DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $this->expected = 2;
        $this->actual = count($result);
        $this->verify('取得できるのは2行');

        $this->expected = 1;
        $this->actual = $result[0]['del_flg'];
        $this->verify('result[0] は del_flg = 1');

        $this->expected = null;
        $this->actual = $result[0]['class_category_id1'];
        $this->verify('class_category_id1 は null');

        $this->expected = null;
        $this->actual = $result[0]['class_category_id2'];
        $this->verify('class_category_id2 は null');

        // del_flg = 0 の行の確認
        $this->expected = 0;
        $this->actual = $result[1]['del_flg'];
        $this->verify('result[1] は del_flg = 0');

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

        $Products = $this->app['eccube.repository.product']->findAll();

        $this->expected = 12;
        $this->actual = count($Products);
        $this->verify();

        $new_count = 0;
        foreach ($Products as $Product) {
            $ProductClasses = $Product->getProductClasses();
            foreach ($ProductClasses as $ProductClass) {
                if (preg_match('/fork-0[0-9]-new/', $ProductClass->getCode())) {
                    $new_count++;
                }
            }
            
            // categories
            $dateTimeNow = new \DateTime('-20 minutes');
            // check only new records
            if($Product->getUpdateDate() > $dateTimeNow){
                $expectedCategoryIds = array();
                $actualCategoryIds = array();
                foreach ($Product->getProductCategories() as $ProductCategory){
                    $expectedCategoryIds[$ProductCategory->getCategoryId()] = $ProductCategory->getCategoryId();
                    $actualCategoryIds[$ProductCategory->getCategoryId()] = $ProductCategory->getCategoryId();
                    foreach($this->getParentsCategoriesId($ProductCategory->getCategoryId()) as $catId){
                        $expectedCategoryIds[$catId] = $catId;
                    }
                }
                foreach ($expectedCategoryIds as $catId){
                    $this->expected = $catId;
                    $this->actual = $actualCategoryIds[$catId];
                    $this->verify();
                }
            }
        }

        $this->expected = 3;
        $this->actual = $new_count;
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

        $Products = $this->app['eccube.repository.product']->findAll();

        $this->expected = 2;    // 既存2商品
        $this->actual = count($Products);
        $this->verify();

        $this->assertRegexp('/商品登録CSVファイルをアップロードしました。/u',
            $crawler->filter('div.alert-success')->text());

        // 規格1のみ商品の確認
        // dtb_product_class.del_flg = 1 の確認をしたいので PDO で取得
        $pdo = $this->app['orm.em']->getConnection()->getWrappedConnection();
        $sql = "SELECT * FROM dtb_product_class WHERE product_id = 2 ORDER BY del_flg DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $this->expected = 2;
        $this->actual = count($result);
        $this->verify('取得できるのは2行');

        $this->expected = 1;
        $this->actual = $result[0]['del_flg'];
        $this->verify('result[0] は del_flg = 1');

        $this->expected = null;
        $this->actual = $result[0]['class_category_id1'];
        $this->verify('class_category_id1 は null');

        $this->expected = null;
        $this->actual = $result[0]['class_category_id2'];
        $this->verify('class_category_id2 は null');

        // del_flg = 0 の行の確認
        $this->expected = 0;
        $this->actual = $result[1]['del_flg'];
        $this->verify('result[1] は del_flg = 0');

        $this->expected = 3;
        $this->actual = $result[1]['class_category_id1'];
        $this->verify('class_category_id1 は 3');

        $this->expected = 6;
        $this->actual = $result[1]['class_category_id2'];
        $this->verify('class_category_id2 は 6');
        
        // ProductCategoryTest
        //カテゴリーIDs
        foreach($csv as $csvRow){
            $csvCat[md5($csvRow[2])] = $csvRow[10];
        }
        foreach ($Products as $Product){
            $nameHash = md5($Product->getName());
            if(!isset($csvCat[$nameHash])){
                continue;
            }
            // expected categories is
            $expectedIds = $this->getExpectedCategoriesIdList($csvCat[$nameHash]);
            $actualIds = array();
            /* @var $Product \Eccube\Entity\Product */
            foreach ($Product->getProductCategories() as $ProductCategory){
                /* @var $ProductCategory \Eccube\Entity\ProductCategory */
                $actualIds[$ProductCategory->getCategoryId()] = $ProductCategory->getCategoryId();
                $this->expected = $expectedIds[$ProductCategory->getCategoryId()];
                $this->actual = $ProductCategory->getCategoryId();
                $this->verify();
            }
            foreach($expectedIds as $catId){
                $this->expected = $catId;
                $this->actual = $actualIds[$catId];
                $this->verify();
            }
        }
        
    }

    public function testCsvTemplateWithProduct()
    {
        // 一旦別の変数に代入しないと, config 以下の値を書きかえることができない
        $config = $this->app['config'];
        $config['csv_export_encoding'] = 'UTF-8'; // SJIS だと比較できないので UTF-8 に変更しておく
        $this->app['config'] = $config;

        $this->expectOutputString('商品ID,公開ステータス(ID),商品名,ショップ用メモ欄,商品説明(一覧),商品説明(詳細),検索ワード,フリーエリア,商品削除フラグ,商品画像,商品カテゴリ(ID),タグ(ID),商品種別(ID),規格分類1(ID),規格分類2(ID),発送日目安(ID),商品コード,在庫数,在庫数無制限フラグ,販売制限数,通常価格,販売価格,送料,商品規格削除フラグ'."\n");

        $crawler = $this->client->request(
            'GET',
            $this->app->path('admin_product_csv_template', array('type' => 'product'))
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

        $Categories = $this->app['eccube.repository.category']->findAll();

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
        $csv = array(
            array('カテゴリID', 'カテゴリ名', '親カテゴリID', 'カテゴリ削除フラグ'),
            array('', '新カテゴリ', '', '')
        );
        $this->filepath = $this->createCsvFromArray($csv, 'categories.csv');

        $crawler = $this->scenario('admin_product_category_csv_import', 'categories.csv');

        $Categories = $this->app['eccube.repository.category']->findBy(array('name' => '新カテゴリ'));

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

        $csv = array(
            array('カテゴリ名'),
            array('新カテゴリ')
        );
        $this->filepath = $this->createCsvFromArray($csv, 'categories.csv');

        $crawler = $this->scenario('admin_product_category_csv_import', 'categories.csv');

        $Categories = $this->app['eccube.repository.category']->findBy(array('name' => '新カテゴリ'));

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

        $categories = $this->app['eccube.repository.category']->findAll();
        $this->expected = count($categories);

        $csv = array(
            array('カテゴリID', 'カテゴリ名'),
            array(null, null),
        );
        $this->filepath = $this->createCsvFromArray($csv, 'categories.csv');

        $crawler = $this->scenario('admin_product_category_csv_import', 'categories.csv');

        $arrCategory = $this->app['eccube.repository.category']->findAll();
        $this->actual = count($arrCategory);
        $this->verify();

        $this->assertRegexp('/2行目のカテゴリ名が設定されていません。/u', $crawler->filter('div#upload_box__body')->text());
    }

    /**
     * Import do not exist category name column.
     */
    public function testCsvCategoryWithoutCategoryNameColumn()
    {
        $this->filepath = __DIR__.'/categories.csv';
        copy(__DIR__.'/../../../../../Fixtures/categories.csv', $this->filepath); // 削除されてしまうのでコピーしておく

        $categories = $this->app['eccube.repository.category']->findAll();
        $this->expected = count($categories);

        $csv = array(
            array('カテゴリID'),
            array(''),
        );
        $this->filepath = $this->createCsvFromArray($csv, 'categories.csv');

        $crawler = $this->scenario('admin_product_category_csv_import', 'categories.csv');

        $arrCategory = $this->app['eccube.repository.category']->findAll();
        $this->actual = count($arrCategory);
        $this->verify();

        $this->assertRegexp('/CSVのフォーマットが一致しません。/u', $crawler->filter('div#upload_box__body')->text());
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
        $csv = array(
            array('カテゴリ名','カテゴリID'),
            array($categoryName,''),
        );
        $this->filepath = $this->createCsvFromArray($csv, 'categories.csv');

        $crawler = $this->scenario('admin_product_category_csv_import', 'categories.csv');

        $arrCategory = $this->app['eccube.repository.category']->findBy(array('name' => $categoryName));
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
        // 一旦別の変数に代入しないと, config 以下の値を書きかえることができない
        $config = $this->app['config'];
        $config['csv_export_encoding'] = 'UTF-8'; // SJIS だと比較できないので UTF-8 に変更しておく
        $this->app['config'] = $config;

        $this->expectOutputString('カテゴリID,カテゴリ名,親カテゴリID,カテゴリ削除フラグ'."\n");

        $crawler = $this->client->request(
            'GET',
            $this->app->path('admin_product_csv_template', array('type' => 'category'))
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * $this->filepath のファイルを CSV アップロードし, 完了画面の crawler を返す.
     */
    public function scenario($bind = 'admin_product_csv_import', $original_name = 'products.csv')
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
            $this->app->path($bind),
            array(
                'admin_csv_import' => array(
                    '_token' => 'dummy',
                    'import_file' => $file
                )
            ),
            array('import_file' => $file)
        );
        return $crawler;
    }
    
    private function getExpectedCategoriesIdList($categoriesStr)
    {
        $catIds = array();
        $tmp = explode(',', $categoriesStr);
        foreach($tmp as $id){
            $id = trim($id);
            if(is_numeric($id)){
                $catIds[$id] = (int) $id;
                foreach ($this->getParentsCategoriesId($id) as $parentCategoryId){
                    $catIds[$parentCategoryId] = $parentCategoryId;
                }
            }
        }
        return $catIds;
    }
    
    private function getParentsCategoriesId($categoryId)
    {
        if(!isset($this->categoriesIdList[$categoryId])){
            $this->categoriesIdList[$categoryId] = array();
            $Category = $this->app['eccube.repository.category']->find($categoryId);
            if($Category){
                $this->categoriesIdList[$categoryId][$Category->getId()] = $Category->getId(); 
                foreach($Category->getPath() as $ParentCategory){
                    $this->categoriesIdList[$categoryId][$ParentCategory->getId()] = $ParentCategory->getId(); 
                }
            }
        }
        
        return $this->categoriesIdList[$categoryId];
    }
}
