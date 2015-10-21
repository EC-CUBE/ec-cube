<?php

namespace Eccube\Tests\Repository;

use Eccube\Tests\EccubeTestCase;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Entity\ProductImage;
use Eccube\Entity\ProductStock;
use Eccube\Entity\Master\ProductListMax;
use Eccube\Entity\Master\ProductListOrderBy;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * ProductRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
class ProductRepositoryTest extends AbstractProductRepositoryTestCase
{
    public function _getSearchData($order){
        //オブジェクト生成
        $page_list_max_obj = new eccube\entity\master\productlistmax;
        $page_list_max_obj->setId(15);
        $page_list_max_obj->setName('15件');
        $page_list_max_obj->setRank(0);

        if(!empty($order) && $order === 1)
        {
            $sort_key = 1;
        }else{
            $sort_key = 2;
        }

        //オブジェクト生成
        $orderby_obj = new Eccube\Entity\Master\ProductListOrderBy;
        $orderby_obj->setId($sort_key);
        $orderby_obj->setName('価格順');
        $orderby_obj->setRank(0);

        return array(
                'mode' => null,
                'category_id' => null,
                'name' => null,
                'pageno' => null,
                'disp_number' => $page_list_max_obj,
                'orderby' => $orderby_obj
        );
    }

    public function testSetLimit()
    {
        $limit_bad_arg = 'a';
        $res = $this->app['eccube.repository.product']->setLimit($limit_bad_arg);
        $this->assertFalse($res);
    }

    public function testSetOffset()
    {
        $offset_bad_arg = 'a';
        $res = $this->app['eccube.repository.product']->setOffset($offset_bad_arg);
        $this->assertFalse($res);
    }

    public function testGetLimit()
    {
        $limit_good_arg = 1;
        $res = $this->app['eccube.repository.product']->setLimit($limit_good_arg);
        $res = $this->app['eccube.repository.product']->getLimit();
        $this->assertEquals($res, $limit_good_arg);
    }

    public function testGetOffset()
    {
        $offset_good_arg = 10;
        $res = $this->app['eccube.repository.product']->setOffset($offset_good_arg);
        $res = $this->app['eccube.repository.product']->getOffset();
        $this->assertEquals($res, $offset_good_arg);
    }

    public function testGetObjectCollectionBySearchData()
    {
        $offset_good_arg = 10;
        $search_datas = $this->_getSearchData(1);
        //ページネーション初期値設定
        $pageno = !empty($search_datas['pageno']) ? $search_datas['pageno'] : 1;
        $maxpage = $search_datas['disp_number']->getId();
        $app['eccube.repository.product']->setOffset((($pageno - 1) * $maxpage));
        $app['eccube.repository.product']->setLimit($maxpage);

        //件数カウント
        $count = $app['eccube.repository.product']->countObjectCollectionBySearchData($search_datas);

        // ソート:価格降順ブジェクト配列取得
        $cobj = $app['eccube.repository.product']->getObjectCollectionBySearchData($search_datas);
        $pagination = $app['paginator']()->paginate(array());
        $pagination->setCurrentPageNumber($pageno);
        $pagination->setItemNumberPerPage($maxpage);
        $pagination->setTotalItemCount($count);
        $pagination->setItems($cobj);
        $paginate_num = $pagination->getItems();

        $this->assertTrue(count($paginate_num), count($maxpage));
    }

    public function testGetQueryBuilderBySearchData()
    {
        $offset_good_arg = 10;
        $search_datas = $this->_getSearchData(2);
        //ページネーション初期値設定
        $pageno = !empty($search_datas['pageno']) ? $search_datas['pageno'] : 1;
        $maxpage = $search_datas['disp_number']->getId();

        $qb = $app['eccube.repository.product']->getQueryBuilderBySearchData($searchData);
        $pagination = $app['paginator']()->paginate(
            $qb,
            $pageno,
            $maxpage
        );

        $paginate_num = $pagination->getItems();

        $this->assertTrue(count($paginate_num), count($maxpage));
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
}
