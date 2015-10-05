<?php

namespace Eccube\Tests\Util;

use Eccube\Application;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Entity\Member;
use Eccube\Common\Constant;
use Eccube\Util\EntityUtil;

/**
 * EntityUtil test cases.
 *
 * @author Kentaro Ohkouchi
 */
class EntityUtilTest extends \PHPUnit_Framework_TestCase
{
    private $actual;
    private $expected;

    private $Product;
    private $ProductClass;
    private $memberId;
    private $productClassId;

    protected static $app = null;

    public static function setUpBeforeClass()
    {
        // TODO Abstract にしたい.
        $app = new Application();
        $app['debug'] = true;
        $app->initialize();
        $app->initPluginEventDispatcher();
        $app['session.test'] = true;
        $app['exception_handler']->disable();

        $app->boot();
        $softDeleteFilter = $app['orm.em']->getFilters()->getFilter('soft_delete');
        $softDeleteFilter->setExcludes(array(
            'Eccube\Entity\ProductClass'
        ));
        // soft_delete filter を有効にする
        $config = $app['orm.em']->getConfiguration();
        $config->addFilter("soft_delete", '\Eccube\Doctrine\Filter\SoftDeleteFilter');
        $app['orm.em']->getFilters()->enable('soft_delete');

        self::$app = $app;
    }

    public static function tearDownAfterClass()
    {
        self::$app = null;
    }

    public function setUp()
    {

        self::$app['orm.em']->getConnection()->beginTransaction();

        // eccube_install.sh で追加される Member
        $Member = self::$app['eccube.repository.member']->find(2);

        $Product = new Product();
        $ProductClass = new ProductClass();
        $Disp = self::$app['eccube.repository.master.disp']->find(\Eccube\Entity\Master\Disp::DISPLAY_HIDE);
        $ProductType = self::$app['eccube.repository.master.product_type']->find(self::$app['config']['product_type_normal']);
        $Product
            ->setName('test')
            ->setCreator($Member)
            ->setDelFlg(Constant::DISABLED)
            ->addProductClass($ProductClass)
            ->setStatus($Disp);
        $ProductClass
            ->setPrice02(1000)
            ->setCreator($Member)
            ->setDelFlg(Constant::DISABLED)
            ->setStockUnlimited(true)
            ->setProductType($ProductType)
            ->setProduct($Product);
        $ProductStock = new \Eccube\Entity\ProductStock();
        $ProductStock->setCreator($Member);
        $ProductClass->setProductStock($ProductStock);
        $ProductStock->setProductClass($ProductClass);

        self::$app['orm.em']->persist($Product);
        self::$app['orm.em']->persist($ProductClass);
        self::$app['orm.em']->persist($ProductStock);
        self::$app['orm.em']->flush();

        $this->Product = $Product;
        $this->ProductClass = $ProductClass;
    }

    public function tearDown()
    {
        self::$app['orm.em']->getConnection()->rollback();
    }

    /**
     * EntityUtil::isEmpty() のテストケース.
     *
     * soft_delete の対象となったオブジェクトを LAZY loading しようとすると
     * Entity was not found エラーとなるため、 EntityUtil::isEmpty() で取得するテスト
     *
     * @see https://github.com/EC-CUBE/ec-cube/pull/602#issuecomment-125431246
     */
    public function testIsEmpty()
    {
        // migration で追加されるサンプル商品
        $Product = self::$app['eccube.repository.product']->find(1);
        // migration で追加されるダミーの Member. del_flg = 1 の状態で INSERT されている
        $Member = $Product->getCreator();
        /*
         * member.del_flg = 1 になっているので、soft_delete filter が適用され
         * LAZY loading しようとすると Entity was not found のエラーとなる
         */
        $this->assertTrue(EntityUtil::isEmpty($Member));
    }

    public function testIsEmptyWithFalse()
    {
        // setUp() で追加したサンプル商品
        $Product = self::$app['eccube.repository.product']->find($this->Product->getId());
        // eccube_install.sh で追加される Member
        $Member = $Product->getCreator();
        /*
         * member.del_flg = 0 になっているので、soft_delete filter が適用されず
         * LAZY loading で取得できる
         */
        $this->assertFalse(EntityUtil::isEmpty($Member));
    }

    public function testIsNotEmpty()
    {
        // migration で追加されるサンプル商品
        $Product = self::$app['eccube.repository.product']->find(1);
        // migration で追加されるダミーの Member
        $Member = $Product->getCreator();
        /*
         * member.del_flg = 1 になっているので、soft_delete filter が適用され
         * LAZY loading しようとすると Entity was not found のエラーとなる
         */
        $this->assertFalse(EntityUtil::isNotEmpty($Member));
    }
}
