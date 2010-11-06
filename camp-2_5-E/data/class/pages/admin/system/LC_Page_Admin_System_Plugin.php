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
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * システム情報 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Admin_System_System.php 18701 2010-06-14 08:30:18Z nanasess $
 */
class LC_Page_Admin_System_Plugin extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'system/plugin.tpl';
        $this->tpl_subnavi  = 'system/subnavi.tpl';
        $this->tpl_subno    = 'system';
        $this->tpl_mainno   = 'system';
        $this->tpl_subtitle = 'プラグイン管理';
    }

    /**
     * フォームパラメータ初期化
     *
     * @return void
     */
    function initForm() {
        $objForm = new SC_FormParam();
        $objForm->addParam('mode', 'mode', INT_LEN, '', array('ALPHA_CHECK', 'MAX_LENGTH_CHECK'));
        $objForm->setParam($_GET);
        $this->objForm = $objForm;
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $query = new SC_Query();
        $sql = <<< SQL
    DROP TABLE IF EXISTS "dtb_plugin";
CREATE TABLE "dtb_plugin" (
  "id" int4 NOT NULL DEFAULT NULL,
  "name" text NOT NULL DEFAULT NULL,
  "enable" int2 NOT NULL DEFAULT 0,
  "del_flg" int2 NOT NULL DEFAULT 0,
  "class_name" text NOT NULL DEFAULT NULL,
  "create_date" timestamp(6) NOT NULL DEFAULT now(),
  "update_date" timestamp(6) NOT NULL DEFAULT now()
)
WITH (OIDS=FALSE);
ALTER TABLE "dtb_plugin" ADD CONSTRAINT "dtb_plugin_pkey" PRIMARY KEY ("id");    
SQL;
        $query->query($sql);
        
        exit;
        SC_Utils_Ex::sfIsSuccess(new SC_Session);
        $objView = new SC_AdminView();
        $this->initForm();
        switch($this->objForm->getValue('mode')) {
            // PHP INFOを表示
            case 'install':

                break;
            case 'uninstall':

                break;
            case 'enable':

                break;
            case 'disable':

                break;
            default:
                $plugins = SC_Helper_Plugin_Ex::getAllPlugin();
                var_dump($plugins);
                break;
        }

        $this->arrSystemInfo = $this->getSystemInfo();

        $objView->assignobj($this);
        $objView->display(MAIN_FRAME);
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
     * システム情報を取得する
     *
     * @return array
     */
    function getSystemInfo() {
        $objDB = SC_DB_DBFactory_Ex::getInstance();

        $arrSystemInfo = array(
        array('title' => 'EC-CUBE',  'value' => ECCUBE_VERSION),
        array('title' => 'OS',       'value' => php_uname()),
        array('title' => 'DBサーバ',  'value' => $objDB->sfGetDBVersion()),
        array('title' => 'WEBサーバ', 'value' => $_SERVER['SERVER_SOFTWARE']),
        array('title' => 'PHP',      'value' => phpversion()),
        array('title' => 'GD',       'value' => extension_loaded('GD') ? 'Loaded' : '--'),
        );

        return $arrSystemInfo;
    }
}
