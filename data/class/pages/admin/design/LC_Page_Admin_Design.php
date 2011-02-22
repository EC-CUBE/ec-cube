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
 * デザイン管理 のページクラス.
 *
 * ターゲットID 0:未使用 1:レフトナビ 2:ライトナビ 3:イン画面上部 4:メイン画面下部  5:画面上部 6:画面下部 7:ヘッダより上 8:フッタより下 9:HEADタグ内 10:ヘッダ内部
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Design extends LC_Page_Admin {

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
        $this->tpl_subtitle = 'レイアウト設定';
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrTarget = $masterData->getMasterData("mtb_target");
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
     * TODO パラメータの数値チェック
     *
     * @return void
     */
    function action() {
        $objLayout = new SC_Helper_PageLayout_Ex();

        // ページIDを取得
        if (isset($_REQUEST['page_id']) && is_numeric($_REQUEST['page_id'])) {
            $page_id = $_REQUEST['page_id'];
        } else {
            $page_id = 1; // FIXME $_REQUEST['page_id'] を受け取れない不具合時に不正処理が行なわれる原因となった
        }
        // 端末種別IDを取得
        if (isset($_REQUEST['device_type_id'])
            && is_numeric($_REQUEST['device_type_id'])) {
            $device_type_id = $_REQUEST['device_type_id'];
        } else {
            $device_type_id = DEVICE_TYPE_PC;
        }

        // 編集可能ページを取得
        $this->arrEditPage = $objLayout->lfGetPageData("page_id <> 0 AND device_type_id = ?", array($device_type_id));

        // レイアウト情報を取得
        $arrBlocPos = $objLayout->lfGetNaviData($page_id, $device_type_id);

        // データの存在チェックを行う
        $arrPageData = $objLayout->lfGetPageData("page_id = ? AND device_type_id = ?", array($page_id, $device_type_id));

        if (count($arrPageData) <= 0) {
            $this->exists_page = 0;
        }else{
            $this->exists_page = 1;
        }

        // メッセージ表示
        if (isset($_GET['msg']) && $_GET['msg'] == "on") {
            $this->complate_msg="alert('登録が完了しました。');";
        }

        // ブロックを取得
        $objQuery = SC_Query::getSingletonInstance();
        $arrBloc = $objQuery->select("*", "dtb_bloc", "device_type_id = ?", array($device_type_id));

        switch ($this->getMode()) {
        // 新規ブロック作成
        case 'new_bloc':
            SC_Response_Ex::sendRedirect('bloc.php', array("device_type_id" => $device_type_id));
            exit;
            break;

        // 新規ページ作成
        case 'new_page':
            SC_Response_Ex::sendRedirect('main_edit.php', array("device_type_id" => $device_type_id));
            exit;
            break;

        case 'confirm':
        case 'preview':
            //TODO 要リファクタリング(MODE if利用)
            $page_id = $_POST['page_id'];
            if ($this->getMode() == 'preview') {
                $page_id = '0';
            }

            // 更新用にデータを整える
            $arrUpdBlocData = array();

            // delete実行
            $arrRet = $objQuery->delete("dtb_blocposition",
                                        "page_id = ? AND device_type_id = ?",
                                        array($page_id, $device_type_id));

            $arrTargetFlip = array_flip($this->arrTarget);

            // POSTのデータを使いやすいように修正
            for ($upd_cnt = 1; $upd_cnt <= $_POST['bloc_cnt']; $upd_cnt++) {
                if (!isset($_POST['id_'.$upd_cnt])) {
                    break;
                }

                // ブロック名称
                $arrUpdBlocData[$upd_cnt]['name']       = $_POST['name_'.$upd_cnt];
                // ブロックID
                $arrUpdBlocData[$upd_cnt]['id']         = $_POST['id_'.$upd_cnt];
                // ターゲットID
                $arrUpdBlocData[$upd_cnt]['target_id']  = $arrTargetFlip[$_POST['target_id_'.$upd_cnt]];
                // TOP座標
                $arrUpdBlocData[$upd_cnt]['top']        = $_POST['top_'.$upd_cnt];
                // 全ページ適用か
                $arrUpdBlocData[$upd_cnt]['anywhere']   = $_POST['anywhere_'.$upd_cnt];
                // 更新URL
                $arrUpdBlocData[$upd_cnt]['update_url'] = $_SERVER['HTTP_REFERER'];
            }


            // ブロックの順序を取得し、更新を行う
            foreach ($arrUpdBlocData as $key => $val) {
                if ($arrUpdBlocData[$key]['target_id'] == TARGET_ID_UNUSED) {
                    continue;
                }

                // ブロックの順序を取得
                $arrUpdBlocData[$key]['bloc_row'] = $this->lfGetRowID($arrUpdBlocData, $val);

                // insertデータ生成
                $arrInsData = array('device_type_id' => $device_type_id,
                                    'page_id' => $page_id,
                                    'target_id' => $arrUpdBlocData[$key]['target_id'],
                                    'bloc_id' => $arrUpdBlocData[$key]['id'],
                                    'bloc_row' => $arrUpdBlocData[$key]['bloc_row'],
                                    'anywhere' => $arrUpdBlocData[$key]['anywhere'] ? 1 : 0);
                $count = $objQuery->getOne("SELECT COUNT(*) FROM dtb_blocposition WHERE anywhere = 1 AND bloc_id = ? AND device_type_id = ?",
                                           array($arrUpdBlocData[$key]['id'], $device_type_id));

                if ($arrUpdBlocData[$key]['anywhere'] == 1) {
                    $count = $objQuery->getOne("SELECT COUNT(*) FROM dtb_blocposition WHERE anywhere = 1 AND bloc_id = ? AND device_type_id = ?",
                                               array($arrUpdBlocData[$key]['id'], $device_type_id));
                    if ($count != 0) {
                        continue;
                    }
                } else {
                    if ($count > 0) {
                        $objQuery->query("DELETE FROM dtb_blocposition WHERE anywhere = 1 AND bloc_id = ? AND device_type_id = ?",
                                         array($arrUpdBlocData[$key]['id'], $device_type_id));
                    }
                }
                // SQL実行
                $arrRet = $objQuery->insert("dtb_blocposition", $arrInsData);
            }

            // プレビュー処理 TODO 要リファクタリング(MODE if利用)
            if ($this->getMode() == 'preview') {
                $this->lfSetPreData($arrPageData, $objLayout);

                $_SESSION['preview'] = "ON";

                SC_Response_Ex::sendRedirectFromUrlPath('preview/' . DIR_INDEX_PATH, array("filename" => $arrPageData[0]["filename"]));
                exit;

            } else {
                $arrQueryString = array("device_type_id" => $device_type_id, "page_id" => $page_id, "msg" => "on");
                SC_Response_Ex::reload($arrQueryString, true);
                exit;
            }
        break;

        // データ削除処理
        case 'delete':
            //ベースデータでなければファイルを削除
            if (!$objLayout->lfCheckBaseData($page_id, $device_type_id)) {
                $objLayout->lfDelPageData($page_id, $device_type_id);
                SC_Response_Ex::reload(array('device_type_id' => $device_type_id), true);
                exit;
            }
        break;

        default:
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
                $val['target_id'] = TARGET_ID_UNUSED; // 未使用に追加する
                $tpl_arrBloc = $this->lfSetBlocData($arrBloc, $val, $tpl_arrBloc, $cnt);
                $cnt++;
            }
        }

        $this->tpl_arrBloc = $tpl_arrBloc;
        $this->bloc_cnt = count($tpl_arrBloc);
        $this->page_id = $page_id;
        $this->device_type_id = $device_type_id;

        // ページ名称を取得
        $arrPageData = $objLayout->lfGetPageData('page_id = ? AND device_type_id = ?', array($page_id, $device_type_id));
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
     * ブロック情報の配列を生成する.
     *
     * @param array $arrBloc Bloc情報
     * @param array $tpl_arrBloc データをセットする配列
     * @param array $val DBから取得したブロック情報
     * @param integer $cnt 配列番号
     * @return array データをセットした配列
     */
    function lfSetBlocData($arrBloc, $val, $tpl_arrBloc, $cnt) {

        $tpl_arrBloc[$cnt]['target_id'] = $this->arrTarget[$val['target_id']];
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
     * FIXME
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
        $del_tpl = USER_REALDIR . "templates/" . $filename . '.tpl';

        if (file_exists($del_tpl)){
            unlink($del_tpl);
        }

        // filename が空の場合にはMYページと判断
        if($filename == ""){
            $tplfile = TEMPLATE_REALDIR . "mypage/index";
            $filename = 'mypage';
        } else {
            $tplfile = TEMPLATE_REALDIR . $filename;
        }

        // プレビュー用tplファイルのコピー
        $copyTo = USER_REALDIR . "templates/preview/" . TEMPLATE_NAME . "/" . $filename . ".tpl";

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
        $sql .= "     ,filename = ?";
//      $sql .= "     ,anywhere = ?";
        $sql .= " where page_id = 0";
        var_dump($ret);
                echo("####<br/>\n\n".__LINE__ ." in file:".__FILE__."<br/>\n\n ####");

        $arrUpdData = array($ret[0]['page_id']
        ,$ret[0]['page_id']
        ,$ret[0]['page_id']
        ,USER_DIR . "templates/" . TEMPLATE_NAME . "/"
        ,$filename
//      ,$ret[0]['anywhere']

        );

        $objQuery->query($sql,$arrUpdData);
    }
}
?>
