<?php

namespace Eccube\Tests\Repository;

use Eccube\Tests\EccubeTestCase;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Entity\ProductImage;
use Eccube\Entity\ProductStock;
use Doctrine\ORM\NoResultException;
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
        $Disp = $this->app['eccube.repository.master.disp']->find(\Eccube\Entity\Master\Disp::DISPLAY_HIDE);
        $Products = $this->app['eccube.repository.product']->findAll();
        $Products[0]->setStatus($Disp);
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
