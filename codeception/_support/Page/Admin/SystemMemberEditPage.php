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

class SystemMemberEditPage extends AbstractAdminPageStyleGuide
{
    /**
     * @param \AcceptanceTester $I
     */
    public static function at($I)
    {
        $page = new self($I);
        $page->atPage('メンバー登録システム設定');

        return $page;
    }

    public function 入力_パスワード($password, $password_second)
    {
        $this->tester->fillField('#admin_member_plain_password_first', $password);
        $this->tester->fillField('#admin_member_plain_password_second', $password_second);

        return $this;
    }

    public function 登録()
    {
        $this->tester->click('.c-conversionArea .btn-ec-conversion');

        return $this;
    }
}
