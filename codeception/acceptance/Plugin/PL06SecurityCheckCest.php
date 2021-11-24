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

namespace Plugin;

use AcceptanceTester;

/**
 * @group plugin
 * @group vaddy
 */
class PL06SecurityCheckCest
{

    public function セキュリティチェック(AcceptanceTester $I)
    {
        $I->loginAsAdmin();
        $I->amOnPage('/admin/store/plugin/Securitychecker4/config');
        $I->scrollTo(['id' => 'securitychecker4_config_tools_agreement'], 0, -100);
        $I->click(['id' => 'securitychecker4_config_eccube_share_0']);
        $I->checkOption(['id' => 'securitychecker4_config_tools_agreement']);
        $I->click(['css' => '#page_securitychecker4_admin_config > div > div.c-contentsArea > form > div > div > div.c-conversionArea > div > div > div:nth-child(2) > div > div > button']);
        $I->waitForText('セキュリティチェックが完了しました。', 120, ['css' => '#page_securitychecker4_admin_config > div > div.c-contentsArea > div.alert.alert-success.alert-dismissible.fade.show.m-3 > span']);

        $I->amOnPage('/admin/store/plugin/Securitychecker4/config/customer/download');
        $I->amOnPage('/admin/store/plugin/Securitychecker4/config/customer_address/download');
        $I->amOnPage('/admin/store/plugin/Securitychecker4/config/order/download');
        $I->amOnPage('/admin/store/plugin/Securitychecker4/config/shipping/download');
    }
}
