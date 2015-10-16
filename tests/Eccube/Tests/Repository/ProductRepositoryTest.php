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
use Eccube\Entity\Master\ProductListMax;
use Eccube\Entity\Master\ProductListOrderBy;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * ProductRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
class ProductRepositoryTest extends AbstractProductRepositoryTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    //サーチデータモック
    private function createSearchDatas()
    {
        //商品表示件数マスタ初期化
        $pd_list_max_obj = new ProductListMax();
        //表示件数15件を設定
        $pd_list_max_obj->setId(15);
        $pd_list_max_obj->setName('15件');
        $pd_list_max_obj->setRank(0);

        //商品検索並び替え種別マスタ初期化
        $pd_order_obj = new ProductListOrderBy();
        //商品検索並び順「価格」設定
        $pd_order_obj->setId(1);
        $pd_order_obj->setName('価格');
        $pd_order_obj->setRank(0);

        $ref = array(
            'mode' => null,
            'category_id' => null,
            'name' => null,
            'pageno' => null,
            'disp_number' => $pd_list_max_obj,
            'orderby' => $pd_order_obj
        );

        return $ref;
    }

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

    /*
    *   @see https://github.com/EC-CUBE/ec-cube/issue/954
    */
    public function testGetQueryBuilderBySearchData()
    {
        //表示件数:15件 / ソート:価格
        $searchData = $this->createSearchDatas();

        $actual = $this->app['eccube.repository.product']->findAll();

        //ページネートの件数が正しく取得出来ているか検証
        $cq = $this->app['eccube.repository.product']->getQueryBuilderBySearchData($searchData);
        $pagination = $this->app['paginator']()->paginate(
            $cq,
            !empty($searchData['pageno']) ? $searchData['pageno'] : 1,
            $searchData['disp_number']->getId()
        );
        $items = $pagination->getItems();

        $this->assertEquals(count($actual),count($items));
    }
}
