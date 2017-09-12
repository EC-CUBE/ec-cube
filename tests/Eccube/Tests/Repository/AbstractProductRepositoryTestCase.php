<?php
namespace Eccube\Tests\Repository;

use Eccube\Entity\CustomerFavoriteProduct;
use Eccube\Tests\EccubeTestCase;

/**
 * ProductRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
abstract class AbstractProductRepositoryTestCase extends EccubeTestCase
{
    public function setUp()
    {
        parent::setUp();
        $tables = array(
            'dtb_product_image',
            'dtb_product_stock',
            'dtb_product_class',
            'dtb_product_category',
            'dtb_product'
        );
        $this->deleteAllRows($tables);
        for ($i = 0; $i < 3; $i++) {
            $this->createProduct('商品-'.$i);
        }
    }

    protected function createFavorites($Customer)
    {
        $Products = $this->app['eccube.repository.product']->findAll();
        foreach ($Products as $Product) {
            $Fav = new CustomerFavoriteProduct();
            $Fav->setProduct($Product)
                ->setCustomer($Customer);
            $this->app['orm.em']->persist($Fav);
        }
        $this->app['orm.em']->flush();
    }
}
