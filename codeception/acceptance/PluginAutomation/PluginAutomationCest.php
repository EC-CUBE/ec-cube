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

use Codeception\Util\Fixtures;
use Doctrine\ORM\EntityManager;
use Eccube\Entity\Plugin;
use Eccube\Repository\PluginRepository;
use Page\Admin\PluginManagePage;
use Page\Admin\PluginSearchPage;

class EA10PluginAutomationCest
{   
    /** @var string */
    private $code;

    public function _before(AcceptanceTester $I)
    {
        $I->loginAsAdmin();
        $this->code = 'EditorJsBlog42';
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function test_install(AcceptanceTester $I)
    {
        Store_Plugin::start($I, $this->code)->インストール();
    }

    public function test_enable(AcceptanceTester $I)
    {
        Store_Plugin::start($I, $this->code)->有効化();
    }

    public function test_disable(AcceptanceTester $I)
    {
        Store_Plugin::start($I, $this->code)->無効化();
    }

    public function test_remove(AcceptanceTester $I)
    {
        Store_Plugin::start($I, $this->code)->削除();
    }
}


class Store_Plugin
{
    /** @var string */
    protected $code;

    /** @var AcceptanceTester */
    protected $I;

    /** @var PluginManagePage */
    protected $ManagePage;

    /** @var \Doctrine\DBAL\Connection */
    protected $conn;

    /** @var Plugin */
    protected $Plugin;

    /** @var EntityManager */
    protected $em;

    /** @var PluginRepository */
    protected $pluginRepository;

    public function __construct(AcceptanceTester $I, $code)
    {
        $this->I = $I;
        $this->code = $code;
        $this->em = Fixtures::get('entityManager');
        $this->conn = $this->em->getConnection();
        $this->pluginRepository = $this->em->getRepository(Plugin::class);
    }

    public static function start(AcceptanceTester $I, $code)
    {
        $result = new self($I, $code);

        return $result;
    }

    public function インストール()
    {
        $this->ManagePage = PluginSearchPage::go($this->I)->入手する($this->code)->インストール();

        $this->Plugin = $this->pluginRepository->findByCode($this->code);
        $this->I->assertFalse($this->Plugin->isInitialized(), '初期化されていない');
        $this->I->assertFalse($this->Plugin->isEnabled(), '有効化されていない');

        return $this;
    }

    public function 有効化()
    {
        $this->Plugin = $this->pluginRepository->findByCode($this->code);

        $this->ManagePage = PluginManagePage::go($this->I)->ストアプラグイン_有効化($this->code);

        $this->em->refresh($this->Plugin);
        $this->I->assertTrue($this->Plugin->isInitialized(), '初期化されている');
        $this->I->assertTrue($this->Plugin->isEnabled(), '有効化されている');

        return $this;
    }

    public function 無効化()
    {
        $this->Plugin = $this->pluginRepository->findByCode($this->code);
        $this->ManagePage = PluginManagePage::go($this->I)->ストアプラグイン_無効化($this->code);

        $this->em->refresh($this->Plugin);
        $this->I->assertTrue($this->Plugin->isInitialized(), '初期化されている');
        $this->I->assertFalse($this->Plugin->isEnabled(), '無効化されている');

        return $this;
    }

    public function 削除()
    {
        $this->Plugin = $this->pluginRepository->findByCode($this->code);
        $this->ManagePage = PluginManagePage::go($this->I)->ストアプラグイン_削除($this->code);

        $this->em->refresh($this->Plugin);
        $this->Plugin = $this->pluginRepository->findByCode($this->code);
        $this->I->assertNull($this->Plugin, '削除されている');

        return $this;
    }
}
