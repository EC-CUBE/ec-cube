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
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\SwiftmailerBundle\DataCollector\MessageDataCollector;
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
     * @var Client
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
    public function setUp()
    {
        parent::setUp();
        self::bootKernel();
        $this->client = static::createClient();
        $this->entityManager = self::$container->get('doctrine')->getManager();
        $this->eccubeConfig = self::$container->get(EccubeConfig::class);
    }

    /**
     * トランザクションをロールバックする.
     */
    public function tearDown()
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
        return self::$container->get(Generator::class)->createMember($username);
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
        return self::$container->get(Generator::class)->createCustomer($email);
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
        return self::$container->get(Generator::class)->createCustomerAddress($Customer, $is_nonmember);
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
        return self::$container->get(Generator::class)->createNonMember($email);
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
        return self::$container->get(Generator::class)->createProduct($product_name, $product_class_num);
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
        return self::$container->get(Generator::class)->createOrder($Customer, [$ProductClasses[0]]);
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
        return self::$container->get(Generator::class)->createOrder($Customer, $ProductClasses);
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
        return self::$container->get(Generator::class)->createPayment($Delivery, $method, $charge, $rule_min, $rule_max);
    }

    /**
     * Page オブジェクトを生成して返す
     *
     * @return \Eccube\Entity\Page
     */
    public function createPage()
    {
        return self::$container->get(Generator::class)->createPage();
    }

    /**
     * LoginHistory オブジェクトを生成して返す
     *
     * @return \Eccube\Entity\LoginHistory
     */
    public function createLoginHistory($user_name, $client_ip = null, $status = 0, $Member = null)
    {
        return self::$container->get(Generator::class)->createLoginHistory($user_name, $client_ip, $status, $Member);
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
     * MailCatcher を初期化する.
     *
     * このメソッドは主に setUp() メソッドでコールされる.
     * MailCatcher が起動してない場合は, テストをスキップする.
     * MailCatcher については \Eccube\Tests\Service\MailServiceTest のコメントを参照してください
     *
     * @see \Eccube\Tests\Service\MailServiceTest
     * @see http://mailcatcher.me/
     * @deprecated
     */
    protected function initializeMailCatcher()
    {
        $this->checkMailCatcherStatus();
    }

    /**
     * MailCatcher の起動状態をチェックする.
     *
     * MailCatcher が起動していない場合は, テストをスキップする.
     *
     * @deprecated
     */
    protected function checkMailCatcherStatus()
    {
        trigger_error('MailCatcher is deprecated. Please implementation to the EccubeTestCase::getMailCollector().', E_USER_ERROR);
        try {
            $httpClient = new HttpClient();
            $response = $httpClient->get(self::MAILCATCHER_URL.'messages');
            if ($response->getStatusCode() !== 200) {
                throw new HttpException($response->getStatusCode());
            }
        } catch (HttpException $e) {
            $this->markTestSkipped($e->getMailCatcherMessage().'['.$e->getStatusCode().']');
        } catch (\Exception $e) {
            $message = 'MailCatcher is not available';
            $this->markTestSkipped($message);
            log_error($message);
        }
    }

    /**
     * MailCatcher のメッセージをすべて削除する.
     *
     * @deprecated
     */
    protected function cleanUpMailCatcherMessages()
    {
        try {
            $httpClient = new HttpClient();
            $response = $httpClient->delete(self::MAILCATCHER_URL.'messages');
        } catch (\Exception $e) {
            log_error('['.get_class().'] '.$e->getMessage());
        }
    }

    /**
     * MailCatcher のメッセージをすべて取得する.
     *
     * @return array MailCatcher のメッセージの配列
     *
     * @deprecated
     */
    protected function getMailCatcherMessages()
    {
        $httpClient = new HttpClient();
        $response = $httpClient->get(self::MAILCATCHER_URL.'messages');

        return json_decode($response->getBody(true));
    }

    /**
     * MailCatcher のメッセージを ID を指定して取得する.
     *
     * @param integer $id メッセージの ID
     *
     * @return object MailCatcher のメッセージ
     *
     * @deprecated
     */
    protected function getMailCatcherMessage($id)
    {
        $httpClient = new HttpClient();
        $response = $httpClient->get(self::MAILCATCHER_URL.'messages/'.$id.'.json');

        return json_decode($response->getBody(true));
    }

    /**
     * MailCatcher のメッセージソースをデコードする.
     *
     * @param object $Message MailCatcher のメッセージ
     *
     * @return string デコードされた eml 形式のソース
     *
     * @deprecated
     */
    protected function parseMailCatcherSource($Message)
    {
        return quoted_printable_decode($Message->source);
    }

    /**
     * Get the MailCollector
     *
     * @param boolean $sendRequest True to send requests internally.
     *
     * @return MessageDataCollector
     */
    protected function getMailCollector($sendRequest = true)
    {
        if ($sendRequest) {
            $this->client->enableProfiler();
            $this->client->request('POST', '/');
        }

        return $this->client->getProfile()->getCollector('swiftmailer');
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
        return self::$container->get('router')->generate($route, $parameters, $referenceType);
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
        return self::$container->get('security.csrf.token_manager')->getToken($csrfTokenId);
    }
}
