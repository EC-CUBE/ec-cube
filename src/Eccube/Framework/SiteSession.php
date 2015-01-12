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
use Eccube\Framework\Util\Utils;

/* カートセッション管理クラス */
class SiteSession
{
    /* コンストラクタ */
    public function __construct()
    {
        // 前ページでの登録成功判定を引き継ぐ
        $_SESSION['site']['pre_regist_success'] =
                isset($_SESSION['site']['regist_success'])
                    ? $_SESSION['site']['regist_success'] : '';

        $_SESSION['site']['regist_success'] = false;
        $_SESSION['site']['pre_page'] =
                isset($_SESSION['site']['now_page'])
                    ? $_SESSION['site']['now_page'] : '';

        $_SESSION['site']['now_page'] = $_SERVER['SCRIPT_NAME'];
    }

    /* 前ページが正当であるかの判定 */
    public function isPrePage()
    {
        if ($_SESSION['site']['pre_page'] != '' && $_SESSION['site']['now_page'] != '') {
            if ($_SESSION['site']['pre_regist_success'] || $_SESSION['site']['pre_page'] == $_SESSION['site']['now_page']) {
                return true;
            }
        }

        return false;
    }

    public function setNowPage($path)
    {
        $_SESSION['site']['now_page'] = $path;
    }

    /* 値の取得 */
    public function getValue($keyname)
    {
        return $_SESSION['site'][$keyname];
    }

    /* ユニークIDの取得 */
    public function getUniqId()
    {
        // ユニークIDがセットされていない場合はセットする。
        if (!isset($_SESSION['site']['uniqid']) || $_SESSION['site']['uniqid'] == '') {
            $this->setUniqId();
        }

        return $_SESSION['site']['uniqid'];
    }

    /* ユニークIDのセット */
    public function setUniqId()
    {
        // 予測されないようにランダム文字列を付与する。
        $_SESSION['site']['uniqid'] = Utils::sfGetUniqRandomId();
    }

    /* ユニークIDのチェック */
    public function checkUniqId()
    {
        if (!empty($_POST['uniqid'])) {
            if ($_POST['uniqid'] != $_SESSION['site']['uniqid']) {
                return false;
            }
        }

        return true;
    }

    /* ユニークIDの解除 */
    public function unsetUniqId()
    {
        $_SESSION['site']['uniqid'] = '';
    }

    /* 登録成功を記録 */
    public function setRegistFlag()
    {
        $_SESSION['site']['regist_success'] = true;
    }
}
