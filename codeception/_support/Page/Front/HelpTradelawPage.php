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

class HelpTradelawPage extends AbstractFrontPage
{
    public static function go(\AcceptanceTester $I)
    {
        $page = new self($I);

        return $page->goPage('/help/tradelaw');
    }

    public function 名称($rowNum)
    {
        return $this->tester->grabTextFrom(['css' => "#page_help_tradelaw > div.ec-layoutRole > div.ec-layoutRole__contents > main > div > div.ec-off1Grid > div > div > dl:nth-child($rowNum) > dt > label"]);
    }

    public function 詳細($rowNum)
    {
        return $this->tester->grabTextFrom(['css' => "#page_help_tradelaw > div.ec-layoutRole > div.ec-layoutRole__contents > main > div > div.ec-off1Grid > div > div > dl:nth-child($rowNum) > dd"]);
    }
}
