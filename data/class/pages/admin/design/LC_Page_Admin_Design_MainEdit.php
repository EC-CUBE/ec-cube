<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * メイン編集 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Design_MainEdit extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
		$this->tpl_mainpage = 'design/main_edit.tpl';
		$this->tpl_subnavi 	= 'design/subnavi.tpl';
		$this->user_URL	 	= USER_URL;
		$this->text_row 	= 13;
		$this->tpl_subno = "main_edit";
		$this->tpl_mainno = "design";
		$this->tpl_subtitle = 'ページ詳細設定';

    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objView = new SC_AdminView();
        $this->objLayout = new SC_Helper_PageLayout_Ex();

        // 認証可否の判定
        $objSess = new SC_Session();
        SC_Utils_Ex::sfIsSuccess($objSess);

        // ページ一覧を取得
        $this->arrPageList = $this->objLayout->lfgetPageData();

        // ブロックIDを取得
        if (isset($_POST['page_id'])) {
            $page_id = $_POST['page_id'];
        }else if (isset($_GET['page_id'])){
            $page_id = $_GET['page_id'];
        }else{
            $page_id = '';
        }

        $this->page_id = $page_id;

        // メッセージ表示
        if (isset($_GET['msg']) && $_GET['msg'] == "on"){
            $this->tpl_onload="alert('登録が完了しました。');";
        }

        // page_id が指定されている場合にはテンプレートデータの取得
        if (is_numeric($page_id) and $page_id != '') {
            $arrPageData = $this->objLayout->lfgetPageData(" page_id = ? " , array($page_id));

            if ($arrPageData[0]['tpl_dir'] === "") {
                $this->arrErr['page_id_err'] = "※ 指定されたページは編集できません。";
                // 画面の表示
                $objView->assignobj($this);
                $objView->display(MAIN_FRAME);
                exit;
            }

            // テンプレートファイルが存在していれば読み込む
            $tpl_file = HTML_PATH . $arrPageData[0]['tpl_dir'] . $arrPageData[0]['filename'] . ".tpl";
            if (file_exists($tpl_file)){
                $arrPageData[0]['tpl_data'] = file_get_contents($tpl_file);
            }

            // チェックボックスの値変更
            $arrPageData[0]['header_chk'] = SC_Utils_Ex::sfChangeCheckBox($arrPageData[0]['header_chk'], true);
            $arrPageData[0]['footer_chk'] = SC_Utils_Ex::sfChangeCheckBox($arrPageData[0]['footer_chk'], true);

            // ディレクトリを画面表示用に編集
            $arrPageData[0]['directory'] = str_replace( USER_DIR,'', $arrPageData[0]['php_dir']);

            $this->arrPageData = $arrPageData[0];
        }

        // プレビュー処理
        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        if ($_POST['mode'] == 'preview') {

            $page_id_old = $page_id;
            $page_id = "0";
            $url = uniqid("");

            $_POST['page_id'] = $page_id;
            $_POST['url'] = $url;

            $arrPreData = $this->objLayout->lfgetPageData(" page_id = ? " , array($page_id));

            // tplファイルの削除
            $del_tpl = USER_PATH . "templates/" . $arrPreData[0]['filename'] . '.tpl';
            if (file_exists($del_tpl)){
                unlink($del_tpl);
            }

            // DBへデータを更新する
            $this->lfEntryPageData($_POST);

            // TPLファイル作成
            $cre_tpl = USER_PATH . "templates/" . $url . '.tpl';
            $this->lfCreateFile($cre_tpl);

            // blocposition を削除
            $objDBConn = new SC_DbConn;		// DB操作オブジェクト
            $sql = 'delete from dtb_blocposition where page_id = 0';
            $ret = $objDBConn->query($sql);

            if ($page_id_old != "") {
                // 登録データを取得
                $sql = "SELECT 0, target_id, bloc_id, bloc_row FROM dtb_blocposition WHERE page_id = ?";
                $ret = $objDBConn->getAll($sql,array($page_id_old));

                if (count($ret) > 0) {

                    // blocposition を複製
                    $sql = " insert into dtb_blocposition (";
                    $sql .= "     page_id,";
                    $sql .= "     target_id,";
                    $sql .= "     bloc_id,";
                    $sql .= "     bloc_row";
                    $sql .= "     )values(?, ?, ?, ?)";

                    // 取得件数文INSERT実行
                    foreach($ret as $key => $val){
                        $ret = $objDBConn->query($sql,$val);
                    }
                }

            }

            $_SESSION['preview'] = "ON";
            $this->sendRedirect($this->getLocation(URL_DIR . "preview/index.php"));
        }

        // データ登録処理
        if ($_POST['mode'] == 'confirm') {

            // エラーチェック
            $this->arrErr = $this->lfErrorCheck($_POST);

            // エラーがなければ更新処理を行う
            if (count($this->arrErr) == 0) {

                // DBへデータを更新する
                $this->lfEntryPageData($_POST);

                // ベースデータでなければファイルを削除し、PHPファイルを作成する
                if (!$this->objLayout->lfCheckBaseData($page_id)) {
                    // ファイル削除
                    $this->objLayout->lfDelFile($arrPageData[0]);

                    // PHPファイル作成
                    $cre_php = USER_PATH . $_POST['url'] . ".php";
                    $this->lfCreatePHPFile($cre_php);
                }

                // TPLファイル作成
                $cre_tpl = dirname(USER_PATH . "templates/" . $_POST['url']) . "/" . basename($_POST['url']) . '.tpl';

                $this->lfCreateFile($cre_tpl);

                // 編集可能ページの場合にのみ処理を行う
                if ($arrPageData[0]['edit_flg'] != 2) {
                    // 新規作成した場合のために改にページIDを取得する
                    $arrPageData = $this->objLayout->lfgetPageData(" url = ? " , array(USER_URL.$_POST['url'].".php"));
                    $page_id = $arrPageData[0]['page_id'];
                }
                $this->sendRedirect($this->getLocation("./main_edit.php",
                                        array("page_id" => $page_id,
                                              "msg"     => "on")));
            }else{
                // エラーがあれば入力時のデータを表示する
                $this->arrPageData = $_POST;
                $this->arrPageData['header_chk'] = SC_Utils_Ex::sfChangeCheckBox(SC_Utils_Ex::sfChangeCheckBox($_POST['header_chk']), true);
                $this->arrPageData['footer_chk'] = SC_Utils_Ex::sfChangeCheckBox(SC_Utils_Ex::sfChangeCheckBox($_POST['footer_chk']), true);
                $this->arrPageData['directory'] = $_POST['url'];
                $this->arrPageData['filename'] = "";
            }
        }

        // データ削除処理 ベースデータでなければファイルを削除
        if ($_POST['mode'] == 'delete' and 	!$this->objLayout->lfCheckBaseData($page_id)) {
            $this->objLayout->lfDelPageData($_POST['page_id']);
            $this->sendRedirect($this->getLocation("./main_edit.php"));
        }

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
     * ブロック情報を更新する.
     *
     * @param array $arrData 更新データ
     * @return integer 更新結果
     */
    function lfEntryPageData($arrData){
        $objDBConn = new SC_DbConn;		// DB操作オブジェクト
        $sql = "";						// データ更新SQL生成用
        $ret = ""; 						// データ更新結果格納用
        $arrUpdData = array();			// 更新データ生成用
        $arrChk = array();				// 排他チェック用

        // 更新データ生成
        $arrUpdData = $this->lfGetUpdData($arrData);

        // データが存在しているかチェックを行う
        if($arrData['page_id'] !== ''){
            $arrChk = $this->objLayout->lfgetPageData(" page_id = ?", array($arrData['page_id']));
        }

        // page_id が空 若しくは データが存在していない場合にはINSERTを行う
        if ($arrData['page_id'] === '' or !isset($arrChk[0])) {
            // SQL生成
            $sql = " INSERT INTO dtb_pagelayout ";
            $sql .= " ( ";
            $sql .= " 	  page_name";
            $sql .= "	  ,url";
            $sql .= "	  ,php_dir";
            $sql .= "	  ,tpl_dir";
            $sql .= "	  ,filename";
            $sql .= "	  ,header_chk";
            $sql .= "	  ,footer_chk";
            $sql .= "	  ,update_url";
            $sql .= "	  ,create_date";
            $sql .= "	  ,update_date";
            $sql .= " ) VALUES ( ?,?,?,?,?,?,?,?,now(),now() )";
            $sql .= " ";
        }else{
            // データが存在してる場合にはアップデートを行う
            // SQL生成
            $sql = " UPDATE dtb_pagelayout ";
            $sql .= " SET";
            $sql .= "	  page_name = ? ";
            $sql .= "	  ,url = ? ";
            $sql .= "	  ,php_dir = ? ";
            $sql .= "	  ,tpl_dir = ? ";
            $sql .= "	  ,filename = ? ";
            $sql .= "	  ,header_chk = ? ";
            $sql .= "	  ,footer_chk = ? ";
            $sql .= "	  ,update_url = ? ";
            $sql .= "     ,update_date = now() ";
            $sql .= " WHERE page_id = ?";
            $sql .= " ";

            // 更新データにブロックIDを追加
            array_push($arrUpdData, $arrData['page_id']);
        }

        // SQL実行
        $ret = $objDBConn->query($sql,$arrUpdData);

        return $ret;
    }

    /**
     * DBへ更新を行うデータを生成する.
     *
     * @param array $arrData 更新データ
     * @return array 更新データ
     */
    function lfGetUpdData($arrData){

        // ベースデータの場合には変更しない。
        if ($this->objLayout->lfCheckBaseData($arrData['page_id'])) {
            $arrPageData = $this->objLayout->lfgetPageData( ' page_id = ? ' , array($arrData['page_id']));

            $name = $arrPageData[0]['page_name'] ;
            $url = $arrPageData[0]['url'];
            $php_dir = $arrPageData[0]['php_dir'];
            $tpl_dir = $arrPageData[0]['tpl_dir'];
            $filename = $arrPageData[0]['filename'];
        }else{
            $name = $arrData['page_name'] ;
            $url = USER_URL.$arrData['url'].".php";
            $php_dir = dirname(USER_DIR.$arrData['url'])."/";
            $tpl_dir = dirname(USER_DIR."templates/".$arrData['url'])."/";
            $filename = basename($arrData['url']);
        }

        // 更新データ配列の作成
        $arrUpdData = array(
                            $name										// 名称
                            ,$url										// URL
                            ,$php_dir									// PHPディレクトリ
                            ,$tpl_dir									// TPLディレクトリ
                            ,$filename									// ファイル名
                            ,SC_Utils_Ex::sfChangeCheckBox($arrData['header_chk'])	// ヘッダー使用
                            ,SC_Utils_Ex::sfChangeCheckBox($arrData['footer_chk'])	// フッター使用
                            ,$_SERVER['HTTP_REFERER']					// 更新URL
                            );

        return $arrUpdData;
    }

    /**
     * 入力項目のエラーチェックを行う.
     *
     * @param array $arrData 入力データ
     * @return array エラー情報
     */
    function lfErrorCheck($array) {
        $objErr = new SC_CheckError($array);
        $objErr->doFunc(array("名称", "page_name", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("URL", "url", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));

        // URLチェック
        if (substr(strrev(trim($array['url'])),0,1) == "/") {
            $objErr->arrErr['url'] = "※ URLを正しく入力してください。<br />";
        }

        $check_url = USER_URL . $array['url'] . ".php";
        if( strlen($array['url']) > 0 && !ereg( "^https?://+($|[a-zA-Z0-9:_~=&\?\.\/-])+$", $check_url ) ) {
            $objErr->arrErr['url'] = "※ URLを正しく入力してください。<br />";
        }

        // 同一のURLが存在している場合にはエラー
        if(!isset($objErr->arrErr['url']) and $array['url'] !== ''){
            $arrChk = $this->objLayout->lfgetPageData(" url = ? " , array(USER_URL . $array['url'].".php"));

            if (count($arrChk[0]) >= 1 and $arrChk[0]['page_id'] != $array['page_id']) {
                $objErr->arrErr['url'] = '※ 同じURLのデータが存在しています。別のURLを付けてください。';
            }
        }

        return $objErr->arrErr;
    }

    /**
     * ファイルを作成する.
     *
     * @param string $path テンプレートファイルのパス
     * @return void
     */
    function lfCreateFile($path){

        // ディレクトリが存在していなければ作成する
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path));
        }

        // ファイル作成
        $fp = fopen($path,"w");
        fwrite($fp, $_POST['tpl_data']); // FIXME いきなり POST はちょっと...
        fclose($fp);
    }

    /**
     * PHPファイルを作成する.
     *
     * @param string $path PHPファイルのパス
     * @return void
     */
    function lfCreatePHPFile($path){

        // php保存先ディレクトリが存在していなければ作成する
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path));
        }

        // ベースとなるPHPファイルの読み込み
        if (file_exists(USER_DEF_PHP)){
            $php_data = file_get_contents(USER_DEF_PHP);
        }

        // require.phpの場所を書き換える
        $php_data = str_replace("###require###", HTML_PATH . "require.php", $php_data);

        // phpファイルの作成
        $fp = fopen($path,"w");
        fwrite($fp, $php_data);
        fclose($fp);
    }

}
?>
