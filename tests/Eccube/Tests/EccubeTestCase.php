<?php

namespace Eccube\Tests;

use Eccube\Entity\Customer;
use Eccube\Tests\Fixture\Generator;
use Faker\Factory as Faker;
use GuzzleHttp\Client as HttpClient;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
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
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Applicaiton を生成しトランザクションを開始する.
     */
    public function setUp()
    {
        if (strpos(get_class($this), 'Eccube\Tests\Application') !== false
            || strpos(get_class($this), 'Eccube\Tests\Command') !== false
            || strpos(get_class($this), 'Eccube\Tests\DI') !== false
            || strpos(get_class($this), 'Eccube\Tests\Doctrine') !== false
            || strpos(get_class($this), 'Eccube\Tests\Entity') !== false
            || strpos(get_class($this), 'Eccube\Tests\Event') !== false
            || strpos(get_class($this), 'Eccube\Tests\EventListener') !== false
            || strpos(get_class($this), 'Eccube\Tests\Form') !== false
            || strpos(get_class($this), 'Eccube\Tests\Plugin') !== false
            || strpos(get_class($this), 'Eccube\Tests\Repository') !== false
            || (strpos(get_class($this), 'Eccube\Tests\Service') !== false
                && strpos(get_class($this), 'Eccube\Tests\ServiceProvider') === false)
            || strpos(get_class($this), 'Eccube\Tests\Transaction') !== false
        ) {
            $this->markTestIncomplete(get_class($this).' は未実装です');
        }
        $src = __DIR__.'/../../../src/Eccube/Resource/config/log.php';
        $dist = __DIR__.'/../../../app/config/eccube/log.php';

        $config = require $src;
        $config['log']['log_level'] = 'ERROR';
        $config['log']['action_level'] = 'ERROR';
        $config['log']['passthru_level'] = 'ERROR';

        $channel = $config['log']['channel'];
        foreach (array('monolog', 'front', 'admin') as $key) {
            $channel[$key]['log_level'] = 'ERROR';
            $channel[$key]['action_level'] = 'ERROR';
            $channel[$key]['passthru_level'] = 'ERROR';
        }
        $config['log']['channel'] = $channel;

        file_put_contents($dist, '<?php return '.var_export($config, true).';');

        parent::setUp();

        $this->client = self::createClient();
        $this->container = $this->client->getContainer();
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
     * Member オブジェクトを生成して返す.
     *
     * @param string $username . null の場合は, ランダムなユーザーIDが生成される.
     * @return \Eccube\Entity\Member
     */
    public function createMember($username = null)
    {
        return $this->container->get(Generator::class)->createMember($username);
    }

    /**
     * Customer オブジェクトを生成して返す.
     *
     * @param string $email メールアドレス. null の場合は, ランダムなメールアドレスが生成される.
     * @return \Eccube\Entity\Customer
     */
    public function createCustomer($email = null)
    {
        return $this->container->get(Generator::class)->createCustomer($email);
    }

    /**
     * CustomerAddress を生成して返す.
     *
     * @param Customer $Customer 対象の Customer インスタンス
     * @param boolean $is_nonmember 非会員の場合 true
     * @return \Eccube\Entity\CustomerAddress
     */
    public function createCustomerAddress(Customer $Customer, $is_nonmember = false)
    {
        return $this->container->get(Generator::class)->createCustomerAddress($Customer, $is_nonmember);
    }

    /**
     * 非会員の Customer オブジェクトを生成して返す.
     *
     * @param string $email メールアドレス. null の場合は, ランダムなメールアドレスが生成される.
     * @return \Eccube\Entity\Customer
     */
    public function createNonMember($email = null)
    {
        return $this->container->get(Generator::class)>createNonMember($email);
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
        return $this->container->get(Generator::class)->createProduct($product_name, $product_class_num);
    }

    /**
     * Order オブジェクトを生成して返す.
     *
     * @param \Eccube\Entity\Customer $Customer Customer インスタンス
     * @return \Eccube\Entity\Order
     */
    public function createOrder(Customer $Customer)
    {
        $Product = $this->createProduct();
        $ProductClasses = $Product->getProductClasses();

        // 後方互換のため最初の1つのみ渡す
        return $this->container->get(Generator::class)->createOrder($Customer, array($ProductClasses[0]));
    }

    /**
     * Payment オプジェクトを生成して返す.
     *
     * @param \Eccube\Entity\Delivery $Delivery デフォルトで設定する配送オブジェクト
     * @param string $method 支払い方法名称
     * @param integer $charge 手数料
     * @param integer $rule_min 下限金額
     * @param integer $rule_max 上限金額
     * @return \Eccube\Entity\Payment
     */
    public function createPayment(\Eccube\Entity\Delivery $Delivery, $method, $charge = 0, $rule_min = 0, $rule_max = 999999999)
    {
        return $this->container->get(Generator::class)->createPayment($Delivery, $method, $charge, $rule_min, $rule_max);
    }

    /**
     * Page オブジェクトを生成して返す
     *
     * @return \Eccube\Entity\Page
     */
    public function createPage()
    {
        return $this->container->get(Generator::class)->createPage();
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
        $conn = $this->app['db'];

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
        // TODO
        // \Eccube\Application::clearInstance();
    }

    /**
     * MailCatcher を初期化する.
     *
     * このメソッドは主に setUp() メソッドでコールされる.
     * MailCatcher が起動してない場合は, テストをスキップする.
     * MailCatcher については \Eccube\Tests\Service\MailServiceTest のコメントを参照してください
     *
     * @see \Eccube\Tests\Service\MailServiceTest
     * @link http://mailcatcher.me/
     */
    protected function initializeMailCatcher()
    {
        $this->checkMailCatcherStatus();
        $config = $this->app['config'];
        $config['mail']['transport'] = 'smtp';
        $config['mail']['host'] = '127.0.0.1';
        $config['mail']['port'] = 1025;
        $config['mail']['username'] = null;
        $config['mail']['password'] = null;
        $config['mail']['encryption'] = null;
        $config['mail']['auth_mode'] = null;
        $this->app->offsetUnset('config');
        $this->app['config'] = $config;
        $this->app['swiftmailer.use_spool'] = false;
        $this->app['swiftmailer.options'] = $this->app['config']['mail'];
    }

    /**
     * MailCatcher の起動状態をチェックする.
     *
     * MailCatcher が起動していない場合は, テストをスキップする.
     */
    protected function checkMailCatcherStatus()
    {
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
            $this->app->log($message);
        }
    }

    /**
     * MailCatcher のメッセージをすべて削除する.
     */
    protected function cleanUpMailCatcherMessages()
    {
        try {
            $httpClient = new HttpClient();
            $response = $httpClient->delete(self::MAILCATCHER_URL.'messages');
        } catch (\Exception $e) {
            // FIXME
            // $this->app->log('['.get_class().'] '.$e->getMessage());
        }
    }

    /**
     * MailCatcher のメッセージをすべて取得する.
     *
     * @return array MailCatcher のメッセージの配列
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
     * @return object MailCatcher のメッセージ
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
     * @return string デコードされた eml 形式のソース
     */
    protected function parseMailCatcherSource($Message)
    {
        return quoted_printable_decode($Message->source);
    }

    // TODO 暫定的に実装する
    protected function url($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $this->container->get('router')->generate($route, $parameters, $referenceType);
    }
}
