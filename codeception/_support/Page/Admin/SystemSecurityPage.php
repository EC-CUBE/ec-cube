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

namespace Page\Admin;

class SystemSecurityPage extends AbstractAdminPageStyleGuide
{
    /**
     * @param \AcceptanceTester $I
     */
    public static function go($I)
    {
        $page = new self($I);

        return $page->goPage('/setting/system/security', 'セキュリティ管理システム設定');
    }

    /**
     * @param \AcceptanceTester $I
     */
    public static function at($I)
    {
        $page = new self($I);
        $page->atPage('セキュリティ管理システム設定');

        return $page;
    }

    public function 入力_front許可リスト($ip)
    {
        $this->tester->fillField(['id' => 'admin_security_front_allow_hosts'], $ip);

        return $this;
    }

    public function 入力_front拒否リスト($ip)
    {
        $this->tester->fillField(['id' => 'admin_security_front_deny_hosts'], $ip);

        return $this;
    }

    public function 登録()
    {
        $this->tester->click('#page_admin_setting_system_security form div.c-contentsArea__cols > div.c-conversionArea > div > div > div:nth-child(2) > div > div > button');
        $this->tester->see('保存しました');

        return $this;
    }
}
