<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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

/**
 * Webページのレイアウト情報を制御するヘルパークラス.
 *
 * @package Helper
 * @author LOCKON CO.,LTD.
 * @version $Id:SC_Helper_PageLayout.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class SC_Helper_PageLayout {

    // }}}
    // {{{ functions

    /**
     * ページのレイアウト情報をセットする.
     *
     * LC_Page オブジェクトにページのレイアウト情報をセットする.
     *
     * @param LC_Page $objPage ページ情報
     * @param boolean $preview プレビュー表示の場合 true
     * @param string $url ページのURL
     * @return void
     */
    function sfGetPageLayout(&$objPage, $preview = false, $url = ""){
        $debug_message = "";
    	$arrPageLayout = array();

        // 現在のURLの取得
        if ($preview === false) {
            if ($url == "") {
                $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
            }
            // URLを元にページデザインを取得
            $arrPageData = $this->lfgetPageData(" url = ? " , array($url));
        }else{
            $arrPageData = $this->lfgetPageData(" page_id = ? " , array("0"));
            $objPage->tpl_mainpage = USER_PATH . "templates/preview/"
                . TEMPLATE_NAME . "/" . $arrPageData[0]['filename'] . ".tpl";
        }
        
        reset($arrPageData[0]);
		while( list($key,$val) = each($arrPageData[0]) ){
        	 $debug_message.= "arrPageData[$key]：" . $val . "\n";
        }
        
        $debug_message.= "TEMPLATE_NAME：".TEMPLATE_NAME . "\n";
        
        // tpl_mainpageの設定なし、又はトップページの場合
        if (!isset($objPage->tpl_mainpage) || $url == "index.php") {
            // ユーザテンプレートのパスを取得
            $user_tpl =  HTML_PATH . USER_DIR . USER_PACKAGE_DIR . TEMPLATE_NAME . "/" . $arrPageData[0]['filename'] . ".tpl";
            $debug_message.= "ユーザテンプレートチェック：".$user_tpl."\n";
            
            // ユーザテンプレートの存在チェック
            if (is_file($user_tpl)) {
                $objPage->tpl_mainpage = $user_tpl;
                $debug_message.= "tpl_mainpage：ユーザーテンプレート\n";
            // 存在しない場合は指定テンプレートを使用
            } else {
                $objPage->tpl_mainpage = TEMPLATE_DIR . $arrPageData[0]['filename'] . ".tpl";
                $debug_message.= "tpl_mainpage：標準テンプレート\n";
            }
        } else {
            $debug_message.= "tpl_mainpage：設定あり" . "\n";
        }
        
        $debug_message.= "tpl_mainpage：" . $objPage->tpl_mainpage . "\n";

        // ページタイトルを設定
        if (!isset($objPage->tpl_title)) {
            $objPage->tpl_title = $arrPageData[0]['page_name'];
        }

        $arrPageLayout = $arrPageData[0];

        // 全ナビデータを取得する
        $arrNavi = $this->lfGetNaviData($url, $preview);

        $arrPageLayout['LeftNavi']  = $this->lfGetNavi($arrNavi,1);	// LEFT NAVI
        $arrPageLayout['MainHead']  = $this->lfGetNavi($arrNavi,2);	// メイン上部
        $arrPageLayout['RightNavi'] = $this->lfGetNavi($arrNavi,3);	// RIGHT NAVI
        $arrPageLayout['MainFoot']  = $this->lfGetNavi($arrNavi,4);	// メイン下部

        GC_Utils::gfDebugLog($arrPageLayout);
        
        $objPage->arrPageLayout = $arrPageLayout;
        
        // カラム数を取得する
        $objPage->tpl_column_num = $this->lfGetColumnNum($arrPageLayout);

        GC_Utils::gfDebugLog($debug_message);
    }

    /**
     * ページ情報を取得する.
     *
     * @param string $where クエリのWHERE句
     * @param array $arrVal WHERE句の条件値
     * @return array ページ情報を格納した配列
     */
    function lfgetPageData($addwhere = '', $sqlval = ''){
        $objQuery = new SC_Query;		// DB操作オブジェクト
        $arrRet = array();				// データ取得用

        // SQL文生成
        // 取得するカラム
        $col  = " page_id";				// ページID
        $col .= " ,page_name";			// 名称
        $col .= " ,url";				// URL
        $col .= " ,php_dir";			// php保存先ディレクトリ
        $col .= " ,tpl_dir";			// tpl保存先ディレクトリ
        $col .= " ,filename";			// ファイル名称
        $col .= " ,header_chk ";		// ヘッダー使用FLG
        $col .= " ,footer_chk ";		// フッター使用FLG
        $col .= " ,edit_flg ";			// 編集可能FLG
        $col .= " ,author";				// authorタグ
        $col .= " ,description";		// descriptionタグ
        $col .= " ,keyword";			// keywordタグ
        $col .= " ,update_url";			// 更新URL
        $col .= " ,create_date";		// データ作成日
        $col .= " ,update_date";		// データ更新日
        
        // 取得するテーブル
        $table = "dtb_pagelayout";
        
        // where句の指定があれば追加
        $where = ($addwhere != '') ? $addwhere : "page_id <> 0";
        
        // 並び変え
        $objQuery->setOrder('page_id');
        
        // SQL実行
        $arrRet = $objQuery->select($col, $table, $where, $sqlval);
        
        // 結果を返す
        return $arrRet;
    }

    /**
     * ナビ情報を取得する.
     *
     * @param string $url ページのURL
     * @param boolean $preview プレビュー表示の場合 true
     * @return array ナビ情報の配列
     */
    function lfGetNaviData($url, $preview=false){
        $objQuery = new SC_Query;		// DB操作オブジェクト
        $sql = "";						// データ取得SQL生成用
        $arrRet = array();				// データ取得用
        $arrData = array();

        // SQL文生成
        // 取得するカラム
        $col = "target_id, bloc_name, tpl_path, php_path";
        
        // 取得するテーブル
        $table = "dtb_blocposition AS pos, dtb_bloc AS bloc";
        
        // where文生成
        $where = "bloc.bloc_id = pos.bloc_id";
        if ($preview == true) {
            $where .= " AND EXISTS (SELECT page_id FROM dtb_pagelayout AS lay WHERE page_id = '0' AND pos.page_id = lay.page_id)";
        }else{
            $where .= " AND EXISTS (SELECT page_id FROM dtb_pagelayout AS lay WHERE url = ? AND page_id <> '0' AND pos.page_id = lay.page_id)";
            $sqlval = array($url);
        }
        // 並び変え
        $objQuery->setOrder('target_id, bloc_row');
        
        // SQL実行
        $arrRet = $objQuery->select($col, $table, $where, $sqlval);
                                            
        // 結果を返す
        return $arrRet;
    }

    /**
     * 各部分のナビ情報を取得する.
     *
     * @param array $arrNavi ナビ情報の配列
     * @param integer|string $target_id ターゲットID
     * @return array ブロック情報の配列
     */
    function lfGetNavi($arrNavi, $target_id) {
        $arrRet = array();
        if(is_array($arrNavi) === true) {
        	reset($arrNavi);
            while( list($key,$val)= each($arrNavi) ){
            	// 指定された箇所と同じデータだけを取得する
                if ($target_id == $val['target_id']){
                    if ($val['php_path'] != '') {
                        $arrNavi[$key]['php_path'] = HTML_PATH . $val['php_path'];
                    }else{
                    	$user_block_path = USER_TEMPLATE_PATH . TEMPLATE_NAME . "/" . $val['tpl_path'];
                    	if(is_file($user_block_path)) {
                    	   $arrNavi[$key]['tpl_path'] = $user_block_path;
                    	} else {
                           $arrNavi[$key]['tpl_path'] = TEMPLATE_DIR . $val['tpl_path'];
                    	}
                    }
                    
                    // phpから呼び出されるか、tplファイルが存在する場合
                    if($val['php_path'] != '' || is_file($arrNavi[$key]['tpl_path'])) {
                        $arrRet[] = $arrNavi[$key];
                    } else {
                        GC_Utils::gfPrintLog("ブロック読み込みエラー：" . $arrNavi[$key]['tpl_path']);
                    }
                }
            }
        }
        return $arrRet;
    }

    /**
     * カラム数を取得する.
     * 
     * @param array $arrPageLayout レイアウト情報の配列
     * @return integer $col_num カラム数
     */
    function lfGetColumnNum($arrPageLayout) {
        // メインは確定
        $col_num = 1;
        // LEFT NAVI
        if (count($arrPageLayout['LeftNavi']) > 0) $col_num++;
        // RIGHT NAVI
        if (count($arrPageLayout['RightNavi']) > 0) $col_num++;
        
        return $col_num;
    }

    /**
     * ページ情報を削除する.
     *
     * @param integer|string $page_id ページID
     * @return integer 削除数
     */
    function lfDelPageData($page_id){
    	// DBへデータを更新する
        $objQuery = new SC_Query;		// DB操作オブジェクト
        $sql = "";						// データ更新SQL生成用
        $ret = ""; 						// データ更新結果格納用
        $arrDelData = array();			// 更新データ生成用

        // page_id が空でない場合にはdeleteを実行
        if ($page_id != '') {

            $arrPageData = $this->lfgetPageData(" page_id = ? " , array($page_id));
            // SQL実行
            $ret = $objQuery->delete("dtb_pagelayout", "page_id = ?", array($page_id));

            // ファイルの削除
            $this->lfDelFile($arrPageData[0]);
        }
        return $ret;
    }

    /**
     * ページのファイルを削除する.
     *
     * @param array $arrData ページ情報の配列
     * @return void // TODO boolean にするべき?
     */
    function lfDelFile($arrData){
        // ファイルディレクトリ取得
        $del_php = HTML_PATH . $arrData['php_dir'] . $arrData['filename'] . ".php";
        $del_tpl = HTML_PATH . $arrData['tpl_dir'] . $arrData['filename'] . ".tpl";

        // phpファイルの削除
        if (file_exists($del_php)){
            unlink($del_php);
        }

        // tplファイルの削除
        if (file_exists($del_tpl)){
            unlink($del_tpl);
        }
    }

    /**
     * データがベースデータかどうか.
     *
     * @param integer|string $data ページID
     * @return boolean ベースデータの場合 true
     */
    function lfCheckBaseData($data){
        $ret = false;

        if ($data == 0) {
            return $ret;
        }

        $arrChkData = $this->lfgetPageData("page_id = ?", array($data));

        if ($arrChkData[0]['edit_flg'] == 2){
            $ret = true;
        }

        return $ret;
    }
}
?>
