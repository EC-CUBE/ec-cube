<?php

namespace Eccube\Tests\Repository;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * ProductRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
class ProductRepositoryTest extends AbstractProductRepositoryTestCase
{

    public function testGet()
    {
        $Product = $this->app['eccube.repository.product']->findOneBy(
            array('name' => '商品-1')
        );
        $this->assertTrue($Product instanceof \Eccube\Entity\Product);

        $product_id = $Product->getId();
        $Result = $this->app['eccube.repository.product']->get($product_id);

        $this->expected = $product_id;
        $this->actual = $Result->getId();
        $this->verify();
    }

    public function testGetWithException()
    {
        try {
            $Product = $this->app['eccube.repository.product']->get(9999);
            $this->fail();
        } catch (NotFoundHttpException $e) {
            $this->expected = 404;
            $this->actual = $e->getStatusCode();
        }
        $this->verify();
    }

    public function testGetFavoriteProductQueryBuilderByCustomer()
    {
        $Customer = $this->createCustomer();
        $this->app['orm.em']->persist($Customer);

        $this->createFavorites($Customer);

        // 3件中, 1件は非表示にしておく
        $ProductStatus = $this->app['eccube.repository.master.product_status']->find(\Eccube\Entity\Master\ProductStatus::DISPLAY_HIDE);
        $Products = $this->app['eccube.repository.product']->findAll();
        $Products[0]->setStatus($ProductStatus);
        $this->app['orm.em']->flush();

        $qb = $this->app['eccube.repository.product']->getFavoriteProductQueryBuilderByCustomer($Customer);
        $Favorites = $qb
            ->getQuery()
            ->getResult();

        $this->expected = 2;
        $this->actual = count($Favorites);
        $this->verify('お気に入りの件数は'.$this->expected.'件');
    }
}
