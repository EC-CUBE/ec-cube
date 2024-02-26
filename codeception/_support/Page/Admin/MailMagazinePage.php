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

class MailMagazinePage extends AbstractAdminPageStyleGuide
{
    public static function go(\AcceptanceTester $I)
    {
        $page = new self($I);
        $page->goPage('/plugin/mail_magazine', '配信メルマガ管理');
        return $page;
    }

    public static function at(\AcceptanceTester $I)
    {
        $page = new MailMagazinePage($I);
        $page->atPage('配信メルマガ管理');
        return $page;
    }

    public function 検索()
    {
        $this->tester->click(['css' => '#search_form > div:nth-child(3) > button']);
        return $this;
    }

    public function 配信内容を作成する()
    {
        $this->tester->click(['css' => '#search_form > div.c-contentsArea__cols > div > div > div.card.rounded.border-0.mb-4 > div > div:nth-child(3) > div > button']);
        return $this;
    }
}
