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
require_once(CLASS_REALDIR . "pages/LC_Page.php");

/**
 * 郵便番号入力 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_InputZip extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_message = "住所を検索しています。";
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objView = new SC_SiteView(false);

        // 入力エラーチェック
        $arrErr = $this->fnErrorCheck($_GET);
        // 入力エラーの場合は終了
        if(count($arrErr) > 0) {
            $tpl_message = "";
            foreach($arrErr as $key => $val) {
                $tpl_message .= preg_replace("/<br \/>/", "\n", $val);
            }
            echo $tpl_message;
        
        // エラー無し
        } else {
            // 郵便番号検索文作成
            $zipcode = $_GET['zip1'].$_GET['zip2'];
            $zipcode = mb_convert_kana($zipcode ,"n");

            // 郵便番号検索
            $data_list = SC_Utils_Ex::sfGetAddress($zipcode);

            // 郵便番号が発見された場合
            if(!empty($data_list)) {
                $data = $data_list[0]['state']. "|". $data_list[0]['city']. "|". $data_list[0]['town'];
                echo $data;

            // 該当無し
            } else {
                echo "該当する住所が見つかりませんでした。";
            }
        }
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }


    /* 入力エラーのチェック */
    function fnErrorCheck($array) {
        // エラーメッセージ配列の初期化
        $objErr = new SC_CheckError($array);

        // 郵便番号
        $objErr->doFunc( array("郵便番号1",'zip1',ZIP01_LEN ) ,array( "NUM_COUNT_CHECK", "NUM_CHECK" ) );
        $objErr->doFunc( array("郵便番号2",'zip2',ZIP02_LEN ) ,array( "NUM_COUNT_CHECK", "NUM_CHECK" ) );
        // 親ウィンドウの戻り値を格納するinputタグのnameのエラーチェック
        if (!$this->lfInputNameCheck($array['input1'])) {
            $objErr->arrErr['input1'] = "※ 入力形式が不正です。<br />";
        }
        if (!$this->lfInputNameCheck($array['input2'])) {
            $objErr->arrErr['input2'] = "※ 入力形式が不正です。<br />";
        }

        return $objErr->arrErr;
    }

    /**
     * エラーチェック
     *
     * @param string $value
     * @return エラーなし：true エラー：false
     */
    function lfInputNameCheck($value) {
        // 半角英数字と_（アンダーバー）以外の文字を使用していたらエラー
        if(strlen($value) > 0 && !ereg("^[a-zA-Z0-9_]+$", $value)) {
            return false;
        }

        return true;
    }
}
?>
