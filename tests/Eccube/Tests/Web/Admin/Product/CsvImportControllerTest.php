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
    public function createCsvFromArray(array $csv)
    {
        $dir = sys_get_temp_dir();
        $filepath = $dir.'products.csv';
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


    public function testCsvImport()
    {
        // 3商品生成
        $csv = $this->createCsvAsArray();
        $csv = array_merge($csv, $this->createCsvAsArray(false));

        // 規格1のみの商品
        $csvClass1Only = $this->createCsvAsArray(false);
        $csvClass1Only[0][13] = null; // 規格分類2(ID)
        $csv = array_merge($csv, $csvClass1Only);

        $this->filepath = $this->createCsvFromArray($csv);

        $crawler = $this->scenario();

        $Products = $this->app['eccube.repository.product']->findAll();

        $this->expected = 5;    // 3商品 + 既存2商品
        $this->actual = count($Products);
        $this->verify();

        $this->assertRegexp('/商品登録CSVファイルをアップロードしました。/u',
                            $crawler->filter('div.alert-success')->text());
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
     * $this->filepath のファイルを CSV アップロードし, 完了画面の crawler を返す.
     */
    public function scenario()
    {
        $file = new UploadedFile(
            $this->filepath,          // file path
            'products.csv',         // original name
            'text/csv',        // mimeType
            null,               // file size
            null,               // error
            true                // test mode
        );

        $crawler = $this->client->request(
            'POST',
            $this->app->path('admin_product_csv_import'),
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
