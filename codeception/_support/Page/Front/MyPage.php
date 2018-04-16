<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2017 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Page\Front;


class MyPage extends AbstractFrontPage
{

    const ORDER_HISTORY = 'ul.ec-navlistRole__navlist li:nth-child(1) a';
    const FAVORITE = 'ul.ec-navlistRole__navlist li:nth-child(2) a';
    const USER_INFO = 'ul.ec-navlistRole__navlist li:nth-child(3) a';
    const ADDRESS = 'ul.ec-navlistRole__navlist li:nth-child(4) a';
    const WITHDRAW = 'ul.ec-navlistRole__navlist li:nth-child(5) a';

    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I) {
        $page = new self($I);
        $page->goPage('/mypage');
        return $page;
    }

    public static function at($I)
    {
        $page = new self($I);
        $page->tester->see('ご注文履歴', self::ORDER_HISTORY);
        $page->tester->see('お気に入り一覧', self::FAVORITE);
        $page->tester->see('会員情報編集', self::USER_INFO);
        $page->tester->see('お届け先編集', self::ADDRESS);
        $page->tester->see('退会手続き', self::WITHDRAW);
        return $page;
    }

    public function 注文履歴()
    {
        $this->tester->click(self::ORDER_HISTORY);
        return $this;
    }

    public function 注文履歴詳細($num)
    {
        $num += 2;
        $this->tester->click(".ec-layoutRole__main div:nth-child(${num}) p.ec-historyListHeader__action a");
    }

    public function お気に入り一覧()
    {
        $this->tester->click(self::FAVORITE);
        return $this;
    }

    public function 会員情報編集()
    {
        $this->tester->click(self::USER_INFO);
        return $this;
    }

    public function お届け先編集()
    {
        $this->tester->click(self::ADDRESS);
        return new CustomerAddressListPage($this->tester);
    }

    public function 退会手続き()
    {
        $this->tester->click(self::WITHDRAW);
    }
}