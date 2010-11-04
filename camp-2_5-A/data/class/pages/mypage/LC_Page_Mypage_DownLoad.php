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

        $customer_id = $_SESSION['customer']['customer_id'];
        $order_id = $_GET['order_id'];
        $product_id = $_GET['product_id'];
        $product_class_id = $_GET['product_class_id'];

        // ID の数値チェック
        // TODO SC_FormParam でチェックした方が良い?
        if (!is_numeric($customer_id)
            || !is_numeric($order_id)
            || !is_numeric($product_id)
            || !is_numeric($product_class_id)) {
            SC_Utils_Ex::sfDispSiteError("");
        }

        $objCustomer = new SC_Customer();
        //ログインしていない場合
        if (!$objCustomer->isLoginSuccess()){
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        } else {
        //ログインしている場合
            //DBから商品情報の読込
            $arrForm = $this->lfGetRealFileName($customer_id, $order_id, $product_id, $product_class_id);
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
            //ファイル名をエンコードする Safariの対策はUTF-8で様子を見る
            $encoding = "Shift_JIS";
            if(isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'],'Safari')) {
                $encoding = "UTF-8";
            }
            $sdown_filename = mb_convert_encoding($arrForm["down_filename"], $encoding, "auto");
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
            $handle = fopen($realpath, "rb");
            while (!feof($handle)) {
                echo(fread($handle, DOWNLOAD_BLOCK*1024));
                ob_flush();
                flush();
            }
            fclose($handle);
        }
    }

    /**
     * 商品情報の読み込みを行う.
     *
     * @param integer $customer_id 顧客ID
     * @param integer $order_id 受注ID
     * @param integer $product_id 商品ID
     * @param integer $product_class_id 商品規格ID
     * @return array 商品情報の配列
     */
    function lfGetRealFileName($customer_id, $order_id, $product_id, $product_class_id) {
        $objQuery = new SC_Query();
        $col = <<< __EOS__
            pc.product_id AS product_id,
            pc.product_class_id AS product_class_id,
            pc.down_realfilename AS down_realfilename,
            pc.down_filename AS down_filename,
            o.order_id AS order_id,
            o.customer_id AS customer_id,
            o.payment_date AS payment_date,
            o.status AS status
__EOS__;

        $table = <<< __EOS__
            dtb_products_class pc,
            dtb_order_detail od,
            dtb_order o
__EOS__;

        $dbFactory = SC_DB_DBFactory_Ex::getInstance();
        $where = "o.customer_id = ? AND o.order_id = ? AND pc.product_id = ? AND pc.product_class_id = ?";
        $where .= " AND " . $dbFactory->getDownloadableDaysWhereSql();
        $where .= " = 1";
        $arrRet = $objQuery->select($col, $table, $where,
                                    array($customer_id, $order_id, $product_id, $product_class_id));
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
