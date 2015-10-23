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
 * ProductRepository#getQueryBuilderBySearchData test cases.
 *
 * @author Kentaro Ohkouchi
 */
class ProductRepositoryGetQueryBuilderBySearchDataTest extends AbstractProductRepositoryTestCase
{
    protected $Results;
    protected $searchData;

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
}
