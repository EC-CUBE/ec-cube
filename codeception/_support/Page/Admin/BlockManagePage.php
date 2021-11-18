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
        $rowNum++;
        $this->tester->click(".c-contentsArea .list-group > li:nth-child(${rowNum}) a[data-original-title=編集]");
    }

    public function 削除($rowNum)
    {
        $rowNum++;
        $this->tester->click(".c-contentsArea .list-group > li:nth-child(${rowNum}) [data-original-title=削除] a");
        return $this;
    }

    public function ポップアップを受け入れます()
    {
        $this->tester->waitForElementVisible(['css' => '.modal.show']);
        $this->tester->click('.modal.show .btn-ec-delete');

        return $this;
    }
}
