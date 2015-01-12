<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Framework;

use Eccube\Application;

/**
 * スマートフォンの情報を扱うクラス.
 *
 * @auther Yu Nobira
 */
class SmartphoneUserAgent
{
    /**
     * スマートフォンかどうかを判別する。
     * $_SESSION['pc_disp'] = true の場合はPC表示。
     *
     * @return boolean
     */
    public function isSmartphone()
    {
        $detect = new \Mobile_Detect;
        // SPでかつPC表示OFFの場合
        // TabletはPC扱い
        return ($detect->isMobile() && !$detect->isTablet()) && !static::getSmartphonePcFlag();
    }

    /**
     * スマートフォンかどうかを判別する。
     *
     * @return boolean
     */
    public function isNonSmartphone()
    {
        return !static::isSmartphone();
    }

    /**
     * PC表示フラグの取得
     *
     * @return string
     */
    public function getSmartphonePcFlag()
    {
        $_SESSION['pc_disp'] = empty($_SESSION['pc_disp']) ? false : $_SESSION['pc_disp'];

        return $_SESSION['pc_disp'];
    }

    /**
     * PC表示ON
     */
    public function setPcDisplayOn()
    {
        $_SESSION['pc_disp'] = true;
    }

    /**
     * PC表示OFF
     */
    public function setPcDisplayOff()
    {
        $_SESSION['pc_disp'] = false;
    }
}
