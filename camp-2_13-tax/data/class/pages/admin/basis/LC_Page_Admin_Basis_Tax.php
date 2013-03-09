<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
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
 * 税金管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Admin_Basis_Tax_Ex.php 22567 2013-03-09 12:18:54Z yomoro $
 */
class LC_Page_Admin_Basis_Tax extends LC_Page_Admin_Ex 
{

    // {{{ properties

    /** エラー情報 */
    var $arrErr;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init()
    {
        parent::init();
        $this->tpl_mainpage = 'basis/tax.tpl';
        $this->tpl_subno = 'tax';
        $this->tpl_mainno = 'basis';
        $this->tpl_maintitle = '基本情報管理';
        $this->tpl_subtitle = '税金管理';
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrTAXCALCRULE = $masterData->getMasterData('mtb_taxrule');

        //配信時刻の項目値設定
        $this->objDate = new SC_Date();
        // 配信時間の年を、「現在年~現在年＋１」の範囲に設定
        for ($year=date("Y"); $year<=date("Y") + 1;$year++){
            $arrYear[$year] = $year;
        }
        $this->arrYear = $arrYear;

        for ($minutes=0; $minutes< 60; $minutes++){
            $arrMinutes[$minutes] = $minutes;
        }
        $this->arrMinutes = $arrMinutes;

    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process()
    {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action()
    {

    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy()
    {
        parent::destroy();
    }
}
