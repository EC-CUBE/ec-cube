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
require_once(CLASS_PATH . "pages/admin/LC_Page_Admin.php");

/**
 * トラックバック編集 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Products_TrackbackEdit extends LC_Page_Admin {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'products/trackback_edit.tpl';
        $this->tpl_subnavi = 'products/subnavi.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'trackback';
        $this->tpl_subtitle = 'トラックバック管理';
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
        $objSess = new SC_Session();
        $objQuery = new SC_Query();

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

        //検索ワードの引継ぎ
        foreach ($_POST as $key => $val){
            if (ereg("^search_", $key)){
                $this->arrSearchHidden[$key] = $val;
            }
        }

        // 状態の設定
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrTrackBackStatus = $masterData->getMasterData("mtb_track_back_status");

        //取得文字列の変換用カラム
        $arrRegistColumn = array (
                                  array( "column" => "update_date"),
                                  array( "column" => "status"),
                                  array(	"column" => "title","convert" => "KVa"),
                                  array(	"column" => "excerpt","convert" => "KVa"),
                                  array(	"column" => "blog_name","convert" => "KVa"),
                                  array(	"column" => "url","convert" => "KVa"),
                                  array(	"column" => "del_flg","convert" => "n")
                                  );

        // トラックバックIDを渡す
        $this->tpl_trackback_id = $_POST['trackback_id'];
        // トラックバック情報のカラムの取得
        $this->arrTrackback = $this->lfGetTrackbackData($_POST['trackback_id'], $objQuery);

        // 商品ごとのトラックバック表示数取得
        $count = $objQuery->count("dtb_trackback", "del_flg = 0 AND product_id = ?", array($this->arrTrackback['product_id']));
        // 両方選択可能
        $this->tpl_status_change = true;

        switch($_POST['mode']) {
            // 登録
        case 'complete':
            //フォーム値の変換
            $arrTrackback = $this->lfConvertParam($_POST, $arrRegistColumn);
            $this->arrErr = $this->lfCheckError($arrTrackback);
            //エラー無し

            if (!$this->arrErr) {
                //レビュー情報の編集登録
                $this->lfRegistTrackbackData($arrTrackback, $arrRegistColumn, $objQuery);
                $this->arrTrackback = $arrTrackback;
                $this->tpl_onload = "confirm('登録が完了しました。');";
            }
            break;

        default:
            break;
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


    // 入力エラーチェック
    function lfCheckError($array) {
        $objErr = new SC_CheckError($array);
        $objErr->doFunc(array("ブログ名", "blog_name", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("ブログ記事タイトル", "title", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("ブログ記事内容", "excerpt", LTEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("ブログURL", "url", URL_LEN), array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("状態", "status"), array("SELECT_CHECK"));
        return $objErr->arrErr;
    }

    //----　取得文字列の変換
    function lfConvertParam($array, $arrRegistColumn) {
        /*
         *	文字列の変換
         *	K :  「半角(ﾊﾝｶｸ)片仮名」を「全角片仮名」に変換
         *	C :  「全角ひら仮名」を「全角かた仮名」に変換
         *	V :  濁点付きの文字を一文字に変換。"K","H"と共に使用します
         *	n :  「全角」数字を「半角(ﾊﾝｶｸ)」に変換
         *  a :  全角英数字を半角英数字に変換する
         */
        // カラム名とコンバート情報
        foreach ($arrRegistColumn as $data) {
            $arrConvList[ $data["column"] ] = isset($data["convert"]) ? $data["convert"] : "";
        }

        // 文字変換
        foreach ($arrConvList as $key => $val) {
            // POSTされてきた値のみ変換する。
            if (!isset($array[$key])) $array[$key] = "";
            if(strlen(($array[$key])) > 0) {
                $array[$key] = mb_convert_kana($array[$key] ,$val);
            }
        }
        return $array;
    }

    // トラックバック情報の取得
    function lfGetTrackbackData($trackback_id, &$objQuery) {

        $select = "tra.trackback_id, tra.product_id, tra.blog_name, tra.title, tra.excerpt, ";
        $select .= "tra.url, tra.status, tra.create_date, tra.update_date, pro.name ";
        $from = "dtb_trackback AS tra LEFT JOIN dtb_products AS pro ON tra.product_id = pro.product_id ";
        $where = "tra.del_flg = 0 AND pro.del_flg = 0 AND tra.trackback_id = ? ";
        $arrTrackback = $objQuery->select($select, $from, $where, array($trackback_id));
        if(!empty($arrTrackback)) {
            $this->arrTrackback = $arrTrackback[0];
        } else {
            sfDispError("");
        }
        return $this->arrTrackback;
    }

    // トラックバック情報の編集登録
    function lfRegistTrackbackData($array, $arrRegistColumn, &$objQuery) {

        foreach ($arrRegistColumn as $data) {
            if (!isset($array[$data["column"]])) $array[$data["column"]] = "";
            if (strlen($array[ $data["column"] ]) > 0 ) {
                $arrRegist[ $data["column"] ] = $array[ $data["column"] ];
            }
            if ($data['column'] == 'update_date'){
                $arrRegist['update_date'] = 'now()';
            }
        }
        //登録実行
        $objQuery->begin();
        $objQuery->update("dtb_trackback", $arrRegist, "trackback_id = '".$_POST['trackback_id']."'");
        $objQuery->commit();
    }
}
?>
