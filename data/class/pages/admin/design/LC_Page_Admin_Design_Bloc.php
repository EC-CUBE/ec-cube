<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
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
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * ブロック編集 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Design_Bloc extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'design/bloc.tpl';
        $this->tpl_subnavi = 'design/subnavi.tpl';
        $this->tpl_subno_edit = 'bloc';
        $this->text_row = 13;
        $this->tpl_subno = 'bloc';
        $this->tpl_mainno = 'design';
        $this->tpl_subtitle = 'ブロック設定';
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrDeviceType = $masterData->getMasterData('mtb_device_type');
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
     * FIXME テンプレートパスの取得方法を要修正
     *
     * @return void
     */
    function action() {
        $objFormParam = new SC_FormParam_Ex();
        $this->lfInitParam($objFormParam, $_REQUEST);

       // ページIDを取得
        $bloc_id = $objFormParam->getValue('bloc_id');
        $this->bloc_id = $bloc_id;

        // 端末種別IDを取得
        $device_type_id = $objFormParam->getValue('device_type_id');

        $this->objLayout = new SC_Helper_PageLayout_Ex();
        $package_path = $this->objLayout->getTemplatePath($device_type_id) . BLOC_DIR;

        //サブタイトルの追加
        $this->tpl_subtitle .= ' - ' . $this->arrDeviceType[$device_type_id];

        // ブロック一覧を取得
        $this->arrBlocList = $this->lfGetBlocData($device_type_id);

        // bloc_id が指定されている場合にはブロックデータの取得
        if ($bloc_id != '') {
            $arrBlocData = $this->lfGetBlocData($device_type_id, "bloc_id = ?",
                                                array($bloc_id));

            $bloc_file = $arrBlocData[0]['tpl_path'];
            if (substr($bloc_file, 0, 1) == '/') {
                $tplPath = $bloc_file;
            } else {
                $tplPath = SC_Helper_PageLayout_Ex::getTemplatePath($this->objDisplay->detectDevice()) . BLOC_DIR . $bloc_file;
            }

            // テンプレートファイルの読み込み
            $arrBlocData[0]['tpl_data'] = file_get_contents($tplPath);
            $this->arrBlocData = $arrBlocData[0];
        }

        // メッセージ表示
        if (isset($_GET['msg']) && $_GET['msg'] == 'on') {
            // 完了メッセージ
            $this->tpl_onload="alert('登録が完了しました。');";
        }

        switch($this->getMode()) {
        case 'confirm':
            // エラーチェック
            $this->arrErr = $this->lfErrorCheck($_POST);

            // エラーがなければ更新処理を行う
            if (count($this->arrErr) == 0) {
                // DBへデータを更新する
                $this->lfEntryBlocData($_POST, $device_type_id);

                // 旧ファイルの削除
                if (file_exists($tplPath)) {
                    unlink($tplPath);
                }

                // ファイル作成
                $new_bloc_path = $package_path . $_POST['filename'] . ".tpl";
                // ディレクトリの作成
                SC_Utils_Ex::sfMakeDir($new_bloc_path);
                $fp = fopen($new_bloc_path,"w");
                if (!$fp) {
                    SC_Utils_Ex::sfDispException();
                }
                fwrite($fp, $_POST['bloc_html']); // FIXME いきなり POST はちょっと...
                fclose($fp);

                $arrBlocData = $this->lfGetBlocData($device_type_id, "filename = ?",
                                                    array($_POST['filename']));

                $bloc_id = $arrBlocData[0]['bloc_id'];
                $arrQueryString = array(
                    'bloc_id' => $bloc_id,
                    'device_type_id' => $device_type_id,
                    'msg' => 'on',
                );
                $this->objDisplay->reload($arrQueryString, true);
                exit;
            }else{
                // エラーがあれば入力時のデータを表示する
                $this->arrBlocData = $_POST;
            }
            break;
        case 'delete':
             // DBへデータを更新する
            $objQuery =& SC_Query_Ex::getSingletonInstance();
            $sql = "";                      // データ更新SQL生成用
            $ret = "";                      // データ更新結果格納用

            // bloc_id が空でない場合にはdeleteを実行
            if ($bloc_id !== '') {
                $objQuery->delete('dtb_bloc', 'bloc_id = ? AND device_type_id = ?', array($bloc_id, $device_type_id));

                // ページに配置されているデータも削除する
                $objQuery->delete('dtb_blocposition', 'bloc_id = ? AND device_type_id = ?', array($bloc_id, $device_type_id));

                // ファイルの削除
                if (file_exists($tplPath)) {
                    unlink($tplPath);
                }
            }
            $this->objDisplay->reload(array("device_type_id" => $device_type_id), true);
            exit;
            break;
        default:
            GC_Utils_Ex::gfPrintLog("MODEエラー：".$this->getMode());
            break;
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
     * パラメータ情報の初期化
     *
     * @param object $objFormParam SC_FormParamインスタンス
     * @param array $arrPost $_POSTデータ
     * @return void
     */
    function lfInitParam(&$objFormParam, $arrPost) {
        $objFormParam->addParam("ブロックID", "bloc_id", INT_LEN, 'n', array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("端末種別ID", "device_type_id", INT_LEN, 'n', array("NUM_CHECK", "MAX_LENGTH_CHECK"), DEVICE_TYPE_PC);
        $objFormParam->setParam($arrPost);
        $objFormParam->convParam();
    }

    /**
     * ブロック情報を取得する.
     *
     * @param integer $device_type_id 端末種別ID
     * @param string $where Where句文
     * @param array $arrVal Where句の絞込条件値
     * @return array ブロック情報
     */
    function lfGetBlocData($device_type_id, $where = '', $arrVal = array()){
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->setOrder("bloc_id");
        $sql_where = 'device_type_id = ?';
        $arrSql = array($device_type_id);
        if (!empty($where)) {
            $sql_where .= ' AND ' . $where;
            $arrSql = array_merge($arrSql, $arrVal);
        }
        return $objQuery->select("*", "dtb_bloc", $sql_where, $arrSql);
    }

    /**
     * ブロック情報を更新する.
     *
     * @param array $arrData 更新データ
     * @param integer $device_type_id 端末種別ID
     * @return integer 更新結果
     */
    function lfEntryBlocData($arrData, $device_type_id){
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $sql = "";                      // データ更新SQL生成用
        $ret = "";                      // データ更新結果格納用
        $arrUpdData = array();          // 更新データ生成用
        $arrChk = array();              // 排他チェック用

        // 更新データ生成
        $arrUpdData = array("bloc_name" => $arrData['bloc_name'],
                            "tpl_path" => $arrData['filename'] . '.tpl',
                            'filename' => $arrData['filename']);

        // データが存在しているかチェックを行う
        if($arrData['bloc_id'] !== ''){
            $arrChk = $this->lfGetBlocData($device_type_id, "bloc_id = ?",
                                           array($arrData['bloc_id']));
        }

        // bloc_id が空 若しくは データが存在していない場合にはINSERTを行う
        if ($arrData['bloc_id'] === '' or !isset($arrChk[0])) {
            // SQL生成
            // FIXME device_type_id ごとの連番にする
            $arrUpdData['bloc_id'] = $objQuery->nextVal('dtb_bloc_bloc_id');
            $arrUpdData['device_type_id'] = $device_type_id;
            $arrUpdData['update_date'] = "now()";
            $ret = $objQuery->insert('dtb_bloc', $arrUpdData);
        } else {
            $ret = $objQuery->update('dtb_bloc', $arrUpdData, 'bloc_id = ? AND device_type_id = ?',
                                     array($arrData['bloc_id'], $device_type_id));
        }
        return $ret;
    }

    /**
     * 入力項目のエラーチェックを行う.
     *
     * @param array $arrData 入力データ
     * @return array エラー情報
     */
    function lfErrorCheck($array) {
        $objErr = new SC_CheckError_Ex($array);

        $objErr->doFunc(array("ブロック名", "bloc_name", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("ファイル名", 'filename', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "MAX_LENGTH_CHECK","FILE_NAME_CHECK_BY_NOUPLOAD"));

        // 同一のファイル名が存在している場合にはエラー
        if(!isset($objErr->arrErr['filename']) && $array['filename'] !== ''){
            $arrChk = $this->lfGetBlocData($array['device_type_id'], "filename = ?", array($array['filename']));

            if (count($arrChk[0]) >= 1 && $arrChk[0]['bloc_id'] != $array['bloc_id']) {
                $objErr->arrErr['filename'] = '※ 同じファイル名のデータが存在しています。別の名称を付けてください。';
            }
        }

        return $objErr->arrErr;
    }
}
?>
