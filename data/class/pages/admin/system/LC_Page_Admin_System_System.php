<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * システム情報 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_System_System extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'system/system.tpl';
        $this->tpl_subno    = 'system';
        $this->tpl_mainno   = 'system';
        $this->tpl_maintitle = 'システム設定';
        $this->tpl_subtitle = 'システム情報';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {

        $objFormParam = new SC_FormParam_Ex();

        $this->initForm($objFormParam, $_GET);
        switch ($this->getMode()) {

            // PHP INFOを表示
            case 'info':
                phpinfo();
                SC_Response_Ex::actionExit();
                break;

            default:
                break;
        }

        $this->arrSystemInfo = $this->getSystemInfo();

    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /**
     * フォームパラメーター初期化.
     *
     * @param object $objFormParam
     * @param array $arrParams $_GET値
     * @return void
     */
    function initForm(&$objFormParam, &$arrParams) {
        $objFormParam->addParam('mode', 'mode', INT_LEN, '', array('ALPHA_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->setParam($arrParams);
    }

    /**
     * システム情報を取得する.
     *
     * @return array システム情報
     */
    function getSystemInfo() {
        $objDB = SC_DB_DBFactory_Ex::getInstance();

        $arrSystemInfo = array(
            array('title' => 'EC-CUBE',     'value' => ECCUBE_VERSION),
            array('title' => 'サーバーOS',    'value' => php_uname()),
            array('title' => 'DBサーバー',    'value' => $objDB->sfGetDBVersion()),
            array('title' => 'WEBサーバー',   'value' => $_SERVER['SERVER_SOFTWARE']),
        );

        $value = phpversion() . ' (' . implode(', ', get_loaded_extensions()) . ')';
        $arrSystemInfo[] = array('title' => 'PHP', 'value' => $value);

        if (extension_loaded('GD') || extension_loaded('gd')) {
            $arrValue = array();
            foreach (gd_info() as $key => $val) {
                $arrValue[] = "$key => $val";
            }
            $value = '有効 (' . implode(', ', $arrValue) . ')';
        } else {
            $value = '無効';
        }
        $arrSystemInfo[] = array('title' => 'GD', 'value' => $value);
        $arrSystemInfo[] = array('title' => 'HTTPユーザーエージェント', 'value' => $_SERVER['HTTP_USER_AGENT']);

        return $arrSystemInfo;
    }
}
