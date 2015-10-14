<?php

namespace Eccube\Tests\Repository;

use Eccube\Tests\EccubeTestCase;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\Product;
use Eccube\Entity\ProductListMax;
use Eccube\Entity\ProductListOrderBy;


/**
 * ProductRepository test cases.
 *
 * @author  Yasumasa Yoshinaga
 */
class ProductRepositoryTest extends EccubeTestCase
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

    public function testGetQueryBuilderBySearchData()
    {
        //---------------------#670に対するテスト---------------------
        //表示件数:15件 / ソート:価格
        $searchData = $this->createSearchDatas();

        $actual = $this->app['eccube.repository.product']->findAll();

        //ページネートの件数が正しく取得出来ているか検証
        $cq = $this->app['eccube.repository.product']->getQueryBuilderBySearchData($searchData);
        $custom_res = $cq->getQuery()->getResult();

        $this->assertEquals(count($actual),count($custom_res));
        //---------------------#670に対するテスト---------------------
    }
}
