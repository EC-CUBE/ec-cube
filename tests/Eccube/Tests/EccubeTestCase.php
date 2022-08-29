<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\Customer;
use Eccube\Entity\ProductClass;
use Eccube\Tests\Fixture\Generator;
use Faker\Factory as Faker;
use GuzzleHttp\Client as HttpClient;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Abstract class that other unit tests can extend, provides generic methods for EC-CUBE tests.
 *
 * @author Kentaro Ohkouchi
 */
abstract class EccubeTestCase extends WebTestCase
{
    /** MailCatcher の URL. */
    const MAILCATCHER_URL = 'http://127.0.0.1:1080/';

    protected $actual;
    protected $expected;

    /**
     * @var KernelBrowser
     */
    protected $client;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * Client を生成しトランザクションを開始する.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
        $this->eccubeConfig = static::getContainer()->get(EccubeConfig::class);
    }

    /**
     * トランザクションをロールバックする.
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->cleanUpProperties();
    }

    /**
     * Faker を生成する.
     *
     * @param string $locale ロケールを指定する. デフォルト ja_JP
     *
     * @return \Faker\Generator
     *
     * @see https://github.com/fzaninotto/Faker
     */
    public function getFaker($locale = 'ja_JP')
    {
        return Faker::create($locale);
    }

    /**
     * Expected と Actual を比較する.
     *
     * @param string $message エラーメッセージ
     *
     * @see http://objectclub.jp/community/memorial/homepage3.nifty.com/masarl/article/junit/scenario-based-testcase.html#verify%20%E3%83%A1%E3%82%BD%E3%83%83%E3%83%89
     */
    public function verify($message = '')
    {
        $this->assertEquals($this->expected, $this->actual, $message);
    }

    /**
     * Member オブジェクトを生成して返す.
     *
     * @param string $username . null の場合は, ランダムなユーザーIDが生成される.
     *
     * @return \Eccube\Entity\Member
     */
    public function createMember($username = null)
    {
        return static::getContainer()->get(Generator::class)->createMember($username);
    }

    /**
     * Customer オブジェクトを生成して返す.
     *
     * @param string $email メールアドレス. null の場合は, ランダムなメールアドレスが生成される.
     *
     * @return \Eccube\Entity\Customer
     */
    public function createCustomer($email = null)
    {
        return static::getContainer()->get(Generator::class)->createCustomer($email);
    }

    /**
     * CustomerAddress を生成して返す.
     *
     * @param Customer $Customer 対象の Customer インスタンス
     * @param boolean $is_nonmember 非会員の場合 true
     *
     * @return \Eccube\Entity\CustomerAddress
     */
    public function createCustomerAddress(Customer $Customer, $is_nonmember = false)
    {
        return static::getContainer()->get(Generator::class)->createCustomerAddress($Customer, $is_nonmember);
    }

    /**
     * 非会員の Customer オブジェクトを生成して返す.
     *
     * @param string $email メールアドレス. null の場合は, ランダムなメールアドレスが生成される.
     *
     * @return \Eccube\Entity\Customer
     */
    public function createNonMember($email = null)
    {
        return static::getContainer()->get(Generator::class)->createNonMember($email);
    }

    /**
     * Product オブジェクトを生成して返す.
     *
     * @param string $product_name 商品名. null の場合はランダムな文字列が生成される.
     * @param integer $product_class_num 商品規格の生成数
     *
     * @return \Eccube\Entity\Product
     */
    public function createProduct($product_name = null, $product_class_num = 3)
    {
        return static::getContainer()->get(Generator::class)->createProduct($product_name, $product_class_num);
    }

    /**
     * Order オブジェクトを生成して返す.
     *
     * @param \Eccube\Entity\Customer $Customer Customer インスタンス
     *
     * @return \Eccube\Entity\Order
     */
    public function createOrder(Customer $Customer)
    {
        $Product = $this->createProduct();
        $ProductClasses = $Product->getProductClasses();

        // 後方互換のため最初の1つのみ渡す
        return static::getContainer()->get(Generator::class)->createOrder($Customer, [$ProductClasses[0]]);
    }

    /**
     * Order オブジェクトを生成して返す.
     *
     * @param \Eccube\Entity\Customer $Customer Customer インスタンス
     * @param ProductClass[] $ProductClasses
     *
     * @return \Eccube\Entity\Order
     */
    public function createOrderWithProductClasses(Customer $Customer, array $ProductClasses)
    {
        return static::getContainer()->get(Generator::class)->createOrder($Customer, $ProductClasses);
    }

    /**
     * Payment オプジェクトを生成して返す.
     *
     * @param \Eccube\Entity\Delivery $Delivery デフォルトで設定する配送オブジェクト
     * @param string $method 支払い方法名称
     * @param integer $charge 手数料
     * @param integer $rule_min 下限金額
     * @param integer $rule_max 上限金額
     *
     * @return \Eccube\Entity\Payment
     */
    public function createPayment(\Eccube\Entity\Delivery $Delivery, $method, $charge = 0, $rule_min = 0, $rule_max = 999999999)
    {
        return static::getContainer()->get(Generator::class)->createPayment($Delivery, $method, $charge, $rule_min, $rule_max);
    }

    /**
     * Page オブジェクトを生成して返す
     *
     * @return \Eccube\Entity\Page
     */
    public function createPage()
    {
        return static::getContainer()->get(Generator::class)->createPage();
    }

    /**
     * LoginHistory オブジェクトを生成して返す
     *
     * @return \Eccube\Entity\LoginHistory
     */
    public function createLoginHistory($user_name, $client_ip = null, $status = 0, $Member = null)
    {
        return static::getContainer()->get(Generator::class)->createLoginHistory($user_name, $client_ip, $status, $Member);
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
        /** @var Connection $conn */
        $conn = $this->entityManager->getConnection();

        // MySQLの場合は参照制約を無効にする.
        if ('mysql' === $conn->getDatabasePlatform()->getName()) {
            $conn->query('SET FOREIGN_KEY_CHECKS = 0');
        }

        foreach ($tables as $table) {
            $sql = 'DELETE FROM '.$table;
            $stmt = $conn->prepare($sql);
            $stmt->execute();
        }

        if ('mysql' === $conn->getDatabasePlatform()->getName()) {
            $conn->query('SET FOREIGN_KEY_CHECKS = 1');
        }
    }

    /**
     * PHPUnit インスタンスのプロパティを初期化する.
     *
     * このメソッドは、PHPUnit のメモリリーク解消のため、 tearDown() メソッドでコールされる.
     *
     * @see http://stackoverflow.com/questions/13537545/clear-memory-being-used-by-php
     */
    protected function cleanUpProperties()
    {
        $refl = new \ReflectionObject($this);
        foreach ($refl->getProperties() as $prop) {
            if (!$prop->isStatic() && 0 !== strpos($prop->getDeclaringClass()->getName(), 'PHPUnit')) {
                $prop->setAccessible(true);
                $prop->setValue($this, null);
            }
        }
    }

    /**
     * Generates a URL from the given parameters.
     *
     * @param string $route         The name of the route
     * @param array  $parameters    An array of parameters
     * @param int    $referenceType The type of reference (one of the constants in UrlGeneratorInterface)
     *
     * @return string The generated URL
     *
     * @see UrlGeneratorInterface
     * @see \Symfony\Bundle\FrameworkBundle\Controller\ControllerTrait::generateUrl
     */
    protected function generateUrl($route, $parameters = [], $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return static::getContainer()->get('router')->generate($route, $parameters, $referenceType);
    }

    /**
     * Returns a CSRF token for the given ID.
     *
     * If previously no token existed for the given ID.
     * ATTENTION: Call this function before login.
     *
     * @param string $csrfTokenId The token ID (e.g. `authenticate`, `<FormTypeBlockPrefix>`)
     *
     * @return CsrfToken The CSRF token
     *
     * @see \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface
     * @see https://stackoverflow.com/a/38661340/4956633
     */
    protected function getCsrfToken($csrfTokenId)
    {
        return static::getContainer()->get('security.csrf.token_manager')->getToken($csrfTokenId);
    }
}
