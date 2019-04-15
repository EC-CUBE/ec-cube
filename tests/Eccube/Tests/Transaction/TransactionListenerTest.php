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

namespace Eccube\Tests\Transaction;

use Eccube\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * TransactinoListener のテストケース.
 *
 * このテストケースは、トランザクションの COMMIT/ROLLBACK をテストするため、
 * EccubeTestCase を継承しない.
 *
 * TODO BaseInfo::companyName が更新されてしまうため、復元できるようにしたい.
 * tearDown で復元しようとするとテストケースでトランザクションブロックが形成される
 * ため、テストに失敗してしまう.
 *
 * @author Kentaro Ohkouchi
 */
class TransactionListenerTest extends WebTestCase
{
    protected function isSqlite()
    {
        return $this->app['db']->getDatabasePlatform()->getName() === 'sqlite';
    }

    public function setUp()
    {
        $this->markTestIncomplete(get_class($this).' は未実装です');
        parent::setUp();

        if ($this->isSqlite()) {
            // Connection が別物になってしまうため
            $this->markTestSkipped('Can not support for sqlite3');
        }

        $c = $this->app['controllers_factory'];
        $c->match('/tran1', '\Eccube\Tests\Transaction\TransactionControllerMock::tran1')->bind('tran1');
        $c->match('/tran2', '\Eccube\Tests\Transaction\TransactionControllerMock::tran2')->bind('tran2');
        $c->match('/tran3', '\Eccube\Tests\Transaction\TransactionControllerMock::tran3')->bind('tran3');
        $c->match('/tran4', '\Eccube\Tests\Transaction\TransactionControllerMock::tran4')->bind('tran4');
        $c->match('/tran5', '\Eccube\Tests\Transaction\TransactionControllerMock::tran5')->bind('tran5');
        $c->match('/tran6', '\Eccube\Tests\Transaction\TransactionControllerMock::tran6')->bind('tran6');
        $c->match('/tran7', '\Eccube\Tests\Transaction\TransactionControllerMock::tran7')->bind('tran7');
        $c->match('/tran8', '\Eccube\Tests\Transaction\TransactionControllerMock::tran8')->bind('tran8');
        $c->match('/tran9', '\Eccube\Tests\Transaction\TransactionControllerMock::tran9')->bind('tran9');
        $this->app->mount('', $c);
    }

    public function tearDown()
    {
        if ($this->app['orm.em']) {
            $this->app['orm.em']->close();
        }
        Application::clearInstance();
    }

    /**
     * 正常系のテストケース. tran1 が保存される.
     */
    public function testTran1()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/tran1');

