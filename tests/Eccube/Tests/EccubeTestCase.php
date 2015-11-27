<?php

namespace Eccube\Tests;

use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\Migration;
use Doctrine\DBAL\Migrations\MigrationException;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\Customer;
use Eccube\Entity\CustomerAddress;
use Eccube\Entity\Order;
use Eccube\Entity\OrderDetail;
use Eccube\Entity\Product;
use Eccube\Entity\ProductCategory;
use Eccube\Entity\ProductClass;
use Eccube\Entity\ProductImage;
use Eccube\Entity\ProductStock;
use Eccube\Entity\Shipping;
use Eccube\Entity\ShipmentItem;
use Eccube\Entity\Master\CustomerStatus;
use Eccube\Tests\Mock\CsrfTokenMock;
use Silex\WebTestCase;
use Faker\Factory as Faker;

/**
 * Abstract class that other unit tests can extend, provides generic methods for EC-CUBE tests.
 *
 * @author Kentaro Ohkouchi
 */
abstract class EccubeTestCase extends WebTestCase
{

    protected $actual;
    protected $expected;

    /**
     * Applicaiton を生成しトランザクションを開始する.
     */
    public function setUp()
    {
        parent::setUp();
        // in the case of sqlite in-memory database only.
        if (array_key_exists('memory', $this->app['config']['database'])
            && $this->app['config']['database']['memory']) {
            $this->initializeDatabase();
        }
        if (isset($this->app['orm.em'])) {
            $this->app['orm.em']->getConnection()->beginTransaction();
        }
    }

    /**
     * トランザクションをロールバックする.
     */
    public function tearDown()
    {
        parent::tearDown();
        $this->app['orm.em']->getConnection()->rollback();
        $this->app['orm.em']->getConnection()->close();
        $this->cleanUpProperties();
    }

    /**
     * データベースを初期化する.
     *
     * データベースを初期化し、マイグレーションを行なう.
     * 全てのデータが初期化されるため注意すること.
     *
     * @link http://jamesmcfadden.co.uk/database-unit-testing-with-doctrine-2-and-phpunit/
     */
    public function initializeDatabase()
    {
        // Get an instance of your entity manager
        $entityManager = $this->app['orm.em'];

        // Retrieve PDO instance
        $pdo = $entityManager->getConnection()->getWrappedConnection();

        // Clear Doctrine to be safe
        $entityManager->clear();

        // Schema Tool to process our entities
        $tool = new \Doctrine\ORM\Tools\SchemaTool($entityManager);
        $classes = $entityManager->getMetaDataFactory()->getAllMetaData();

        // Drop all classes and re-build them for each test case
        $tool->dropSchema($classes);
        $tool->createSchema($classes);
        $config = new Configuration($this->app['db']);
        $config->setMigrationsNamespace('DoctrineMigrations');

        $migrationDir = __DIR__ . '/../../../src/Eccube/Resource/doctrine/migration';
        $config->setMigrationsDirectory($migrationDir);
        $config->registerMigrationsFromDirectory($migrationDir);

        $migration = new Migration($config);
        $migration->migrate(null, false);

        // 通常は eccube_install.sh で追加されるデータを追加する
        $sql = "INSERT INTO dtb_member (member_id, login_id, password, salt, work, del_flg, authority, creator_id, rank, update_date, create_date,name,department) VALUES (2, 'admin', 'test', 'test', 1, 0, 0, 1, 1, current_timestamp, current_timestamp,'管理者','EC-CUBE SHOP')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $sql = "INSERT INTO dtb_base_info (id, shop_name, email01, email02, email03, email04, update_date, option_product_tax_rule) VALUES (1, 'SHOP_NAME', 'admin@example.com', 'admin@example.com', 'admin@example.com', 'admin@example.com', current_timestamp, 0)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    }

    /**
     * Faker を生成する.
     *
     * @param string $locale ロケールを指定する. デフォルト ja_JP
     * @return Faker\Generator
     * @link https://github.com/fzaninotto/Faker
     */
    public function getFaker($locale = 'ja_JP')
    {
        return Faker::create($locale);
    }

    /**
     * Expected と Actual を比較する.
     *
     * @param string $message エラーメッセージ
     * @link http://objectclub.jp/community/memorial/homepage3.nifty.com/masarl/article/junit/scenario-based-testcase.html#verify%20%E3%83%A1%E3%82%BD%E3%83%83%E3%83%89
     */
    public function verify($message = '')
    {
        $this->assertEquals($this->expected, $this->actual, $message);
    }

