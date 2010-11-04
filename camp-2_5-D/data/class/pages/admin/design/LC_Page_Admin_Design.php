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

/** ターゲットID 未使用 */
define('TARGET_ID_UNUSED', 0);

/**
 * デザイン管理 のページクラス.
 *
 * ターゲットID 0:未使用 1:レフトナビ 2:ライトナビ 3:イン画面上部 4:メイン画面下部  5:画面上部 6:画面下部 7:ヘッダより上 8:フッタより下 9:HEADタグ内
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Design extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'design/index.tpl';
        $this->tpl_subnavi = 'design/subnavi.tpl';
        $this->tpl_subno = "layout";
        $this->tpl_mainno = "design";
        $this->tpl_subtitle = 'レイアウト編集';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {

        $objView = new SC_AdminView();
        $objSess = new SC_Session();
        $objLayout = new SC_Helper_PageLayout_Ex();

        // 認証可否の判定
        $objSess = new SC_Session();
        SC_Utils_Ex::sfIsSuccess($objSess);

        // ページIDを取得
        if (isset($_GET['page_id'])) {
            $page_id = $_GET['page_id'];
        }else if (isset($_POST['page_id'])){
            $page_id = $_POST['page_id'];
        }else{
            $page_id = 1;
        }

        // 編集可能ページを取得
        $this->arrEditPage = $objLayout->lfgetPageData();

        // ブロック配置用データを取得
        $sel   = ", pos.target_id, pos.bloc_id, pos.bloc_row ,pos.anywhere";
        $from  = ", dtb_blocposition AS pos";
        $where = " where ";
        $where .= "( pos.anywhere = 1 OR (lay.page_id = ? AND ";
        $where .= "lay.page_id = pos.page_id AND exists (select bloc_id from dtb_bloc as blc where pos.bloc_id = blc.bloc_id) )) ORDER BY lay.page_id,pos.target_id, pos.bloc_row, pos.bloc_id ";
        //        $where .= "((lay.page_id = ? AND ";
        //        $where .= "lay.page_id = pos.page_id AND exists (select bloc_id from dtb_bloc as blc where pos.bloc_id = blc.bloc_id) )) ORDER BY lay.page_id,pos.target_id, pos.bloc_row, pos.bloc_id ";

        $arrData = array($page_id);
        $arrBlocPos = $this->lfgetLayoutData($sel, $from, $where, $arrData );

        // データの存在チェックを行う
        $arrPageData = $objLayout->lfgetPageData("page_id = ?", array($page_id));
        if (count($arrPageData) <= 0) {
            $exists_page = 0;
        }else{
            $exists_page = 1;
        }
        $this->exists_page = $exists_page;

        // メッセージ表示
        if (isset($_GET['msg']) && $_GET['msg'] == "on") {
            $this->complate_msg="alert('登録が完了しました。');";
        }

        // ブロックを取得
        $arrBloc = $this->lfgetBlocData();

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        // 新規ブロック作成
        if ($_POST['mode'] == 'new_bloc') {
            $this->sendRedirect($this->getLocation("./bloc.php"));
            exit;
        }

        // 新規ページ作成
        if ($_POST['mode'] == 'new_page') {
            $this->sendRedirect($this->getLocation("./main_edit.php"));
            exit;
        }

        // データ登録処理
        if ($_POST['mode'] == 'confirm' or $_POST['mode'] == 'preview') {
            $page_id = $_POST['page_id'];
            if ($_POST['mode'] == 'preview') {
                $page_id = '0';
            }
            $masterData = new SC_DB_MasterData_Ex();
            $arrTarget = $masterData->getMasterData("mtb_target");

            // 更新用にデータを整える
            $arrUpdBlocData = array();
            $arrTargetFlip = array_flip($arrTarget);

            $upd_cnt = 1;
            $arrUpdData[$upd_cnt]['page_id'] = $page_id;

            // POSTのデータを使いやすいように修正
            for($upd_cnt = 1; $upd_cnt <= $_POST['bloc_cnt']; $upd_cnt++){
                if (!isset($_POST['id_'.$upd_cnt])) {
                    break;
                }
                $arrUpdBlocData[$upd_cnt]['name']       = $_POST['name_'.$upd_cnt];                         // ブロック名称
                $arrUpdBlocData[$upd_cnt]['id']         = $_POST['id_'.$upd_cnt];                           // ブロックID
                $arrUpdBlocData[$upd_cnt]['target_id']  = $arrTargetFlip[$_POST['target_id_'.$upd_cnt]];    // ターゲットID
                $arrUpdBlocData[$upd_cnt]['top']        = $_POST['top_'.$upd_cnt];                          // TOP座標
                $arrUpdBlocData[$upd_cnt]['anywhere']   = $_POST['anywhere_'.$upd_cnt];                     // 全ページ適用か
                $arrUpdBlocData[$upd_cnt]['update_url'] = $_SERVER['HTTP_REFERER'];                         // 更新URL

            }

            // データの更新を行う
            $objQuery = new SC_Query();     // DB操作オブジェクト
            $arrRet = array();              // データ取得用

            // delete実行
            $del_sql = "";
            $del_sql .= "DELETE FROM dtb_blocposition WHERE page_id = ? ";
            $arrRet = $objQuery->query($del_sql,array($page_id));

            // ブロックの順序を取得し、更新を行う
            foreach($arrUpdBlocData as $key => $val){
                if ($arrUpdBlocData[$key]['target_id'] == TARGET_ID_UNUSED) {
                    continue;
                }

                // ブロックの順序を取得
                $bloc_row = $this->lfGetRowID($arrUpdBlocData, $val);
                $arrUpdBlocData[$key]['bloc_row'] = $bloc_row;
                $arrUpdBlocData[$key]['page_id']    =  $page_id;    // ページID

                // insert文生成
                $ins_sql = "";
                $ins_sql .= "INSERT INTO dtb_blocposition ";
                $ins_sql .= " values ( ";
                $ins_sql .= "   ?  ";           // ページID
                $ins_sql .= "   ,? ";           // ターゲットID
                $ins_sql .= "   ,? ";           // ブロックID
                $ins_sql .= "   ,? ";           // ブロックの並び順序
                $ins_sql .= "   ,(SELECT filename FROM dtb_bloc WHERE bloc_id = ?) ";           // ファイル名称
                $ins_sql .= "   ,? ";           // 全ページフラグ
                $ins_sql .= "   )  ";

                // insertデータ生成
                $arrInsData = array($page_id,
                    $arrUpdBlocData[$key]['target_id'],
                    $arrUpdBlocData[$key]['id'],
                    $arrUpdBlocData[$key]['bloc_row'],
                    $arrUpdBlocData[$key]['id'],
                    $arrUpdBlocData[$key]['anywhere'] ? 1 : 0
                );
                $count = $objQuery->getOne("SELECT COUNT(*) FROM dtb_blocposition WHERE anywhere = 1 AND bloc_id = ?",array($arrUpdBlocData[$key]['id']));

                if($arrUpdBlocData[$key]['anywhere'] == 1){
                    $count = $objQuery->getOne("SELECT COUNT(*) FROM dtb_blocposition WHERE anywhere = 1 AND bloc_id = ?",array($arrUpdBlocData[$key]['id']));
                    if($count != 0){
                        continue;
                    }else{
                    }
                }else{
                    if($count > 0){
                        $objQuery->query("DELETE FROM dtb_blocposition WHERE anywhere = 1 AND bloc_id = ?",array($arrUpdBlocData[$key]['id']));
                    }
                }
                // SQL実行
                $arrRet = $objQuery->query($ins_sql,$arrInsData);
            }

            // プレビュー処理
            if ($_POST['mode'] == 'preview') {
                if ($page_id === "") {
                    $this->sendRedirect($this->getLocation(DIR_INDEX_URL));
                    exit;
                }
                $this->lfSetPreData($arrPageData, $objLayout);

                $_SESSION['preview'] = "ON";

                $this->sendRedirect($this->getLocation(URL_DIR . "preview/" . DIR_INDEX_URL, array("filename" => $arrPageData[0]["filename"])));
                exit;

            }else{
                $this->sendRedirect($this->getLocation(DIR_INDEX_URL,
                array("page_id" => $page_id,
                                                  "msg" => "on")));
                exit;

            }
        }

        // データ削除処理 ベースデータでなければファイルを削除
        if ($_POST['mode'] == 'delete' and  !$objLayout->lfCheckBaseData($page_id)) {
            $objLayout->lfDelPageData($page_id);
            $this->sendRedirect($this->getLocation(DIR_INDEX_URL));
            exit;
        }

        // ブロック情報を画面配置用に編集
        $tpl_arrBloc = array();
        $cnt = 0;
        // 使用されているブロックデータを生成
        foreach($arrBlocPos as $key => $val){
            if ($val['page_id'] == $page_id) {
                $tpl_arrBloc = $this->lfSetBlocData($arrBloc, $val, $tpl_arrBloc, $cnt);
                $cnt++;
            }else{
            }
        }

        // 未使用のブロックデータを追加
        foreach($arrBloc as $key => $val){
            if (!$this->lfChkBloc($val, $tpl_arrBloc)) {
                $val['target_id'] = TARGET_ID_UNUSED; // 未使用に追加する
                $tpl_arrBloc = $this->lfSetBlocData($arrBloc, $val, $tpl_arrBloc, $cnt);
                $cnt++;
            }else{
            }
        }

        $this->tpl_arrBloc = $tpl_arrBloc;
        $this->bloc_cnt = count($tpl_arrBloc);
        $this->page_id = $page_id;

        // ページ名称を取得
        $arrPageData = $objLayout->lfgetPageData(' page_id = ?', array($page_id));
        $this->arrPageData = $arrPageData[0];

        global $GLOBAL_ERR;
        $errCnt = 0;
        if ($GLOBAL_ERR != "") {
            $arrGlobalErr = explode("\n",$GLOBAL_ERR);
            $errCnt = count($arrGlobalErr) - 8;
            if ($errCnt < 0 ) {
                $errCnt = 0;
            }
        }
        $this->errCnt = $errCnt;

        // 画面の表示
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
     * 編集可能なページ情報を取得する.
     *
     * @param string $sel Select句文
     * @param string $where Where句文
     * @param array $arrVa Where句の絞込条件値
     * @return array ページレイアウト情報の配列
     */
    function lfgetLayoutData($sel = '' , $from = '', $where = '', $arrVal = ''){
        $objQuery = new SC_Query();     // DB操作オブジェクト
        $sql = "";                      // データ取得SQL生成用
        $arrRet = array();              // データ取得用

        // SQL生成

        $sql = "";
        $sql .= " select ";
        $sql .= "     lay.page_id ";
        $sql .= "     ,lay.page_name ";
        $sql .= "     ,lay.url ";
        $sql .= "     ,lay.author ";
        $sql .= "     ,lay.description ";
        $sql .= "     ,lay.keyword ";
        $sql .= "     ,lay.update_url ";
        $sql .= "     ,lay.create_date ";
        $sql .= "     ,lay.update_date ";

        // Select句の指定があれば追加
        if ($sel != '') {
            $sql .= $sel;
        }

        $sql .= " from dtb_pagelayout AS lay ";
        // From句の指定があれば追加
        if ($from != '') {
            $sql .= $from;
        }

        // where句の指定があれば追加
        if ($where != '') {
            $sql .= $where;
        }else{
            $sql .= " ORDER BY lay.page_id ";
        }

        $arrRet = $objQuery->getAll($sql, $arrVal);

        return $arrRet;
    }

    /**
     * ブロック情報を取得する.
     *
     * @param string $where Where句文
     * @param array $arrVal Where句の絞込条件値
     * @return array ブロック情報の配列
     */
    function lfgetBlocData($where = '', $arrVal = ''){
        $objQuery = new SC_Query();     // DB操作オブジェクト
        $sql = "";                      // データ取得SQL生成用
        $arrRet = array();              // データ取得用

        // SQL生成
        $sql = "";
        $sql .= " SELECT ";
        $sql .= "   bloc_id";
        $sql .= "   ,bloc_name";
        $sql .= "   ,tpl_path";
        $sql .= "   ,filename";
        $sql .= "   ,update_date";
        $sql .= " FROM ";
        $sql .= "   dtb_bloc";

        // where句の指定があれば追加
        if ($where != '') {
            $sql .= " WHERE " . $where;
        }

        $sql .= " ORDER BY  bloc_id";

        $arrRet = $objQuery->getAll($sql, $arrVal);

        return $arrRet;
    }

    /**
     * ブロック情報の配列を生成する.
     *
     * @param array $arrBloc Bloc情報
     * @param array $tpl_arrBloc データをセットする配列
     * @param integer $cnt 配列番号
     * @return array データをセットした配列
     */
    function lfSetBlocData($arrBloc, $val, $tpl_arrBloc, $cnt) {
        $masterData = new SC_DB_MasterData_Ex();
        $arrTarget = $masterData->getMasterData("mtb_target");

        $tpl_arrBloc[$cnt]['target_id'] = $arrTarget[$val['target_id']];
        $tpl_arrBloc[$cnt]['bloc_id'] = $val['bloc_id'];
        $tpl_arrBloc[$cnt]['bloc_row'] =
        isset($val['bloc_row']) ? $val['bloc_row'] : "";
        $tpl_arrBloc[$cnt]['anywhere'] = $val['anywhere'];
        if($val['anywhere'] == 1){
            $tpl_arrBloc[$cnt]['anywhere_selected'] = 'checked="checked"';
        }
        foreach($arrBloc as $bloc_key => $bloc_val){
            if ($bloc_val['bloc_id'] == $val['bloc_id']) {
                $bloc_name = $bloc_val['bloc_name'];
                break;
            }
        }
        $tpl_arrBloc[$cnt]['name'] = $bloc_name;

        return $tpl_arrBloc;
    }

    /**
     * ブロックIDが配列に追加されているかのチェックを行う.
     *
     * @param array $arrBloc Bloc情報
     * @param array $arrChkData チェックを行うデータ配列
     * @return bool 存在する場合 true
     */
    function lfChkBloc($arrBloc, $arrChkData) {
        foreach($arrChkData as $key => $val){
            if ($val['bloc_id'] === $arrBloc['bloc_id'] ) {
                // 配列に存在すればTrueを返す
                return true;
            }
        }

        // 配列に存在しなければFlaseを返す
        return false;
    }

    /**
     * ブロックIDが何番目に配置されているかを調べる.
     *
     * @param array $arrUpdData 更新情報
     * @param array $arrObj チェックを行うデータ配列
     * @return integer 順番
     */
    function lfGetRowID($arrUpdData, $arrObj){
        $no = 0; // カウント用（同じデータが必ず1件あるので、初期値は0）

        // 対象データが何番目に配置されているのかを取得する。
        foreach ($arrUpdData as $key => $val) {
            if ($val['target_id'] === $arrObj['target_id'] and $val['top'] <= $arrObj['top']){
                $no++;
            }
        }
        // 番号を返す
        return $no;
    }

    /**
     * プレビューするデータを DB に保存する.
     *
     * @param array $arrPageData ページ情報の配列
     * @return void
     */
    function lfSetPreData($arrPageData, &$objLayout){
        $objQuery = new SC_Query();     // DB操作オブジェクト
        $sql = "";                      // データ更新SQL生成用
        $ret = "";                      // データ更新結果格納用
        $arrUpdData = array();          // 更新データ生成用
        $filename = $arrPageData[0]['filename'];

        $arrPreData = $objLayout->lfgetPageData(" page_id = ? " , array("0"));

        // XXX tplファイルの削除
        $del_tpl = USER_PATH . "templates/" . $filename . '.tpl';

        if (file_exists($del_tpl)){
            unlink($del_tpl);
        }

        // filename が空の場合にはMYページと判断
        if($filename == ""){
            $tplfile = TEMPLATE_DIR . "mypage/index";
            $filename = 'mypage';
        } else {
            if (file_exists(TEMPLATE_FTP_DIR . $filename . ".tpl")) {
                $tplfile = TEMPLATE_FTP_DIR . $filename;
            } else {
                $tplfile = TEMPLATE_DIR . $filename;
            }
        }

        // プレビュー用tplファイルのコピー
        $copyTo = USER_PATH . "templates/preview/" . TEMPLATE_NAME . "/" . $filename . ".tpl";

        if (!is_dir(dirname($copyTo))) {
            mkdir(dirname($copyTo));
        }

        copy($tplfile . ".tpl", $copyTo);

        // 更新データの取得
        $sql = "select page_id,page_name, header_chk, footer_chk from dtb_pagelayout where page_id = ? OR page_id = (SELECT page_id FROM dtb_blocposition WHERE anywhere = 1)" ;
        
        $ret = $objQuery->getAll($sql, array($arrPageData[0]['page_id']));

        // dbデータのコピー
        $sql = " update dtb_pagelayout set ";
        $sql .= "     page_name = ?";
        $sql .= "     ,header_chk = ?";
        $sql .= "     ,footer_chk = ?";
        $sql .= "     ,url = ?";
        $sql .= "     ,tpl_dir = ?";
        $sql .= "     ,filename = ?";
//      $sql .= "     ,anywhere = ?";
        $sql .= " where page_id = 0";
        var_dump($ret);
                echo("####<br/>\n\n".__LINE__ ." in file:".__FILE__."<br/>\n\n ####");

        $arrUpdData = array($ret[0]['page_id']
        ,$ret[0]['page_id']
        ,$ret[0]['page_id']
        ,USER_DIR . "templates/" . TEMPLATE_NAME . "/"
        ,USER_DIR . "templates/" . TEMPLATE_NAME . "/"
        ,$filename
//      ,$ret[0]['anywhere']
         
        );

        $objQuery->query($sql,$arrUpdData);
    }
}
?>
