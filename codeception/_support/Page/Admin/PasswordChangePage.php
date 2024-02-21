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

class PasswordChangePage extends AbstractAdminPageStyleGuide
{
    public static function go($I)
    {
        $page = new self($I);

        $page->tester->click('header.c-headerBar div.c-headerBar__container a.c-headerBar__userMenu');
        $page->tester->click('.popover .btn-ec-regular');

        $page->atPage('パスワード変更');

        return $page;
    }

    public function 入力_パスワード($current_password, $new_password, $new_password_second)
    {
        $this->tester->fillField('#admin_change_password_current_password', $current_password);
        $this->tester->fillField('#admin_change_password_change_password_first', $new_password);
        $this->tester->fillField('#admin_change_password_change_password_second', $new_password_second);

        return $this;
    }

    public function 登録()
    {
        $this->tester->click('.c-conversionArea .btn-ec-conversion');

        return $this;
    }
}
