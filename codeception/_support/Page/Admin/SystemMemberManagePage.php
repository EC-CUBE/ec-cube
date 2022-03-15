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

class SystemMemberManagePage extends AbstractAdminPageStyleGuide
{
    public static function go($I)
    {
        $page = new self($I);

        return $page->goPage('/setting/system/member', 'メンバー管理システム設定');
    }

    public function 編集($name)
    {
        $this->tester->click("//*[@id='form1']//tr/td[1][contains(text(), '{$name}')]/parent::tr//*[@data-original-title='編集']");

        return $this;
    }

    public function 削除($name)
    {
        $this->tester->click("//*[@id='form1']//tr/td[1][contains(text(), '{$name}')]/parent::tr//*[@data-original-title='削除']/a");
        $this->tester->wait(1);
        $this->tester->click('.modal .btn-ec-delete');

        return $this;
    }
}
