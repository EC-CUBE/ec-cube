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
require_once(CLASS_REALDIR . "pages/admin/LC_Page_Admin.php");

/**
 * メイン編集 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Design_MainEdit extends LC_Page_Admin {

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
        $this->tpl_subnavi  = 'design/subnavi.tpl';
        $this->text_row     = 13;
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
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        $objView = new SC_AdminView_Ex();
        $this->objLayout = new SC_Helper_PageLayout_Ex();

        // ページIDを取得
        if (isset($_REQUEST['page_id']) && is_numeric($_REQUEST['page_id'])) {
            $page_id = $_REQUEST['page_id'];
        }

        $this->page_id = $page_id;

        // 端末種別IDを取得
        if (isset($_REQUEST['device_type_id'])
            && is_numeric($_REQUEST['device_type_id'])) {
            $device_type_id = $_REQUEST['device_type_id'];
        } else {
            $device_type_id = DEVICE_TYPE_PC;
        }

        // ページ一覧を取得
        $this->arrPageList = $this->objLayout->lfGetPageData("page_id <> 0 AND device_type_id = ?",
                                                             array($device_type_id));

        // メッセージ表示
        if (isset($_GET['msg']) && $_GET['msg'] == "on"){
            $this->tpl_onload="alert('登録が完了しました。');";
        }

        // page_id が指定されている場合にはテンプレートデータの取得
        if (is_numeric($page_id) && $page_id != '') {
            $this->arrPageData = $this->lfGetPageData($page_id, $device_type_id, $objView);
        }

        switch ($this->getMode()) {
        case 'preview':
            $this->lfPreviewPageData($page_id, $device_type_id);
            exit;
            break;

        case 'delete':
            if (!$this->objLayout->lfCheckBaseData($page_id, $device_type_id)) {
                $this->lfDeletePageData($page_id, $device_type_id);
                exit;
            }
            break;

        case 'confirm':
            $this->lfConfirmPageData($page_id, $device_type_id);
        default:
        }
        $this->device_type_id = $device_type_id;
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
     * ページデータを取得する.
     *
     * @param integer $page_id ページID
     * @param integer $device_type_id 端末種別ID
     * @param object $objView ビューオブジェクト
     * @return void
     */
    function lfGetPageData($page_id, $device_type_id, $objView){
        $arrPageData = $this->objLayout->lfGetPageData("page_id = ? AND device_type_id = ?",
                                                       array($page_id, $device_type_id));

        if (strlen($arrPageData[0]['filename']) == 0) {
            $this->arrErr['page_id_err'] = "※ 指定されたページは編集できません。";
            // 画面の表示
            $objView->assignobj($this);
            $objView->display(MAIN_FRAME);
            exit;
        }

        // テンプレートを読み込む
        $templatePath = $this->objLayout->getTemplatePath($device_type_id);
        $arrPageData[0]['tpl_data'] = file_get_contents($templatePath . $arrPageData[0]['filename'] . ".tpl");

        // チェックボックスの値変更
        $arrPageData[0]['header_chk'] = SC_Utils_Ex::sfChangeCheckBox($arrPageData[0]['header_chk'], true);
        $arrPageData[0]['footer_chk'] = SC_Utils_Ex::sfChangeCheckBox($arrPageData[0]['footer_chk'], true);

        // ディレクトリを画面表示用に編集
        $arrPageData[0]['filename'] = preg_replace('|^' . preg_quote(USER_DIR) . '|', '', $arrPageData[0]['filename']);

        return $arrPageData[0];
    }

    /**
     * プレビュー画面を表示する.
     *
     * @param integer $page_id_old 元のページID
     * @param integer $device_type_id 端末種別ID
     * @return void
     */
    function lfPreviewPageData($page_id_old, $device_type_id) {

        // プレビューの場合ページIDを0にセットする。
        $page_id = '0';
        $url = 'preview/index';

        $arrPreData = $this->objLayout->lfGetPageData("page_id = ? AND device_type_id = ?",
                                                      array($page_id, $device_type_id));

        // DBへデータを更新する
        $this->lfEntryPageData(
            $device_type_id,
            $page_id,
            $_POST['page_name'],
            $url,
            $_POST['header_chk'],
            $_POST['footer_chk']
        );

        // TPLファイル作成
        $cre_tpl = $this->objLayout->getTemplatePath($device_type_id) . "{$url}.tpl";
        $this->lfCreateFile($cre_tpl, $_POST['tpl_data']);

        // blocposition を削除
        $objQuery = new SC_Query(); // DB操作オブジェクト
        $ret = $objQuery->delete('dtb_blocposition', 'page_id = 0 AND device_type_id = ?', array($device_type_id));

        if ($page_id_old != "") {
            // 登録データを取得
            $sql = 'SELECT target_id, bloc_id, bloc_row FROM dtb_blocposition WHERE page_id = ? AND device_type_id = ?';
            $ret = $objQuery->getAll($sql, array($page_id_old, $device_type_id));

            // blocposition を複製
            foreach($ret as $row){
                $row['page_id'] = $page_id;
                $row['device_type_id'] = $device_type_id;
                $objQuery->insert('dtb_blocposition', $row);
            }
        }
        $_SESSION['preview'] = "ON";
        SC_Response_Ex::sendRedirectFromUrlPath('preview/' . DIR_INDEX_PATH, array("filename" => $arrPageData[0]["filename"]));
    }

    /**
     * データ登録処理.
     *
     * @param integer $page_id ページID
     * @param integer $device_type_id 端末種別ID
     * @return void
     */
    function lfConfirmPageData($page_id, $device_type_id) {
        // エラーチェック
        $this->arrErr = $this->lfErrorCheck($_POST, $device_type_id);

        // エラーがなければ更新処理を行う
        if (count($this->arrErr) == 0) {
            // DBへデータを更新する
            $arrTmp = $this->lfEntryPageData(
                $device_type_id,
                $page_id,
                $_POST['page_name'],
                USER_DIR . $_POST['url'],
                $_POST['header_chk'],
                $_POST['footer_chk']
            );
            $page_id = $arrTmp['page_id'];

            $arrTmp = $this->objLayout->lfGetPageData('page_id = ? AND device_type_id = ?', array($page_id, $device_type_id));
            $arrData = $arrTmp[0];

            // ベースデータでなければファイルを削除し、PHPファイルを作成する
            if (!$this->objLayout->lfCheckBaseData($arrData['page_id'], $device_type_id)) {
                // PHPファイル作成
                $this->lfCreatePHPFile($_POST['url'], $device_type_id);
            }

            // TPLファイル作成
            $cre_tpl = $this->objLayout->getTemplatePath($device_type_id) . $arrData['filename'] . '.tpl';
            $this->lfCreateFile($cre_tpl, $_POST['tpl_data']);

            $arrQueryString = array(
                "page_id" => $arrData['page_id'],
                "device_type_id" => $device_type_id,
                "msg"     => "on",
            );
            $this->objDisplay->reload($arrQueryString, true);
            exit;
        } else {
            // エラーがあれば入力時のデータを表示する
            $this->arrPageData = $_POST;
            $this->arrPageData['header_chk'] = SC_Utils_Ex::sfChangeCheckBox(SC_Utils_Ex::sfChangeCheckBox($_POST['header_chk']), true);
            $this->arrPageData['footer_chk'] = SC_Utils_Ex::sfChangeCheckBox(SC_Utils_Ex::sfChangeCheckBox($_POST['footer_chk']), true);
            $this->arrPageData['directory'] = '';
            $this->arrPageData['filename'] = $_POST['url'];
        }
    }

    /**
     * ブロック情報を更新する.
     *
     * @param integer $device_type_id
     * @param integer $page_id
     * @param string $page_name
     * @param string $filename
     * @param integer $header_chk
     * @param integer $footer_chk
     * @return array 実際に使用した更新データ
     */
    function lfEntryPageData($device_type_id, $page_id, $page_name, $filename, $header_chk, $footer_chk) {
        $objQuery = new SC_Query();
        $arrChk = array();          // 排他チェック用

        // 更新用データの変換
        $sqlval = $this->lfGetUpdData($device_type_id, $page_id, $page_name, $filename, $header_chk, $footer_chk);

        // データが存在しているかチェックを行う
        if ($page_id !== ''){
            $arrChk = $this->objLayout->lfGetPageData("page_id = ? AND device_type_id = ?",
                                                      array($page_id, $device_type_id));
        }

        // page_id が空 若しくは データが存在していない場合にはINSERTを行う
        if ($page_id === '' || !isset($arrChk[0])) {
            // FIXME device_type_id ごとの連番にする
            $sqlval['page_id'] = $objQuery->nextVal('dtb_pagelayout_page_id');
            $sqlval['device_type_id'] = $device_type_id;
            $sqlval['create_date'] = 'now()';
            $objQuery->insert('dtb_pagelayout', $sqlval);
        }
        // データが存在してる場合にはアップデートを行う
        else {
            $objQuery->update('dtb_pagelayout', $sqlval, 'page_id = ? AND device_type_id = ?',
                              array($page_id, $device_type_id));
            // 戻り値用
            $sqlval['page_id'] = $page_id;
        }
        return $sqlval;
    }

    /**
     * DBへ更新を行うデータを生成する.
     *
     * @param integer $device_type_id
     * @param integer $page_id
     * @param string $page_name
     * @param string $filename
     * @param integer $header_chk
     * @param integer $footer_chk
     * @return array 更新データ
     */
    function lfGetUpdData($device_type_id, $page_id, $page_name, $filename, $header_chk, $footer_chk) {
        $arrUpdData = array(
            'header_chk'    => SC_Utils_Ex::sfChangeCheckBox($header_chk),  // ヘッダー使用
            'footer_chk'    => SC_Utils_Ex::sfChangeCheckBox($footer_chk),  // フッター使用
            'update_url'    => $_SERVER['HTTP_REFERER'],                    // 更新URL
            'update_date'   => 'now()',
        );

        // ベースデータの場合には変更しない。
        if (!$this->objLayout->lfCheckBaseData($page_id, $device_type_id)) {
            $arrUpdData['page_name']    = $page_name;
            $arrUpdData['url']          = $filename . '.php';
            $arrUpdData['filename']     = $filename; // 拡張子を付加しない
        }

        return $arrUpdData;
    }

    /**
     * ページデータを削除する.
     *
     * @param integer $page_id ページID
     * @return void
     */
    function lfDeletePageData($page_id, $device_type_id){
        $this->objLayout->lfDelPageData($page_id, $device_type_id);
        $this->objDisplay->reload(array("device_type_id" => $device_type_id), true);
    }

    /**
     * 入力項目のエラーチェックを行う.
     *
     * @param array $arrData 入力データ
     * @param integer $device_type_id 端末種別ID
     * @return array エラー情報
     */
    function lfErrorCheck($array, $device_type_id) {
        $objErr = new SC_CheckError($array);
        $objErr->doFunc(array("名称", "page_name", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("URL", "url", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));

        // URLチェック
        $okUrl = true;
        foreach (explode('/', $array['url']) as $url_part) {
            if (!ereg( '^[a-zA-Z0-9:_~\.-]+$', $url_part)) {
                $okUrl = false;
            }
            if ($url_part == '.' || $url_part == '..') {
                $okUrl = false;
            }
        }
        if (!$okUrl) {
            $objErr->arrErr['url'] = "※ URLを正しく入力してください。<br />";
        }

        // 同一のURLが存在している場合にはエラー
        $params = array();

        $sqlWhere = 'url = ?';
        $params[] = $this->objLayout->getUserDir($device_type_id) . $array['url'] . '.php';

        // プレビュー用のレコードは除外
        $sqlWhere .= ' AND page_id <> 0';

        // 変更の場合、自身のレコードは除外
        if (strlen($array['page_id']) != 0) {
            $sqlWhere .= ' AND page_id <> ?';
            $params[] = $array['page_id'];
        }

        $arrChk = $this->objLayout->lfgetPageData($sqlWhere , $params);

        if (count($arrChk) >= 1) {
            $objErr->arrErr['url'] = '※ 同じURLのデータが存在しています。別のURLを付けてください。<br />';
        }

        return $objErr->arrErr;
    }

    /**
     * ファイルを作成する.
     *
     * @param string $path テンプレートファイルのパス
     * @param string $data テンプレートの内容
     * @return void
     */
    function lfCreateFile($path, $data){

        // ディレクトリが存在していなければ作成する
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true); // FIXME (PHP4)
        }

        // ファイル作成
        $fp = fopen($path,"w");
        if ($fp === false) {
            SC_Utils_Ex::sfDispException();
        }
        $ret = fwrite($fp, $data);
        if ($ret === false) {
            SC_Utils_Ex::sfDispException();
        }
        fclose($fp);
    }

    /**
     * PHPファイルを作成する.
     *
     * @param string $path PHPファイルのパス
     * @return void
     */
    function lfCreatePHPFile($url, $device_type_id){

        $path = $this->objLayout->getUserPath($device_type_id) . $url . ".php";

        // カスタマイズを考慮し、上書きしない。(#831)
        if (file_exists($path)) {
            return;
        }

        // php保存先ディレクトリが存在していなければ作成する
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true); // FIXME (PHP4)
        }

        // ベースとなるPHPファイルの読み込み
        if (file_exists(USER_DEF_PHP_REALFILE)){
            $php_data = file_get_contents(USER_DEF_PHP_REALFILE);
        }

        // require.phpの場所を書き換える
        $defaultStrings = "exit; // Don't rewrite. This line is rewritten by EC-CUBE.";
        $replaceStrings = "require_once '" . str_repeat('../', substr_count($url, '/')) . "../require.php';";
        $php_data = str_replace($defaultStrings, $replaceStrings, $php_data);

        // phpファイルの作成
        $fp = fopen($path,"w");
        fwrite($fp, $php_data);
        fclose($fp);
    }

}
?>