        $this->verify('tran1');
    }

    /**
     * 異常系のテストケース.
     * すべてロールバックされ初期値が設定される
     */
    public function testTran2()
    {
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $companyName = $BaseInfo->getCompanyName();
        $client = $this->createClient();
        $crawler = $client->request('GET', '/tran2');

        $this->verify($companyName);
    }

    /**
     * 入れ子のトランザクションブロック.
     * 正常系. tran3 が設定される.
     */
    public function testTran3()
    {
        if ($this->isSqlite()) {
            $message = 'sqlite3 is not supported';
            $this->markTestSkipped($message);
        }

        $client = $this->createClient();
        $crawler = $client->request('GET', '/tran3');

        $this->verify('tran3');
    }

    /**
     * 入れ子のトランザクションブロック.
     * 異常系.
     * <pre>
     * BEGIN
     *   BEGIN
     *       UPDATE to tran4
     *   COMMIT
     * ROLLBACK
     * </pre>
     * 初期値が設定される.
     */
    public function testTran4()
    {
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $companyName = $BaseInfo->getCompanyName();

        $client = $this->createClient();
        $crawler = $client->request('GET', '/tran4');

        $this->verify($companyName);
    }

    /**
     * 入れ子のトランザクションブロック.
     * 異常系.
     * <pre>
     * BEGIN
     *   BEGIN
     *       UPDATE to tran5-1
     *   COMMIT
     *   BEGIN
     *       UPDATE to tran5-2
     *   COMMIT
     * ROLLBACK
     * </pre>
     * 初期値が設定される.
     */
    public function testTran5()
    {
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $companyName = $BaseInfo->getCompanyName();
        $client = $this->createClient();
        $crawler = $client->request('GET', '/tran5');

        $this->verify($companyName);
    }

    /**
     * 入れ子のトランザクションブロック.
     * 異常系.
     * <pre>
     * BEGIN
     *   BEGIN
     *       UPDATE to tran6-1
     *   COMMIT
     *   UPDATE to tran6-2
     *   UPDATE to tran6-3
     * ROLLBACK
     * </pre>
     * 初期値が設定される.
     */
    public function testTran6()
    {
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $companyName = $BaseInfo->getCompanyName();
        $client = $this->createClient();
        $crawler = $client->request('GET', '/tran6');

        $this->verify($companyName);
    }

    /**
     * 入れ子のトランザクションブロック.
     * 処理中に例外が発生するが、後続処理は正常終了するパターン.
     * <pre>
     * BEGIN
     *   BEGIN
     *       UPDATE to tran7-1
     *   ROLLBACK
     *   UPDATE to tran7-2
     *   UPDATE to tran7-3
     * COMMIT
     * </pre>
     * tran7-3 が設定される.
     */
    public function testTran7()
    {
        if ($this->isSqlite()) {
            $message = 'sqlite3 is not supported';
            $this->markTestSkipped($message);
        }

        $client = $this->createClient();
        $crawler = $client->request('GET', '/tran7');

        $this->verify('tran7-3');
    }

    /**
     * 入れ子のトランザクションブロック.
     * 異常系.
     * <pre>
     * BEGIN
     *   BEGIN
     *       UPDATE to tran8-1
     *   ROLLBACK
     *   UPDATE to tran8-2
     *   UPDATE to tran8-3
     * ROLLBACK
     * </pre>
     * 初期値が設定される.
     */
    public function testTran8()
    {
        if ($this->isSqlite()) {
            $message = 'sqlite3 is not supported';
            $this->markTestSkipped($message);
        }

        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $companyName = $BaseInfo->getCompanyName();
        $client = $this->createClient();
        $crawler = $client->request('GET', '/tran8');

        $this->verify($companyName);
    }

    /**
     * 入れ子のトランザクションブロック.
     * 異常系.
     * <pre>
     * BEGIN
     *   UPDATE to tran9-1
     *   BEGIN
     *       UPDATE to tran9-2
     *   ROLLBACK
     *   UPDATE to tran9-3
     * COMMIT
     * </pre>
     * 初期値が設定される.
     */
    public function testTran9()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/tran9');

        $this->verify('tran9-3');
    }

    protected function verify($expected, $message = '')
    {
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $this->app['orm.em']->detach($BaseInfo);

        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $actual = $BaseInfo->getCompanyName();
        $this->assertEquals($expected, $actual);
        $this->app['orm.em']->detach($BaseInfo);
    }

    /**
     * {@inheritdoc}
     */
    public function createApplication()
    {
        $app = Application::getInstance([
            'eccube.autoloader' => $GLOBALS['eccube.autoloader'],
        ]);
        $app['debug'] = true;

        // ログの内容をERRORレベルでしか出力しないように設定を上書き
        if (!$app->offsetExists('config')) {
            $app->extend('config', function ($config, $app) {
                $config['log']['log_level'] = 'ERROR';
                $config['log']['action_level'] = 'ERROR';
                $config['log']['passthru_level'] = 'ERROR';

                $channel = $config['log']['channel'];
                foreach (['monolog', 'front', 'admin'] as $key) {
                    $channel[$key]['log_level'] = 'ERROR';
                    $channel[$key]['action_level'] = 'ERROR';
                    $channel[$key]['passthru_level'] = 'ERROR';
                }
                $config['log']['channel'] = $channel;

                return $config;
            });
            $app->initLogger();
        }

        $app->initialize();
        $app->initializePlugin();

        $app['session.test'] = true;
        //$app->offsetUnset('exception_handler');

        $app->offsetUnset('csrf.token_manager');
        $app->register(new \Eccube\Tests\ServiceProvider\CsrfMockServiceProvider());

        if (!$app->offsetExists('eccube.fixture.generator')) {
            $app->register(new \Eccube\Tests\ServiceProvider\FixtureServiceProvider());
        }
        $app->boot();
        $app->flush();

        return $app;
    }
}

class TransactionControllerMock
{
    public function index(Application $app)
    {
        return $app->render('index.twig');
    }

    public function tran1(Application $app)
    {
        // update 1
        $BaseInfo = $app['eccube.repository.base_info']->get();
        $BaseInfo->setCompanyName('tran1');
        $app['orm.em']->flush($BaseInfo);

        return $app->render('index.twig');
    }

    public function tran2(Application $app)
    {
        // update 1
        $BaseInfo = $app['eccube.repository.base_info']->get();
        $BaseInfo->setCompanyName('tran2-1');
        $app['orm.em']->flush($BaseInfo);

        // update 2
        $BaseInfo->setCompanyName('tran2-1');
        $app['orm.em']->flush($BaseInfo);

        // 1/2 は rollback.
        throw new \Exception();

        return $app->render('index.twig');
    }

    public function tran3(Application $app)
    {
        $app['orm.em']->beginTransaction();

        try {
            $BaseInfo = $app['eccube.repository.base_info']->get();
            $BaseInfo->setCompanyName('tran3');
            $app['orm.em']->flush($BaseInfo);
            $app['orm.em']->commit();
        } catch (\Exception $e) {
            $app['orm.em']->rollback();
        }

        return $app->render('index.twig');
    }

