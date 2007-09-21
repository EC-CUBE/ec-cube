<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * デザイン管理 のページクラス.
 *
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
        $sel   = ", pos.target_id, pos.bloc_id, pos.bloc_row ";
        $from  = ", dtb_blocposition AS pos";
        $where = " where ";
        $where .= " lay.page_id = ? AND ";
        $where .= "lay.page_id = pos.page_id AND exists (select bloc_id from dtb_bloc as blc where pos.bloc_id = blc.bloc_id) ORDER BY lay.page_id,pos.target_id, pos.bloc_row, pos.bloc_id ";
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
        }

        // 新規ページ作成
        if ($_POST['mode'] == 'new_page') {
            $this->sendRedirect($this->getLocation("./main_edit.php"));
        }

        // データ登録処理
        if ($_POST['mode'] == 'confirm' or $_POST['mode'] == 'preview') {

            $arrPageData = array();
            if ($_POST['mode'] == 'preview') {
                $arrPageData = $objLayout->lfgetPageData(" page_id = ? " , array($page_id));
                $page_id = "0";
                $_POST['page_id'] = "0";
            }

            $masterData = new SC_DB_MasterData_Ex();
            $arrTarget = $masterData->getMasterData("mtb_target");

            // 更新用にデータを整える
            $arrUpdBlocData = array();
            $arrTargetFlip = array_flip($arrTarget);

            $upd_cnt = 1;
            $arrUpdData[$upd_cnt]['page_id'] = $_POST['page_id'];

            // POSTのデータを使いやすいように修正
            for($upd_cnt = 1; $upd_cnt <= $_POST['bloc_cnt']; $upd_cnt++){
                if (!isset($_POST['id_'.$upd_cnt])) {
                    break;
                }
                $arrUpdBlocData[$upd_cnt]['name']       = $_POST['name_'.$upd_cnt];                         // ブロック名称
                $arrUpdBlocData[$upd_cnt]['id']         = $_POST['id_'.$upd_cnt];                           // ブロックID
                $arrUpdBlocData[$upd_cnt]['target_id']  = $arrTargetFlip[$_POST['target_id_'.$upd_cnt]];    // ターゲットID
                $arrUpdBlocData[$upd_cnt]['top']        = $_POST['top_'.$upd_cnt];                          // TOP座標
                $arrUpdBlocData[$upd_cnt]['update_url'] = $_SERVER['HTTP_REFERER'];                         // 更新URL
            }

            // データの更新を行う
            $objDBConn = new SC_DbConn;     // DB操作オブジェクト
            $arrRet = array();              // データ取得用

            // delete実行
            $del_sql = "";
            $del_sql .= "DELETE FROM dtb_blocposition WHERE page_id = ? ";
            $arrRet = $objDBConn->query($del_sql,array($page_id));

            // ブロックの順序を取得し、更新を行う
            foreach($arrUpdBlocData as $key => $val){
                // ブロックの順序を取得
                $bloc_row = $this->lfGetRowID($arrUpdBlocData, $val);
                $arrUpdBlocData[$key]['bloc_row'] = $bloc_row;
                $arrUpdBlocData[$key]['page_id']    = $_POST['page_id'];    // ページID

                if ($arrUpdBlocData[$key]['target_id'] == 5) {
                    $arrUpdBlocData[$key]['bloc_row'] = "0";
                }

                // insert文生成
                $ins_sql = "";
                $ins_sql .= "INSERT INTO dtb_blocposition ";
                $ins_sql .= " values ( ";
                $ins_sql .= "   ?  ";           // ページID
                $ins_sql .= "   ,? ";           // ターゲットID
                $ins_sql .= "   ,? ";           // ブロックID
                $ins_sql .= "   ,? ";           // ブロックの並び順序
                $ins_sql .= "   ,(SELECT filename FROM dtb_bloc WHERE bloc_id = ?) ";           // ファイル名称
                $ins_sql .= "   )  ";

                // insertデータ生成
                $arrInsData = array($page_id,
                                    $arrUpdBlocData[$key]['target_id'],
                                    $arrUpdBlocData[$key]['id'],
                                    $arrUpdBlocData[$key]['bloc_row'],
                                    $arrUpdBlocData[$key]['id']
                                    );
                // SQL実行
                $arrRet = $objDBConn->query($ins_sql,$arrInsData);
            }

            // プレビュー処理
            if ($_POST['mode'] == 'preview') {
                if ($page_id === "") {
                    $this->sendRedirect($this->getLocation("./index.php"));
                }
                $this->lfSetPreData($arrPageData, $objLayout);

                $_SESSION['preview'] = "ON";
                $this->sendRedirect($this->getLocation(URL_DIR . "preview/index.php"));

            }else{
                $this->sendRedirect($this->getLocation("./index.php",
                                            array("page_id" => $page_id,
                                                  "msg" => "on")));

            }
        }

        // データ削除処理 ベースデータでなければファイルを削除
        if ($_POST['mode'] == 'delete' and  !$objLayout->lfCheckBaseData($page_id)) {
            $objLayout->lfDelPageData($page_id);
            $this->sendRedirect($this->getLocation("./index.php"));
        }

        // ブロック情報を画面配置用に編集
        $tpl_arrBloc = array();
        $cnt = 0;
        // 使用されているブロックデータを生成
        foreach($arrBlocPos as $key => $val){
            if ($val['page_id'] == $page_id) {
                $tpl_arrBloc = $this->lfSetBlocData($arrBloc, $val, $tpl_arrBloc, $cnt);
                $cnt++;
            }
        }

        // 未使用のブロックデータを追加
        foreach($arrBloc as $key => $val){
            if (!$this->lfChkBloc($val, $tpl_arrBloc)) {
                $val['target_id'] = 5;  // 未使用に追加する
                $tpl_arrBloc = $this->lfSetBlocData($arrBloc, $val, $tpl_arrBloc, $cnt);
                $cnt++;
            }
        }

        $this->tpl_arrBloc = $tpl_arrBloc;
        $this->bloc_cnt = count($tpl_arrBloc);
        $this->page_id = $page_id;

        // ページ名称を取得
        $arrPageData = $objLayout->lfgetPageData(' page_id = ?', array($page_id));
        $this->arrPageData = $arrPageData[0];

        global $GLOBAL_ERR; // FIXME
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
        $objDBConn = new SC_DbConn;     // DB操作オブジェクト
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

        $arrRet = $objDBConn->getAll($sql, $arrVal);

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
        $objDBConn = new SC_DbConn;     // DB操作オブジェクト
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

        $arrRet = $objDBConn->getAll($sql, $arrVal);

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
        $objDBConn = new SC_DbConn;     // DB操作オブジェクト
        $sql = "";                      // データ更新SQL生成用
        $ret = "";                      // データ更新結果格納用
        $arrUpdData = array();          // 更新データ生成用
        $filename = uniqid("");

        $arrPreData = $objLayout->lfgetPageData(" page_id = ? " , array("0"));

        // tplファイルの削除
        $del_tpl = USER_PATH . "templates/" . TEMPLATE_NAME . "/"
            . $arrPreData[0]['filename'] . '.tpl';

        if (file_exists($del_tpl)){
            unlink($del_tpl);
        }

        // プレビュー用tplファイルのコピー
        $tplfile = $arrPageData[0]['tpl_dir'] . $arrPageData[0]['filename'];

        if($tplfile == ""){
            // tplファイルが空の場合にはMYページと判断
            $tplfile = "user_data/templates/mypage/index";
        }
        copy(HTML_PATH . $tplfile . ".tpl", USER_PATH . "templates/"
             . TEMPLATE_NAME . "/" . $filename . ".tpl");

        // 更新データの取得
        $sql = "select page_name, header_chk, footer_chk from dtb_pagelayout where page_id = ?";
        $ret = $objDBConn->getAll($sql, array($arrPageData[0]['page_id']));

        // dbデータのコピー
        $sql = " update dtb_pagelayout set ";
        $sql .= "     page_name = ?";
        $sql .= "     ,header_chk = ?";
        $sql .= "     ,footer_chk = ?";
        $sql .= "     ,url = ?";
        $sql .= "     ,tpl_dir = ?";
        $sql .= "     ,filename = ?";
        $sql .= " where page_id = 0";

        $arrUpdData = array($ret[0]['page_id']
                            ,$ret[0]['page_id']
                            ,$ret[0]['page_id']
                            ,USER_DIR . "templates/" . TEMPLATE_NAME . "/"
                            ,USER_DIR . "templates/" . TEMPLATE_NAME . "/"
                            ,$filename
                            );

        $objDBConn->query($sql,$arrUpdData);
    }
}
?>
