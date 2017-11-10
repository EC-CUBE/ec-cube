<?php

namespace Eccube\Tests\Repository;

use Eccube\Common\Constant;
use Eccube\Entity\Category;

/**
 * ProductRepository#getQueryBuilderBySearchData test cases.
 *
 * @author Kentaro Ohkouchi
 */
class ProductRepositoryGetQueryBuilderBySearchDataTest extends AbstractProductRepositoryTestCase
{
    protected $Results;
    protected $searchData;
    protected $ProductListMax;
    protected $ProductListOrderBy;

    public function setUp() {
        parent::setUp();
        $this->ProductListMax = new \Eccube\Entity\Master\ProductListMax();
        $this->ProductListOrderBy = new \Eccube\Entity\Master\ProductListOrderBy();
    }

    public function scenario()
    {
        $this->Results = $this->app['eccube.repository.product']->getQueryBuilderBySearchData($this->searchData)
            ->getQuery()
            ->getResult();
    }

    public function testCategory()
    {
        $Categories = $this->app['eccube.repository.category']->findAll();
        $this->searchData = array(
            'category_id' => $Categories[0]
        );
        $this->scenario();

        $this->expected = 3;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testCategoryWithOut()
    {
        $Category = new Category();
        $Category
            ->setName('test')
            ->setRank(1)
            ->setLevel(1)
            ->setDelFlg(Constant::DISABLED)
            ->setCreateDate(new \DateTime())
            ->setUpdateDate(new \DateTime());
        $this->app['orm.em']->persist($Category);
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'category_id' => $Category
        );
        $this->scenario();

        $this->expected = 0;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testName()
    {
        $Products = $this->app['eccube.repository.product']->findAll();
        $Products[0]->setName('りんご');
        $Products[1]->setName('アイス');
        $Products[1]->setSearchWord('抹茶');
        $Products[2]->setName('お鍋');
        $Products[2]->setSearchWord('立方体');
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'name' => 'お鍋　立方体'
        );
        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testOrderByPrice()
    {
        $Products = $this->app['eccube.repository.product']->findAll();
        $Products[0]->setName('りんご');
        foreach ($Products[0]->getProductClasses() as $ProductClass) {
            $ProductClass->setPrice02(100);
        }
        $Products[1]->setName('アイス');
        foreach ($Products[1]->getProductClasses() as $ProductClass) {
            $ProductClass->setPrice02(1000);
        }
        $Products[2]->setName('お鍋');
        foreach ($Products[2]->getProductClasses() as $ProductClass) {
            $ProductClass->setPrice02(10000);
        }
        $this->app['orm.em']->flush();

        $ProductListOrderBy = $this->app['orm.em']->getRepository('\Eccube\Entity\Master\ProductListOrderBy')->find(1);
        $this->searchData = array(
            'orderby' => $ProductListOrderBy
        );

        $this->scenario();

        $this->expected = array('りんご', 'アイス', 'お鍋');
        $this->actual = array($this->Results[0]->getName(),
                              $this->Results[1]->getName(),
                              $this->Results[2]->getName());
        $this->verify();
    }

    /**
     * 価格が高い順のソート
     */
    public function testOrderByPriceHigher()
    {
        $Products = $this->app['eccube.repository.product']->findAll();
        $Products[0]->setName('りんご');
        foreach ($Products[0]->getProductClasses() as $ProductClass) {
            $ProductClass->setPrice02(100);
        }
        $Products[1]->setName('アイス');
        foreach ($Products[1]->getProductClasses() as $ProductClass) {
            $ProductClass->setPrice02(1000);
        }
        $Products[2]->setName('お鍋');
        foreach ($Products[2]->getProductClasses() as $ProductClass) {
            $ProductClass->setPrice02(10000);
        }
        $this->app['orm.em']->flush();

        $ProductListOrderBy = $this->app['orm.em']
            ->getRepository('\Eccube\Entity\Master\ProductListOrderBy')
            ->find($this->app['config']['product_order_price_higher']);
        $this->searchData = array(
            'orderby' => $ProductListOrderBy
        );

        $this->scenario();

        $this->expected = array('お鍋', 'アイス', 'りんご');
        $this->actual = array($this->Results[0]->getName(),
            $this->Results[1]->getName(),
            $this->Results[2]->getName());
        $this->verify();
    }

    public function testOrderByNewer()
    {
        $Products = $this->app['eccube.repository.product']->findAll();
        $Products[0]->setName('りんご');
        $Products[0]->setCreateDate(new \DateTime('-1 day'));
        $Products[1]->setName('アイス');
        $Products[1]->setCreateDate(new \DateTime('-2 day'));
        $Products[2]->setName('お鍋');
        $Products[2]->setCreateDate(new \DateTime('-3 day'));
        $this->app['orm.em']->flush();

        // 新着順
        $ProductListOrderBy = $this->app['orm.em']->getRepository('\Eccube\Entity\Master\ProductListOrderBy')->find(2);
        $this->searchData = array(
            'orderby' => $ProductListOrderBy
        );

        $this->scenario();

        $this->expected = array('りんご', 'アイス', 'お鍋');
        $this->actual = array($this->Results[0]->getName(),
                              $this->Results[1]->getName(),
                              $this->Results[2]->getName());

        $this->verify();
    }

    public function testOrderByNewerSameCreateDate()
    {
        $date = new \DateTime();
        $Products = $this->app['eccube.repository.product']->findBy(array(), array('id' => 'DESC'));
        $Products[0]->setName('りんご');
        $Products[0]->setCreateDate($date);
        $Products[1]->setName('アイス');
        $Products[1]->setCreateDate($date);
        $Products[2]->setName('お鍋');
        $Products[2]->setCreateDate($date);
        $this->app['orm.em']->flush();

        // 新着順
        $ProductListOrderBy = $this->app['orm.em']->getRepository('\Eccube\Entity\Master\ProductListOrderBy')->find(2);
        $this->searchData = array(
            'orderby' => $ProductListOrderBy
        );

        $this->scenario();

        $this->expected = array('りんご', 'アイス', 'お鍋');
        $this->actual = array($this->Results[0]->getName(),
            $this->Results[1]->getName(),
            $this->Results[2]->getName());

        $this->verify();
    }

    public function testProductImage()
    {
        $this->searchData = array();

        $this->scenario();

        $Products = $this->Results;

        foreach ($Products as $Product) {
            $this->expected = array(0, 1, 2);
            $this->actual = array();

            $ProductImages = $Product->getProductImage();
            foreach ($ProductImages as $ProductImage) {
                $this->actual[] = $ProductImage->getRank();
            }

            $this->verify();
        }
    }

    public function testPaginationEventByOrderPrice()
    {
        $this->ProductListMax->setId(15);
        $this->ProductListMax->setName('15件');
        $this->ProductListMax->setRank(0);

        $this->ProductListOrderBy->setId(1);
        $this->ProductListOrderBy->setName('価格順');
        $this->ProductListOrderBy->setRank(0);


        $this->searchData = array(
            'mode' => NULL,
            'category_id' => NULL,
            'name' => NULL,
            'pageno' => '1',
            'disp_number' => $this->ProductListMax,
            'orderby' => $this->ProductListOrderBy
        );
        $this->scenario();

        $pagination = $this->app['paginator']()->paginate(
            $this->Results,
            $this->searchData['pageno'],
            $this->searchData['disp_number']->getId()
        );

        $this->expected = count($pagination);
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testPaginationEventByOrderCreated()
    {
        $this->ProductListMax->setId(15);
        $this->ProductListMax->setName('15件');
        $this->ProductListMax->setRank(0);

        $this->ProductListOrderBy->setId(2);
        $this->ProductListOrderBy->setName('新着順');
        $this->ProductListOrderBy->setRank(0);


        $this->searchData = array(
            'mode' => NULL,
            'category_id' => NULL,
            'name' => NULL,
            'pageno' => '1',
            'disp_number' => $this->ProductListMax,
            'orderby' => $this->ProductListOrderBy
        );
        $this->scenario();

        $pagination = $this->app['paginator']()->paginate(
            $this->Results,
            $this->searchData['pageno'],
            $this->searchData['disp_number']->getId()
        );

        $this->expected = count($pagination);
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function test300ProductsList()
    {
        $tables = array(
            'dtb_product_image',
            'dtb_product_stock',
            'dtb_product_class',
            'dtb_product_category',
            'dtb_product'
        );
        $this->deleteAllRows($tables);
        $productList = array();
        for ($i = 1; $i <= 300; $i++) {
            $classNo = mt_rand(1, 3);
            $productName = 'BIG商品-' . $i;
            $this->createProduct($productName, $classNo);
            $productList[] = $productName;
        }
        $productList = array_reverse($productList);

        // 商品作成時間同じにする
        $QueryBuilder = $this->app['orm.em']->createQueryBuilder();
        $QueryBuilder->update('Eccube\Entity\Product','p');
        $QueryBuilder->set('p.create_date',':createDate');
        $QueryBuilder->setParameter(':createDate',new \DateTime());
        $QueryBuilder->getQuery()->execute();

        // 新着順
        $ProductListOrderBy = $this->app['orm.em']->getRepository('\Eccube\Entity\Master\ProductListOrderBy')->find(2);
        $this->searchData = array(
            'name' => 'BIG商品-',
            'orderby' => $ProductListOrderBy
        );

        $this->scenario();
        $this->expected = array();
        foreach($productList as $productName){
            $this->expected[] = $productName;
        }

        $this->actual = array();
        foreach($this->Results as $row){
            $this->actual[] = $row->getName();
        }
        $this->verify();
    }
}