    /**
     * Customer オブジェクトを生成して返す.
     *
     * @param string $email メールアドレス. null の場合は, ランダムなメールアドレスが生成される.
     * @return \Eccube\Entity\Customer
     */
    public function createCustomer($email = null)
    {
        $faker = $this->getFaker();
        $Customer = new Customer();
        if (is_null($email)) {
            $email = $faker->email;
        }
        $Status = $this->app['orm.em']->getRepository('Eccube\Entity\Master\CustomerStatus')->find(CustomerStatus::ACTIVE);
        $Pref = $this->app['eccube.repository.master.pref']->find(1);
        $Customer
            ->setName01($faker->lastName)
            ->setName02($faker->firstName)
            ->setEmail($email)
            ->setPref($Pref)
            ->setPassword('password')
            ->setSecretKey($this->app['eccube.repository.customer']->getUniqueSecretKey($this->app))
            ->setStatus($Status)
            ->setDelFlg(0);
        $Customer->setPassword($this->app['eccube.repository.customer']->encryptPassword($this->app, $Customer));
        $this->app['orm.em']->persist($Customer);
        $this->app['orm.em']->flush();

        $CustomerAddress = new CustomerAddress();
        $CustomerAddress
            ->setCustomer($Customer)
            ->setDelFlg(0);
        $CustomerAddress->copyProperties($Customer);
        $this->app['orm.em']->persist($CustomerAddress);
        $this->app['orm.em']->flush();

        return $Customer;
    }

    /**
     * 非会員の Customer オブジェクトを生成して返す.
     *
     * @param string $email メールアドレス. null の場合は, ランダムなメールアドレスが生成される.
     * @return \Eccube\Entity\Customer
     */
    public function createNonMember($email = null)
    {
        $sessionKey = 'eccube.front.shopping.nonmember';
        $sessionCustomerAddressKey = 'eccube.front.shopping.nonmember.customeraddress';
        $faker = $this->getFaker();
        $Customer = new Customer();
        if (is_null($email)) {
            $email = $faker->email;
        }
        $Pref = $this->app['eccube.repository.master.pref']->find(1);
        $Customer
            ->setName01($faker->lastName)
            ->setName02($faker->firstName)
            ->setEmail($email)
            ->setPref($Pref)
            ->setDelFlg(0);

        $CustomerAddress = new CustomerAddress();
        $CustomerAddress
            ->setCustomer($Customer)
            ->setDelFlg(0);
        $CustomerAddress->copyProperties($Customer);
        $Customer->addCustomerAddress($CustomerAddress);

        $nonMember = array();
        $nonMember['customer'] = $Customer;
        $nonMember['pref'] = $Customer->getPref()->getId();
        $this->app['session']->set($sessionKey, $nonMember);

        $customerAddresses = array();
        $customerAddresses[] = $CustomerAddress;
        $this->app['session']->set($sessionCustomerAddressKey, serialize($customerAddresses));
        return $Customer;
    }

    /**
     * Product オブジェクトを生成して返す.
     *
     * @param string $product_name 商品名. null の場合はランダムな文字列が生成される.
     * @param integer $product_class_num 商品規格の生成数
     * @return \Eccube\Entity\Product
     */
    public function createProduct($product_name = null, $product_class_num = 3)
    {
        $faker = $this->getFaker();
        $Member = $this->app['eccube.repository.member']->find(2);
        $Disp = $this->app['eccube.repository.master.disp']->find(\Eccube\Entity\Master\Disp::DISPLAY_SHOW);
        $ProductType = $this->app['eccube.repository.master.product_type']->find(1);
        $Product = new Product();
        if (is_null($product_name)) {
            $product_name = $faker->word;
        }

        $Product
            ->setName($product_name)
            ->setCreator($Member)
            ->setStatus($Disp)
            ->setDelFlg(Constant::DISABLED)
            ->setDescriptionList($faker->paragraph())
            ->setDescriptionDetail($faker->text());

        $this->app['orm.em']->persist($Product);
        $this->app['orm.em']->flush();

        for ($i = 0; $i < 3; $i++) {
            $ProductImage = new ProductImage();
            $ProductImage
                ->setCreator($Member)
                ->setFileName($faker->word.'.jpg')
                ->setRank($i)
                ->setProduct($Product);
            $this->app['orm.em']->persist($ProductImage);
            $Product->addProductImage($ProductImage);
        }

        for ($i = 0; $i < $product_class_num; $i++) {
            $ProductStock = new ProductStock();
            $ProductStock
                ->setCreator($Member)
                ->setStock($faker->randomNumber());
            $this->app['orm.em']->persist($ProductStock);
            $ProductClass = new ProductClass();
            $ProductClass
                ->setCreator($Member)
                ->setProductStock($ProductStock)
                ->setProduct($Product)
                ->setProductType($ProductType)
                ->setStockUnlimited(false)
                ->setPrice02($faker->randomNumber(5))
                ->setDelFlg(Constant::DISABLED);
            $this->app['orm.em']->persist($ProductClass);
            $Product->addProductClass($ProductClass);
        }

        $Categories = $this->app['eccube.repository.category']->findAll();
        $i = 0;
        foreach ($Categories as $Category) {
            $ProductCategory = new ProductCategory();
            $ProductCategory
                ->setCategory($Category)
                ->setProduct($Product)
                ->setCategoryId($Category->getId())
                ->setProductId($Product->getId())
                ->setRank($i);
            $this->app['orm.em']->persist($ProductCategory);
            $Product->addProductCategory($ProductCategory);
            $i++;
        }

        $this->app['orm.em']->flush();
        return $Product;
    }

