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
use Eccube\Common\Constant;

class PluginAutomationCest
{  
    /** @var string */
    private $code;

    private $name;

    private $config;

    private $authenticationKey;

    public function _before(AcceptanceTester $I)
    {
        $I->loginAsAdmin();
        $this->config = Fixtures::get('config');
        $url = $this->config->get('eccube_package_api_url').'/plugins/purchased';
        $this->authenticationKey = getenv('AUTHENTICATION_KEY');
        $pluginId = getenv('PLUGIN_ID');

        $context = stream_context_create(array(
            'http' => array(
                'method' => 'GET',
                'header' => array(
                    'X-ECCUBE-KEY: '.$this->authenticationKey,
                    'X-ECCUBE-VERSION: '.Constant::VERSION,
                ),
            )
        ));
            
        $result = json_decode(file_get_contents($url, false, $context), true);
        $targetPlugin = array_reduce($result, function ($carry, $item) use ($pluginId) {
            if ($item['id'] == $pluginId) {
                $carry = $item;
            }
            return $carry;
        }, []);
        if(count($targetPlugin) > 0) {
            $this->code = $targetPlugin['code'];
            $this->name = $targetPlugin['name'];
        }
        
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function test_authenKey(AcceptanceTester $I)
    {
        $I->wantTo('Authentication key setting');

        $I->amOnPage('/'.$this->config['eccube_admin_route'].'/store/plugin/authentication_setting');
        $I->expect('認証キーの入力を行います。');
        $I->fillField(['id' => 'admin_authentication_authentication_key'], $this->authenticationKey);

        $I->expect('認証キーの登録ボタンをクリックします。');
        $I->click(['css' => '.btn-ec-conversion']);
        $I->waitForText('保存しました');
        $I->wantTo('Test authentication key is valid:');
        PluginManagePage::go($I);
        $I->waitForText('オーナーズストアのプラグイン');
        $I->see($this->name, 'table');
    }


    public function test_searchByName(AcceptanceTester $I)
    {
        $I->wantTo('Test search plugin by name');
        PluginSearchPage::go($I);
        $I->fillField(['id' => 'search_plugin_keyword'], $this->name);
        $I->click('検索');
        $I->see($this->name, '#plugin-list');
    }

    public function test_install(AcceptanceTester $I)
    {
        $I->wantTo('Test install plugin:'.$this->name);
        Store_Plugin::start($I, $this->code)->install();
    }

    
    public function test_enable(AcceptanceTester $I)
    {
        $I->wantTo('Test enable plugin:'.$this->name);
        Store_Plugin::start($I, $this->code)->enable();
    }

    public function test_disable(AcceptanceTester $I)
    {
        $I->wantTo('Test disable plugin:'.$this->name);
        Store_Plugin::start($I, $this->code)->disable();
    }

    public function test_remove(AcceptanceTester $I)
    {
        $I->wantTo('Test uninstall plugin:'.$this->name);
        Store_Plugin::start($I, $this->code)->uninstall();
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

    /** @var EccubeConfig */
    protected $config;

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
        $this->config = Fixtures::get('config');
        $this->conn = $this->em->getConnection();
        $this->pluginRepository = $this->em->getRepository(Plugin::class);
    }

    public static function start(AcceptanceTester $I, $code)
    {
        $result = new self($I, $code);

        return $result;
    }

    public function install()
    {
        PluginManagePage::go($this->I);
        $this->I->click(['xpath' => '//p[contains(text(),"'.$this->code.'")]/ancestor::tr/td/a[contains(text(),"インストール")]']);
        $this->I->click('インストール');
        $this->I->waitForElement('#installModal');
        $this->I->seeElement('#installModal');
        $this->I->waitForText('をインストールしますか？', 20, '#installModal .modal-body');
        $this->I->click('インストール', '#installModal');
        $this->I->waitForText('インストールが完了しました。', 60, '#installModal .modal-body');
        $this->I->click('完了','#installModal .modal-footer');

        $this->Plugin = $this->pluginRepository->findByCode($this->code);
        $this->I->assertFalse($this->Plugin->isInitialized(), '初期化されていない');
        $this->I->assertFalse($this->Plugin->isEnabled(), '有効化されていない');
        $this->I->assertDirectoryExists($this->config['plugin_realdir'].'/'.$this->code);

        return $this;
    }

    public function enable()
    {
        $this->Plugin = $this->pluginRepository->findByCode($this->code);

        $this->ManagePage = PluginManagePage::go($this->I)->ストアプラグイン_有効化($this->code);

        $this->em->refresh($this->Plugin);
        $this->I->assertTrue($this->Plugin->isInitialized(), '初期化されている');
        $this->I->assertTrue($this->Plugin->isEnabled(), '有効化されている');

        return $this;
    }

    public function disable()
    {
        $this->Plugin = $this->pluginRepository->findByCode($this->code);
        $this->ManagePage = PluginManagePage::go($this->I)->ストアプラグイン_無効化($this->code);

        $this->em->refresh($this->Plugin);
        $this->I->assertTrue($this->Plugin->isInitialized(), '初期化されている');
        $this->I->assertFalse($this->Plugin->isEnabled(), '無効化されている');

        return $this;
    }

    public function uninstall()
    {
        $this->Plugin = $this->pluginRepository->findByCode($this->code);
        $this->ManagePage = PluginManagePage::go($this->I)->ストアプラグイン_削除($this->code);

        $this->em->refresh($this->Plugin);
        $this->Plugin = $this->pluginRepository->findByCode($this->code);
        $this->I->assertNull($this->Plugin, '削除されている');

        $this->I->assertDirectoryDoesNotExist($this->config['plugin_realdir'].'/'.$this->code);

        return $this;
    }
}