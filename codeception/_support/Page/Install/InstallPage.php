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

namespace Page\Install;

class InstallPage extends AbstractInstallPage
{
    public static $STEP1_タイトル = '#main > div > div > div > div > div > div.page-header > h1';
    public static $STEP1_次へ = '//*[@id="form1"]/ul/li/button';

    public static $STEP2_タイトル = '#main > div > div > div.page-header > h1';
    public static $STEP2_テキストエリア = '#main > div > div > div.container-fluid > div > div > textarea';

    public static function go(\AcceptanceTester $I)
    {
        $page = new self($I);

        return $page->goPage('');
    }

    public function step1_次へボタンをクリック()
    {
        $this->tester->click(self::$STEP1_次へ);

        return $this;
    }

    public function step2_リロード()
    {
        $this->tester->reloadPage();

        return $this;
    }
}