    /**
     * Order オブジェクトを生成して返す.
     *
     * @param \Eccube\Entity\Customer $Customer Customer インスタンス
     * @return \Eccube\Entity\Order
     */
    public function createOrder(Customer $Customer)
    {
        $faker = $this->getFaker();
        $quantity = $faker->randomNumber(2);
        $Pref = $this->app['eccube.repository.master.pref']->find(1);
        $Order = new Order($this->app['eccube.repository.order_status']->find($this->app['config']['order_processing']));
        $Order->setCustomer($Customer);
        $Order->copyProperties($Customer);
        $Order->setPref($Pref);
        $this->app['orm.em']->persist($Order);
        $this->app['orm.em']->flush();

        $Delivery = $this->app['eccube.repository.delivery']->find(1);
        $Shipping = new Shipping();
        $Shipping->copyProperties($Customer);
        $Shipping
            ->setPref($Pref)
            ->setDelivery($Delivery);
        $Order->addShipping($Shipping);
        $Shipping->setOrder($Order);
        $this->app['orm.em']->persist($Shipping);

        $Product = $this->createProduct();
        $ProductClasses = $Product->getProductClasses();
        $ProductClass = $ProductClasses[0];

        $OrderDetail = new OrderDetail();
        $TaxRule = $this->app['eccube.repository.tax_rule']->getByRule(); // デフォルト課税規則
        $OrderDetail->setProduct($Product)
            ->setProductClass($ProductClass)
            ->setProductName($Product->getName())
            ->setProductCode($ProductClass->getCode())
            ->setPrice($ProductClass->getPrice02())
            ->setQuantity($quantity)
            ->setTaxRule($TaxRule->getCalcRule()->getId())
            ->setTaxRate($TaxRule->getTaxRate());
        $this->app['orm.em']->persist($OrderDetail);
        $OrderDetail->setOrder($Order);
        $Order->addOrderDetail($OrderDetail);

        $ShipmentItem = new ShipmentItem();
        $ShipmentItem->setShipping($Shipping)
            ->setOrder($Order)
            ->setProductClass($ProductClass)
            ->setProduct($Product)
            ->setProductName($Product->getName())
            ->setProductCode($ProductClass->getCode())
            ->setPrice($ProductClass->getPrice02())
            ->setQuantity($quantity);
        $Shipping->addShipmentItem($ShipmentItem);
        $this->app['orm.em']->persist($ShipmentItem);

        $subTotal = $OrderDetail->getPriceIncTax() * $OrderDetail->getQuantity();
        // TODO 送料, 手数料の加算
        $Order->setSubTotal($subTotal);
        $Order->setTotal($subTotal);
        $Order->setPaymentTotal($subTotal);

        $this->app['orm.em']->flush();
        return $Order;
    }

    /**
     * テーブルのデータを全て削除する.
     *
     * このメソッドは、参照制約の関係で、 Doctrine ORM ではデータ削除できない場合に使用する.
     * 通常は、 EntityManager::remove() を使用して削除すること.
     *
     * @param array $tables 削除対象のテーブル名の配列
     */
    public function deleteAllRows(array $tables)
    {
        $pdo = $this->app['orm.em']->getConnection()->getWrappedConnection();
        foreach ($tables as $table) {
            $sql = 'DELETE FROM '.$table;
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createApplication()
    {
        $app = new Application();
        $app['debug'] = true;
        $app->initialize();
        $app->initPluginEventDispatcher();
        $app['session.test'] = true;
        $app['exception_handler']->disable();

        $app['form.csrf_provider'] = $app->share(function () {
            return new CsrfTokenMock();
        });

        $app->boot();

        return $app;
    }

    /**
     * PHPUnit_* インスタンスのプロパティを初期化する.
     *
     * このメソッドは、PHPUnit のメモリリーク解消のため、 tearDown() メソッドでコールされる.
     *
     * @link http://stackoverflow.com/questions/13537545/clear-memory-being-used-by-php
     */
    protected function cleanUpProperties()
    {
        $refl = new \ReflectionObject($this);
        foreach ($refl->getProperties() as $prop) {
            if (!$prop->isStatic() && 0 !== strpos($prop->getDeclaringClass()->getName(), 'PHPUnit_')) {
                $prop->setAccessible(true);
                $prop->setValue($this, null);
            }
        }
    }
}
