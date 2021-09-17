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

namespace Plugin;

use AcceptanceTester;

/**
 * @group plugin
 * @group vaddy
 */
class PL10GMCCest
{
    public function gmc(AcceptanceTester $I)
    {
        $I->amOnPage('/admin/gmc/config');
    }
}
