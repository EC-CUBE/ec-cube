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
    public static function go_new($I)
    {
        $page = new self($I);

        return $page->goPage('/setting/system/member/new', 'メンバー登録システム設定');
    }

    /**
     * @param \AcceptanceTester $I
     */
    public static function at($I)
    {
        $page = new self($I);
        $page->atPage('メンバー登録システム設定');

        return $page;
    }

    public function メンバー登録($form)
    {
        $form += [
            'name' => 'name',
            'department' => 'department',
            'login_id' => 'id',
            'password' => 'password',
            'password_second' => 'password',
            'authority' => 'システム管理者',
            'work' => 1,
        ];

        $this->tester->fillField('#admin_member_name', $form['name']);
        $this->tester->fillField('#admin_member_department', $form['department']);
        $this->tester->fillField('#admin_member_login_id', $form['login_id']);
        $this->tester->fillField('#admin_member_plain_password_first', $form['password']);
        $this->tester->fillField('#admin_member_plain_password_second', $form['password_second']);
        $this->tester->selectOption('#admin_member_Authority', $form['authority']);
        $this->tester->selectOption('input[name="admin_member[Work]"]', $form['work']);

        return $this;
    }

    public function 入力_パスワード($password, $password_second)
    {
        $this->tester->fillField('#admin_member_plain_password_first', $password);
        $this->tester->fillField('#admin_member_plain_password_second', $password_second);

        return $this;
    }

    public function 入力_稼働($value)
    {
        $this->tester->selectOption('input[name="admin_member[Work]"]', $value);

        return $this;
    }

    public function 登録()
    {
        $this->tester->click('.c-conversionArea .btn-ec-conversion');

        return $this;
    }
}
