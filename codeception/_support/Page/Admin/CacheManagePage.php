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

class CacheManagePage extends AbstractAdminPageStyleGuide
{
    public static function go($I)
    {
        $page = new self($I);

        return $page->goPage('/content/cache', 'キャッシュ管理コンテンツ管理');
    }

    public function キャッシュ削除()
    {
        $this->tester->click('//*[@id="page_admin_content_cache"]/div[1]/div[3]/form/div/div/div/div/div[2]/div[2]/div/button');
    }
}
