<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Page\Admin;

class BlockManagePage extends AbstractAdminPageStyleGuide
{
    /**
     * BlockManagePage constructor.
     */
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I)
    {
        $page = new self($I);

        return $page->goPage('/content/block', 'ブロック管理コンテンツ管理');
    }

    public function 新規入力()
    {
        $this->tester->click('#page_admin_content_block > div.c-container > div.c-contentsArea > div.c-contentsArea__cols > div > div > div.row.justify-content-between.mb-2 > div.col-9 > a');
    }

    public function 編集($rowNum)
    {
        $this->tester->click("#page_admin_content_block > div > div.c-contentsArea > div.c-contentsArea__cols > div > div.c-primaryCol > div > div > div > ul > li:nth-child(${rowNum}) > div > div.col-auto.text-right > a:nth-child(1)");
    }

    public function 削除($rowNum)
    {
        $this->tester->click("#page_admin_content_block > div > div.c-contentsArea > div.c-contentsArea__cols > div > div.c-primaryCol > div > div > div > ul > li:nth-child(${rowNum}) > div > div.col-auto.text-right > a.btn.btn-ec-actionIcon.mr-3.disabled");
    }
}
