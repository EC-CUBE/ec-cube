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

namespace Page\Front;

class ShoppingLoginPage extends AbstractFrontPage
{
    /**
     * @param \AcceptanceTester $I
     */
    public static function at($I)
    {
        $page = new self($I);
        $page->tester->see('ログイン', 'div.ec-pageHeader h1');

        return $page;
    }

    public function ログイン($email, $password = 'password')
    {
        $this->tester->submitForm('div.ec-layoutRole__main form', [
            'login_email' => $email,
            'login_pass' => 'password',
        ]);

        return $this;
    }

    /**
     * @return ShoppingNonmemberPage
     */
    public function ゲスト購入()
    {
        $this->tester->click('div.ec-guest a.ec-blockBtn--cancel');

        return new ShoppingNonmemberPage($this->tester);
    }
}
