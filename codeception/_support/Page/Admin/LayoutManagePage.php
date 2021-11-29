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

class LayoutManagePage extends AbstractAdminPageStyleGuide
{
    public static $登録完了メッセージ = ['css' => '.c-contentsArea .alert'];

    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I)
    {
        $page = new self($I);

        return $page->goPage('/content/layout', 'レイアウト管理コンテンツ管理');
    }

    public function レイアウト編集($layoutName)
    {
        $this->tester->click(['xpath' => "//*[@id=\"page_admin_content_layout\"]/div[1]/div[3]/div[2]/div/div//div/a[translate(text(), ' \r\n', '')='${layoutName}']"]);
    }

    public function 削除($layoutName)
    {
        $this->tester->click(['xpath' => "//button[contains(@data-message, '「{$layoutName}」を削除')]"]);
        $this->tester->waitForElementVisible(['css' => '.modal.show']);
        $this->tester->wait(1);
        $this->tester->click('.modal.show .btn-ec-delete');
    }

    public function 新規登録()
    {
        $this->tester->click(['xpath' => '//*[@id="page_admin_content_layout"]/div[1]/div[3]/div[2]/div/div/div[1]/a']);
    }
}
