<?php

namespace Eccube\Tests\Web\Admin\Product;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Eccube\Common\Constant;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CsvImportControllerTest extends AbstractAdminWebTestCase
{
    protected $Products;
    protected $filepath;

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
        $csv = array(
            '商品ID' => null,
            '公開ステータス(ID)' => 1,
            '商品名' => $faker->word,
            'ショップ用メモ欄' => $faker->paragraph,
            '商品説明(一覧)' => $faker->paragraph,
            '商品説明(詳細)' => $faker->text,
            '検索ワード' => $faker->word,
            'フリーエリア' => $faker->paragraph,
            '商品削除フラグ' => 0,
            '商品画像' => $faker->word.'.jpg,'.$faker->word.'.jpg',
            '商品カテゴリ(ID)' => '5,6',
            '商品種別(ID)' => 1,
            '規格分類1(ID)' => 3,
            '規格分類2(ID)' => 6,
            '発送日目安(ID)' => 1,
            '商品コード' => $faker->word,
            '在庫数' => 100,
            '在庫数無制限フラグ' => 0,
            '販売制限数' => null,
            '通常価格' => $faker->randomNumber(5),
            '販売価格' => $faker->randomNumber(5),
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
        $csvClass1Only[0][13] = null; // 規格分類2(ID)
        $csvClass1Only[0][15] = 'class1-only'; // 商品コード
        $csv = array_merge($csv, $csvClass1Only);

        $this->filepath = $this->createCsvFromArray($csv);

        $crawler = $this->scenario();

        $Products = $this->app['eccube.repository.product']->findAll();

        $this->expected = 5;    // 3商品 + 既存2商品
        $this->actual = count($Products);
        $this->verify();

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
        $csv[1][15] = 'add-class';             // 商品コード

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
    }

    public function testCsvTemplateWithProduct()
    {
        // 一旦別の変数に代入しないと, config 以下の値を書きかえることができない
        $config = $this->app['config'];
        $config['csv_export_encoding'] = 'UTF-8'; // SJIS だと比較できないので UTF-8 に変更しておく
        $this->app['config'] = $config;

        $this->expectOutputString('商品ID,公開ステータス(ID),商品名,ショップ用メモ欄,商品説明(一覧),商品説明(詳細),検索ワード,フリーエリア,商品削除フラグ,商品画像,商品カテゴリ(ID),商品種別(ID),規格分類1(ID),規格分類2(ID),発送日目安(ID),商品コード,在庫数,在庫数無制限フラグ,販売制限数,通常価格,販売価格,送料,商品規格削除フラグ'."\n");

        $crawler = $this->client->request(
            'GET',
            $this->app->path('admin_product_csv_template', array('type' => 'product'))
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testCsvCategory()
    {
        $this->filepath = __DIR__.'/categories.csv';
        copy(__DIR__.'/../../../../../Fixtures/categories.csv', $this->filepath); // 削除されてしまうのでコピーしておく

        $crawler = $this->scenario('admin_product_category_csv_import', 'categories.csv');

        $Categories = $this->app['eccube.repository.category']->findAll();

        $this->expected = 6;
        $this->actual = count($Categories);
        $this->verify();

        $this->assertRegexp('/カテゴリ登録CSVファイルをアップロードしました。/u',
                            $crawler->filter('div.alert-success')->text());
    }

    public function testCsvTemplateWithCategory()
    {
        // 一旦別の変数に代入しないと, config 以下の値を書きかえることができない
        $config = $this->app['config'];
        $config['csv_export_encoding'] = 'UTF-8'; // SJIS だと比較できないので UTF-8 に変更しておく
        $this->app['config'] = $config;

        $this->expectOutputString('カテゴリID,カテゴリ名,親カテゴリID'."\n");

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
}
