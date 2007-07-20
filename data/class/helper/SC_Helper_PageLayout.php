<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/**
 * Webページのレイアウト情報を制御するヘルパークラス.
 *
 * @package Helper
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class SC_Helper_PageLayout {

    // }}}
    // {{{ functions

    /**
     * ページのレイアウト情報をセットする.
     *
     * LC_Page オブジェクトにページのレイアウト情報をセットして返す.
     *
     * @param LC_Page $objPage ページ情報
     * @param boolean $preview プレビュー表示の場合 true
     * @param string $url ページのURL
     * @return LC_Page ページのレイアウト情報
     */
    function sfGetPageLayout($objPage, $preview = false, $url = ""){
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
        }

        // メインテンプレートファイルを設定
        if (!isset($objPage->tpl_mainpage)) {
            $objPage->tpl_mainpage = HTML_PATH . $arrPageData[0]['tpl_dir'] . $arrPageData[0]['filename'] . ".tpl";
        }

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

        $objPage->arrPageLayout = $arrPageLayout;

        return $objPage;
    }

    /**
     * ページ情報を取得する.
     *
     * @param string $where クエリのWHERE句
     * @param array $arrVal WHERE句の条件値
     * @return array ページ情報を格納した配列
     */
    function lfgetPageData($where = '', $arrVal = ''){
        $objDBConn = new SC_DbConn;		// DB操作オブジェクト
        $sql = "";						// データ取得SQL生成用
        $arrRet = array();				// データ取得用

        // SQL生成
        $sql .= " SELECT";
        $sql .= " page_id";				// ページID
        $sql .= " ,page_name";			// 名称
        $sql .= " ,url";				// URL
        $sql .= " ,php_dir";			// php保存先ディレクトリ
        $sql .= " ,tpl_dir";			// tpl保存先ディdレクトリ
        $sql .= " ,filename";			// ファイル名称
        $sql .= " ,header_chk ";		// ヘッダー使用FLG
        $sql .= " ,footer_chk ";		// フッター使用FLG
        $sql .= " ,edit_flg ";			// 編集可能FLG
        $sql .= " ,author";				// authorタグ
        $sql .= " ,description";		// descriptionタグ
        $sql .= " ,keyword";			// keywordタグ
        $sql .= " ,update_url";			// 更新URL
        $sql .= " ,create_date";		// データ作成日
        $sql .= " ,update_date";		// データ更新日
        $sql .= " FROM ";
        $sql .= "     dtb_pagelayout";
        $sql .= " WHERE ";

        // where句の指定があれば追加
        if ($where != '') {
            $sql .= $where;
        }else{
            $sql .= "     page_id != 0 ";
        }

        $sql .= " ORDER BY 	page_id";

        $arrRet = $objDBConn->getAll($sql, $arrVal);

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
        $objDBConn = new SC_DbConn;		// DB操作オブジェクト
        $sql = "";						// データ取得SQL生成用
        $arrRet = array();				// データ取得用
        $arrData = array();

        // SQL文生成
        $sql = "";
        $sql .= " SELECT ";
        $sql .= "     target_id ";
        $sql .= "     ,(SELECT bloc_name FROM dtb_bloc AS bloc WHERE bloc.bloc_id = pos.bloc_id) AS bloc_name";
        $sql .= "     ,(SELECT tpl_path FROM dtb_bloc AS bloc WHERE bloc.bloc_id = pos.bloc_id) AS tpl_path";
        $sql .= "     ,(SELECT php_path FROM dtb_bloc AS bloc WHERE bloc.bloc_id = pos.bloc_id) AS php_path";
        $sql .= " FROM";
        $sql .= "     dtb_blocposition AS pos";
        $sql .= " WHERE";
        if ($preview == true) {
            $sql .= "     page_id = (SELECT page_id FROM dtb_pagelayout WHERE page_id = '0')";
        }else{
            $sql .= "     page_id = (SELECT page_id FROM dtb_pagelayout WHERE url = ?)";
            $arrData = array($url);
        }
        $sql .= " ORDER BY target_id,bloc_row";
        $sql .= " ";

        // SQL実行
        $arrRet = $objDBConn->getAll($sql, $arrData);

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
        if(is_array($arrNavi)) {
            foreach($arrNavi as $key => $val){
                // 指定された箇所と同じデータだけを取得する
                if ($target_id == $val['target_id']){
                    if ($val['php_path'] != '') {
                        $arrNavi[$key]['php_path'] = HTML_PATH . $val['php_path'];
                        $arrNavi[$key]['include'] = "<!--{include file='".$val['php_path']."'}-->";
                    }else{
                        $arrNavi[$key]['tpl_path'] = USER_PATH . $val['tpl_path'];
                        $arrNavi[$key]['include'] = "<!--{include file='". USER_PATH . $val['tpl_path'] ."'}-->";
                    }

                    $arrRet[] = $arrNavi[$key];
                }
            }
        }
        return $arrRet;
    }

    /**
     * ページ情報を削除する.
     *
     * @param integer|string $page_id ページID
     * @return integer 削除数
     */
    function lfDelPageData($page_id){
        // DBへデータを更新する
        $objDBConn = new SC_DbConn;		// DB操作オブジェクト
        $sql = "";						// データ更新SQL生成用
        $ret = ""; 						// データ更新結果格納用
        $arrDelData = array();			// 更新データ生成用

        // page_id が空でない場合にはdeleteを実行
        if ($page_id !== '') {
            // SQL生成
            $sql = " DELETE FROM dtb_pagelayout WHERE page_id = ?";

            // SQL実行
            $ret = $objDBConn->query($sql,array($page_id));

            // ファイルの削除
            lfDelFile($arrPageData[0]);
        }

        // FIXME 削除数を返し, 遷移は Page クラスで行う
        header("location: ".$_SERVER['REQUEST_URI']);

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
