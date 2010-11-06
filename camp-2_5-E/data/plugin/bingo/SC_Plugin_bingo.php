<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

// {{{ requires
require_once(CLASS_EX_PATH . "SC_Plugin_Ex.php");
// }}}

class SC_Plugin_bingo extends SC_Plugin_Ex {
    var $plugin_name = null;

    // $X回に1回が当たり
    var $X = 1;

    function SC_Plugin_bingo() {
        $this->plugin_name = 'bingo';

        // $X回に1回が当たり
        // ここの値を変更して下さい。
        $this->X = 1;
    }

    function is_enable($class_name) {
//        parent::is_enable($class_name);

        $arrEnableClass = array(
            'LC_Page_Shopping_Confirm_Ex',
            'LC_Page_Shopping_Complete_Ex',
        );

        return in_array($class_name, $arrEnableClass);
    }

    function preProcess(&$objPage) {
//        parent::preProcess($objPage);

        $class_name = get_class($objPage);
        switch ($class_name) {
            case 'LC_Page_Shopping_Confirm_Ex':
                if ($_POST['mode'] == 'confirm') {
                    $this->lfDrawing();
                }
                break;
        }
    }

    function process(&$objPage) {
//        parent::process($objPage);

        $class_name = get_class($objPage);
        switch ($class_name) {
            case 'LC_Page_Shopping_Confirm_Ex':
                break;
            case 'LC_Page_Shopping_Complete_Ex':
                $is_bingo = $_SESSION['plugin_bingo']['is_bingo'];
                if ($is_bingo) {
                    if (SC_Utils_Ex::sfIsMobileSite()) {
                        $objPage->tpl_mainpage = DATA_PATH . 'plugin/' . $this->plugin_name . '/templates/mobile/shopping/complete.tpl';
                    } else {
                        $objPage->tpl_mainpage = DATA_PATH . 'plugin/' . $this->plugin_name . '/templates/shopping/complete.tpl';
                    }
                }
                break;
        }
    }

    function lfDrawing() {
        // is_bingoを初期化
        $_SESSION['plugin_bingo'] = array('is_bingo'=>false);

        $rand = mt_rand(1, $this->X);

        // 当たり!
        if ($rand === 1) {
            $objDb = new SC_Helper_DB_Ex();
            $objCartSess = new SC_CartSession();
            $objCustomer = new SC_Customer();
            $objSiteSess = new SC_SiteSession();

            $tmpData = $objDb->sfGetOrderTemp($uniqid);

            // 当たりなので、使用ポイントを未使用にする。
            $tmpData['use_point'] = 0;

            $cartKey = $_SESSION['cartKey'];
            $results = $objCartSess->calculate($cartKey, $objCustomer, $tmpData['use_point'], $tmpData['deliv_pref'], $tmpData['payment_id'], $tmpData['charge']);

            // 合計を0にするために合計額を値引にセット
            $tmpData['discount'] = $results['total'];

            // メモに「当たり」と記載する
            $tmpData['note'] = '当たり';

            $uniqid = $objSiteSess->getUniqId();
            $objDb->sfRegistTempOrder($uniqid, $tmpData);

            // is_bingoセット
            $_SESSION['plugin_bingo'] = array('is_bingo'=>true);
        }
    }
}

?>