    public function tran4(Application $app)
    {
        $app['orm.em']->beginTransaction();

        try {
            // update 1
            $BaseInfo = $app['eccube.repository.base_info']->get();
            $BaseInfo->setCompanyName('tran4');
            $app['orm.em']->flush($BaseInfo);
            $app['orm.em']->commit();

            // update 1 は rollback
            throw new \Exception();
        } catch (\Exception $e) {
            $app['orm.em']->rollback();
        }

        return $app->render('index.twig');
    }

    public function tran5(Application $app)
    {
        $app['orm.em']->beginTransaction();

        try {
            // update 1
            $BaseInfo = $app['eccube.repository.base_info']->get();
            $BaseInfo->setCompanyName('tran5-1');
            $app['orm.em']->flush($BaseInfo);
            $app['orm.em']->commit();
        } catch (\Exception $e) {
            $app['orm.em']->rollback();
        }

        $app['orm.em']->beginTransaction();

        try {
            // update 2
            $BaseInfo = $app['eccube.repository.base_info']->get();
            $BaseInfo->setCompanyName('tran5-2');
            $app['orm.em']->flush($BaseInfo);
            $app['orm.em']->commit();
        } catch (\Exception $e) {
            $app['orm.em']->rollback();
        }

        // update1/2はrollback
        throw new \Exception();

        return $app->render('index.twig');
    }

    public function tran6(Application $app)
    {
        $app['orm.em']->beginTransaction();

        try {
            // update 1
            $BaseInfo = $app['eccube.repository.base_info']->get();
            $BaseInfo->setCompanyName('tran6-1');
            $app['orm.em']->flush($BaseInfo);
            $app['orm.em']->commit();
        } catch (\Exception $e) {
            $app['orm.em']->rollback();
        }

        // update 2
        $BaseInfo->setCompanyName('tran6-2');
        $app['orm.em']->flush($BaseInfo);

        // update 3
        $BaseInfo->setCompanyName('tran6-3');
        $app['orm.em']->flush($BaseInfo);

        // update1/2/3 すべてrollback
        throw new \Exception();

        return $app->render('index.twig');
    }

    public function tran7(Application $app)
    {
        $app['orm.em']->beginTransaction();

        try {
            // update 1
            $BaseInfo = $app['eccube.repository.base_info']->get();
            $BaseInfo->setCompanyName('tran7-1');
            $app['orm.em']->flush($BaseInfo);
            $app['orm.em']->commit();

            // update 1がrollback
            throw new \Exception();
        } catch (\Exception $e) {
            // update 1がrollback
            $app['orm.em']->rollback();
        }

        // update 2
        $BaseInfo->setCompanyName('tran7-2');
        $app['orm.em']->flush($BaseInfo);

        // update 3
        $BaseInfo->setCompanyName('tran7-3');
        $app['orm.em']->flush($BaseInfo);

        // update2/3 は 暗黙のtransaction block 内のため、まとめて更新される.
        return $app->render('index.twig');
    }

    public function tran8(Application $app)
    {
        $app['orm.em']->beginTransaction();

        try {
            // update 1
            $BaseInfo = $app['eccube.repository.base_info']->get();
            $BaseInfo->setCompanyName('tran8-1');
            $app['orm.em']->flush($BaseInfo);
            $app['orm.em']->commit();

            // update 1がrollback
            throw new \Exception();
        } catch (\Exception $e) {
            // update 1がrollback
            $app['orm.em']->rollback();
        }

        // update 2
        $BaseInfo->setCompanyName('tran8-2');
        $app['orm.em']->flush($BaseInfo);

        // update 3
        $BaseInfo->setCompanyName('tran8-3');
        $app['orm.em']->flush($BaseInfo);

        // update2/3 は 暗黙のtransaction block内のため、2/3はrollbackされる
        throw new \Exception();

        return $app->render('index.twig');
    }

    public function tran9(Application $app)
    {
        // update 1 ：本体側の更新処理とする
        $BaseInfo = $app['eccube.repository.base_info']->get();
        $BaseInfo->setCompanyName('tran9-1');
        $app['orm.em']->flush($BaseInfo);

        // プラグインAが、beginTransactionして更新を行う
        $app['orm.em']->beginTransaction();

        try {
            // update 2
            $BaseInfo->setCompanyName('tran9-2');
            $app['orm.em']->flush($BaseInfo);
            $app['orm.em']->commit();

            // プラグイン内部でエラー
            throw new \Exception();
        } catch (\Exception $e) {
            // update 1 / update 2 がrollbackされる.
            $app['orm.em']->rollback();
        }

        // update 3：プラグインBが、更新処理を行う.
        $BaseInfo = $app['eccube.repository.base_info']->get();
        $BaseInfo->setCompanyName('tran9-3');
        $app['orm.em']->flush($BaseInfo);

        // update 1/2 はrollback され, update 3の更新処理のみ適応される
        return $app->render('index.twig');
    }
}
