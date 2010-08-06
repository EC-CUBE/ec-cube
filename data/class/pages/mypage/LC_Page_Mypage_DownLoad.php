<?php
/*
 * This file is part of EC CUORE
 *
 * Copyright(c) 2009 CUORE CO.,LTD. All Rights Reserved.
 *
 * http://ec.cuore.jp/
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
ini_set("memory_limit","100M");
// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * ダウンロード商品ダウンロード のページクラス.
 *
 * @package Page
 * @author CUORE CO.,LTD.
 * @version $Id: LC_Page_Mypage_DownLoad.php 1 2009-08-04 00:00:00Z $
 */
class LC_Page_Mypage_DownLoad extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->allowClientCache();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        ob_end_clean();
        $objCustomer = new SC_Customer();
        //ログインしていない場合
        if (!$objCustomer->isLoginSuccess()){
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        } else {
        //ログインしている場合
            //DBから商品情報の読込
            $arrForm = $this->lfGetRealFileName($_GET['product_id']);

            //ステータスが支払済み以上である事
            if ($arrForm["status"] < ORDER_DELIV){
                SC_Utils_Ex::sfDispSiteError(DOWNFILE_NOT_FOUND,"",true);
            }
            //ファイル情報が無い場合はNG
            if ($arrForm["down_realfilename"] == "" ){
                SC_Utils_Ex::sfDispSiteError(DOWNFILE_NOT_FOUND,"",true);
            }
            //ファイルそのものが無い場合もとりあえずNG
            $realpath = DOWN_SAVE_DIR . $arrForm["down_realfilename"];
            if (!file_exists($realpath)){
                SC_Utils_Ex::sfDispSiteError(DOWNFILE_NOT_FOUND,"",true);
            }
            //ファイル名をエンコードする
            $sdown_filename = mb_convert_encoding($arrForm["down_filename"], "Shift_JIS", "auto");
            //タイプ指定
            header("Content-Type: Application/octet-stream");
            //ファイル名指定
            header("Content-Disposition: attachment; filename=" . $sdown_filename);
            header("Content-Transfer-Encoding: binary");
            //キャッシュ無効化
            header("Expires: Mon, 26 Nov 1962 00:00:00 GMT");
            header("Last-Modified: " . gmdate("D,d M Y H:i:s") . " GMT");
            //IE6+SSL環境下は、キャッシュ無しでダウンロードできない
            header("Cache-Control: private");
            header("Pragma: private");
            //ファイルサイズ指定
            $zv_filesize = filesize($realpath);
            header("Content-Length: " . $zv_filesize);
            set_time_limit(0);
            ob_end_flush();
            flush();

            //ファイル読み込み
            readfile($realpath);
        }
    }

    /* 商品情報の読み込み */
    function lfGetRealFileName($product_id) {
        $objQuery = new SC_Query();
        $col = "*";
        $table = "vw_download_class AS T1";
        if (DB_TYPE == "mysql"){
            $where = "T1.customer_id = " . (int)$_SESSION['customer']['customer_id'] . " AND T1.order_id = " . (int)$_GET['order_id'] . " AND T1.product_id = " . (int)$_GET['product_id'] .
                " AND (SELECT IF((SELECT d1.downloadable_days_unlimited FROM dtb_baseinfo d1)=1, 1, DATE(NOW()) <= DATE(DATE_ADD(T1.commit_date, INTERVAL (SELECT downloadable_days FROM dtb_baseinfo) DAY)))) = 1;";
        }else{
            $baseinfo = SC_Helper_DB_Ex::sf_getBasisData();
            $where = "T1.customer_id = " . (int)$_SESSION['customer']['customer_id'] . " AND T1.order_id = " . (int)$_GET['order_id'] . " AND T1.product_id = " . (int)$_GET['product_id'] .
                " AND (SELECT CASE WHEN (SELECT d1.downloadable_days_unlimited FROM dtb_baseinfo d1) = 1 THEN 1 WHEN DATE(NOW()) <= DATE(T1.commit_date + '". $baseinfo['downloadable_days'] ." days') THEN 1 ELSE 0 END) = 1;";
        }
        $arrRet = $objQuery->select($col, $table, $where);
        return $arrRet[0];
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }
}
?>
