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

    public function testCsvImport()
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
    }
}
