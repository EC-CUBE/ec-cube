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

use Page\Admin\OwnersPluginListPage;
use Eccube\Common\EccubeConfig;
use Codeception\Util\Fixtures;
use Eccube\Common\Constant;

/**
 * @group plugin
 * @group plugin_installer
 */
class AA00PluginInstallerByKeyCest
{

    protected $plugin = [];

    /** @var EccubeConfig */
    private $config;

    public function _before(AcceptanceTester $I)
    {
        $this->config = Fixtures::get('config');
        $url = $this->config->get('eccube_package_api_url').'/plugins/purchased';
        $authenticationKey = getenv('AUTHENTICATION_KEY');
        $pluginId = getenv('PLUGIN_ID');

        $context = stream_context_create(array(
            'http' => array(
                'method' => 'GET',
                'header' => array(
                    'X-ECCUBE-KEY: '.$authenticationKey,
                    'X-ECCUBE-VERSION: '.Constant::VERSION,
                ),
            )
        ));
            
        $result = json_decode(file_get_contents($url, false, $context), true);
        $this->plugin = array_reduce($result, function ($carry, $item) use ($pluginId) {
            if ($item['id'] == $pluginId) {
                $carry = $item;
            }
            return $carry;
        }, []); 

        $I->loginAsAdmin();
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function plugin_install(AcceptanceTester $I)
    {
        $plugin = $this->plugin;

            $I->wantTo('Install plugin ' . $plugin['name']);
            $OwnersPluginListPage = OwnersPluginListPage::go($I);
            // Back to plugin list page
            $authenticationKey = getenv('AUTHENTICATION_KEY');
            $OwnersPluginListPage->authenByKey($authenticationKey);
            $OwnersPluginListPage->go($I);
            $OwnersPluginListPage->install($plugin['code']);
    }
    public function plugin_enable(AcceptanceTester $I)
    {
        $plugin = $this->plugin;

        $I->wantTo('Enable Plugin' . $plugin['name']);
        $OwnersPluginListPage = OwnersPluginListPage::go($I);
        // Back to plugin list page
        $OwnersPluginListPage->enable($plugin['code']);
    }
    public function plugin_disable(AcceptanceTester $I)
    {
        $plugin = $this->plugin;
        $I->wantTo('Disable Plugin' . $plugin['name']);
        $OwnersPluginListPage = OwnersPluginListPage::go($I);
        $OwnersPluginListPage->disable($plugin['code']);
    }
    public function plugin_uninstall(AcceptanceTester $I)
    {
        $plugin = $this->plugin;
        $I->wantTo('Uninstall Plugin' . $plugin['name']);
        $OwnersPluginListPage = OwnersPluginListPage::go($I);
        $OwnersPluginListPage->uninstall($plugin['code']);

    }
}
