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

class NewsEditPage extends AbstractAdminPage
{
    /**
     * NewsRegisterPage constructor.
     */
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function of($I)
    {
        $page = new self($I);
        $page->atPage('新着情報管理コンテンツ管理');
        $page->tester->see('新着情報', '.c-container .c-contentsArea .c-contentsArea__cols .card-header');

        return $page;
    }

    public function 入力_日付($value)
    {
        $this->tester->executeJS("$('#admin_news_publish_date').val('".$value."').change();");

        return $this;
    }

    public function 入力_タイトル($value)
    {
        $this->tester->fillField(['id' => 'admin_news_title'], $value);

        return $this;
    }

    public function 入力_本文($value)
    {
        $this->tester->fillField(['id' => 'admin_news_description'], $value);

        return $this;
    }

    public function 登録()
    {
        $this->tester->click('.c-contentsArea .c-contentsArea__cols .c-conversionArea .btn-ec-conversion');

        return $this;
    }
}
