<?php

namespace Eccube\Tests\Repository;

use Eccube\Tests\EccubeTestCase;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\Category;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Entity\ProductImage;
use Eccube\Entity\ProductStock;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ProductRepository#getQueryBuilderBySearchDataAdmin test cases.
 *
 * @author Kentaro Ohkouchi
 */
class ProductRepositoryGetQueryBuilderBySearchDataAdminTest extends AbstractProductRepositoryTestCase
{
    protected $Results;
    protected $searchData;

    public function scenario()
    {
        $this->Results = $this->app['eccube.repository.product']->getQueryBuilderBySearchDataForAdmin($this->searchData)
            ->getQuery()
            ->getResult();
    }

    public function testId()
    {
        $Product = $this->app['eccube.repository.product']->findOneBy(array('name' => '商品-2'));
        $id = $Product->getId();

        $this->searchData = array(
            'id' => $id
        );
        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testCode()
    {
        $Products = $this->app['eccube.repository.product']->findAll();
        $Products[0]->setName('りんご');
        foreach ($Products[0]->getProductClasses() as $ProductClass) {
            $ProductClass->setCode('dessert-1');
        }
        $Products[1]->setName('アイス');
        foreach ($Products[1]->getProductClasses() as $ProductClass) {
            $ProductClass->setCode('dessert-2');
        }
        $Products[2]->setName('お鍋');
        foreach ($Products[2]->getProductClasses() as $ProductClass) {
            $ProductClass->setCode('onabe-1');
        }
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'id' => 'dessert-'
        );
        $this->scenario();

        $this->expected = 2;
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
            'id' => 'お'
        );
        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testStatus()
    {
        $Product = $this->app['eccube.repository.product']->findOneBy(array('name' => '商品-1'));
        $Disp = $this->app['eccube.repository.master.disp']->find(\Eccube\Entity\Master\Disp::DISPLAY_HIDE);
        $Product->setStatus($Disp);
        $this->app['orm.em']->flush();

        $Status = new ArrayCollection(array($Disp));
        $this->searchData = array(
            'status' => $Status
        );
        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testLinkStatus()
    {
        $Product = $this->app['eccube.repository.product']->findOneBy(array('name' => '商品-1'));
        $Disp = $this->app['eccube.repository.master.disp']->find(\Eccube\Entity\Master\Disp::DISPLAY_HIDE);
        $Product->setStatus($Disp);
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'link_status' => \Eccube\Entity\Master\Disp::DISPLAY_HIDE
        );
        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testStockStatus()
    {
        $faker = $this->getFaker();
        // 全商品の在庫を 1 以上にしておく
        $Products = $this->app['eccube.repository.product']->findAll();
        foreach ($Products as $Product) {
            foreach ($Product->getProductClasses() as $ProductClass) {
                $ProductClass
                    ->setStockUnlimited(false)
                    ->setStock($faker->numberBetween(1, 999));
            }
        }
        $this->app['orm.em']->flush();

        // 1商品だけ 0 に設定する
        $Product = $this->app['eccube.repository.product']->findOneBy(array('name' => '商品-1'));
        foreach ($Product->getProductClasses() as $ProductClass) {
            $ProductClass
                ->setStockUnlimited(false)
                ->setStock(0);
        }
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'stock_status' => 0
        );
        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testCreateDateStart()
    {
        $this->searchData = array(
            'create_date_start' => new \DateTime('- 1 days')
        );

        $this->scenario();

        $this->expected = 3;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testCreateDateEnd()
    {
        $this->searchData = array(
            'create_date_end' => new \DateTime('+ 1 days')
        );

        $this->scenario();

        $this->expected = 3;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testUpdateDateStart()
    {
        $this->searchData = array(
            'update_date_start' => new \DateTime('- 1 days')
        );

        $this->scenario();

        $this->expected = 3;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testUpdateDateEnd()
    {
        $this->searchData = array(
            'update_date_end' => new \DateTime('+ 1 days')
        );

        $this->scenario();

        $this->expected = 3;
        $this->actual = count($this->Results);
        $this->verify();
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
}
