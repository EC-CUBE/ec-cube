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
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';
require_once DATA_REALDIR . 'module/Tar.php';
require_once CLASS_EX_REALDIR . 'helper_extends/SC_Helper_FileManager_Ex.php';

/**
 * テンプレート設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Design_Template extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'design/template.tpl';
        $this->tpl_subnavi  = 'design/subnavi.tpl';
        $this->tpl_subno    = 'template';
        $this->tpl_mainno   = "design";
        $this->tpl_subtitle = 'テンプレート設定';
        $this->arrErr  = array();
        $this->arrForm = array();
        ini_set("max_execution_time", 300);
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
     * FIXME ロジックを見直し
     *
     * @return void
     */
    function action() {
        // 端末種別IDを取得
        if (isset($_REQUEST['device_type_id'])
            && is_numeric($_REQUEST['device_type_id'])) {
            $device_type_id = $_REQUEST['device_type_id'];
        } else {
            $device_type_id = DEVICE_TYPE_PC;
        }

        $this->tpl_select = $this->getTemplateName($device_type_id);

        $objView = new SC_AdminView_Ex();

        switch($this->getMode()) {

            // 登録ボタン押下時
        case 'register':
            // パラメータ検証
            $objForm = $this->lfInitRegister();
            if ($objForm->checkError()) {
                SC_Utils_Ex::sfDispError('');
            }

            $template_code = $objForm->getValue('template_code');
            $this->tpl_select = $template_code;

            if($template_code == "") {
                $template_code = $this->getTemplateName($device_type_id, true);
            }

            // DBへ使用するテンプレートを登録
            $this->lfRegisterTemplate($template_code);

            // XXX コンパイルファイルのクリア処理を行う
            $objView->_smarty->clear_compiled_tpl();

            // ブロック位置を更新
            $this->lfChangeBloc($template_code);

            // 完了メッセージ
            $this->tpl_onload="alert('登録が完了しました。');";
            break;

            // 削除ボタン押下時
        case 'delete':
            // パラメータ検証
            $objForm = $this->lfInitDelete();
            if ($objForm->checkError()) {
                SC_Utils_Ex::sfDispError('');
            }

            //現在使用中のテンプレートとデフォルトのテンプレートは削除できないようにする
            $template_code = $objForm->getValue('template_code_temp');
            if ($template_code == $this->getTemplateName($device_type_id)
                || $template_code == $this->getTemplateName($device_type_id, true)) {
                $this->tpl_onload = "alert('デフォルトテンプレートと、選択中のテンプレートは削除出来ません');";
                break;
            }
            $this->lfDeleteTemplate($template_code);
            break;

            // downloadボタン押下時
        case 'download':
            // パラメータ検証
            $objForm = $this->lfInitDownload();
            $template_code = $objForm->getValue('template_code_temp');
            // ユーザデータの下のファイルも保存する。
            $from_dir = USER_TEMPLATE_REALDIR . $template_code . "/";
            $to_dir = SMARTY_TEMPLATES_REALDIR . $template_code . "/_packages/";
            SC_Utils_Ex::sfMakeDir($to_dir);
            SC_Utils_Ex::sfCopyDir($from_dir, $to_dir);
            SC_Helper_FileManager_Ex::downloadArchiveFiles(SMARTY_TEMPLATES_REALDIR . $template_code);
            break;

            // プレビューボタン押下時
        case 'preview':
            break;

        default:
            break;
        }

        $this->templates = $this->lfGetAllTemplates($device_type_id);
        $this->now_template = TEMPLATE_NAME;
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

    function lfInitRegister() {
        $objForm = new SC_FormParam_Ex();
        $objForm->addParam(
            'template_code', 'template_code', STEXT_LEN, '',
            array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK", "ALNUM_CHECK")
        );
        $objForm->setParam($_POST);

        return $objForm;
    }

    function lfInitDelete() {
        $objForm = new SC_FormParam_Ex();
        $objForm->addParam(
            'template_code_temp', 'template_code_temp', STEXT_LEN, '',
            array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK", "ALNUM_CHECK")
        );
        $objForm->setParam($_POST);

        return $objForm;
    }

    function lfInitDownload() {
        $objForm = new SC_FormParam_Ex();
        $objForm->addParam(
            'template_code_temp', 'template_code_temp', STEXT_LEN, '',
            array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK", "ALNUM_CHECK")
        );
        $objForm->setParam($_POST);

        return $objForm;
    }

    /**
     * 使用するテンプレートをDBへ登録する
     */
    function lfRegisterTemplate($template_code, $device_type_id) {
        $masterData = new SC_DB_MasterData_Ex();

        $defineName = 'TEMPLATE_NAME';
        switch ($device_type_id) {
        case DEVICE_TYPE_MOBILE:
            $defineName = 'MOBILE_' . $defineName;
            break;

        case DEVICE_TYPE_SMARTPHONE:
            $defineName = 'SMARTPHONE_' . $defineName;
            break;
        case DEVICE_TYPE_PC:
        default:
        }

        $data = array($defineName => var_export($template_code, TRUE));

        // DBのデータを更新
        $masterData->updateMasterData('mtb_constants', array(), $data);

        // キャッシュを生成
        $masterData->createCache('mtb_constants', array(), true, array('id', 'remarks'));
    }

    /**
     * ブロック位置の更新
     */
    function lfChangeBloc($template_code) {
        $objQuery = new SC_Query_Ex();
        /*
         * FIXME 各端末に合わせて作成する必要あり
         * $filepath = USER_TEMPLATE_REALDIR. $template_code. "/sql/update_bloc.sql";
         */

        // ブロック位置更新SQLファイル有
        if(file_exists($filepath)) {
            if($fp = fopen($filepath, "r")) {
                $sql = fread($fp, filesize($filepath));
                fclose($fp);
            }
            // 改行、タブを1スペースに変換
            $sql = preg_replace("/[\r\n\t]/", " " ,$sql);
            $sql_split = explode(";", $sql);
            foreach($sql_split as $key => $val){
                if (trim($val) != "") {
                    $objQuery->query($val);
                }
            }
        }
    }

    /**
     * テンプレートパッケージの削除
     */
    function lfDeleteTemplate($template_code) {
        // DB更新
        $objQuery = new SC_Query_Ex();
        $objQuery->delete('dtb_templates', 'template_code = ?', array($template_code));
        // テンプレート削除
        $templates_dir = SMARTY_TEMPLATES_REALDIR. $template_code. "/";
        SC_Utils_Ex::sfDelFile($templates_dir);
        // コンパイル削除
        $templates_c_dir = DATA_REALDIR. "Smarty/templates_c/". $template_code. "/";
        SC_Utils_Ex::sfDelFile($templates_c_dir);
        // ユーザーデータ削除
        $user_dir = USER_TEMPLATE_REALDIR. $template_code. "/";
        SC_Utils_Ex::sfDelFile($user_dir);
    }

    function lfGetAllTemplates($device_type_id) {
        $objQuery = new SC_Query_Ex();
        $arrRet = $objQuery->select('*', 'dtb_templates', "device_type_id = ?", array($device_type_id));
        if (empty($arrRet)) return array();

        return $arrRet;
    }

    /*
     * 関数名：lfGetFileContents()
     * 引数1 ：ファイルパス
     * 説明　：ファイル読込
     * 戻り値：無し
     */
    function lfGetFileContents($read_file) {

        if(file_exists($read_file)) {
            $contents = file_get_contents($read_file);
        } else {
            $contents = "";
        }

        return $contents;
    }

    /**
     * テンプレート名を返す.
     */
    function getTemplateName($device_type_id, $isDefault = false) {
        switch ($device_type_id) {
        case DEVICE_TYPE_MOBILE:
            return $isDefault ? MOBILE_DEFAULT_TEMPLATE_NAME : MOBILE_TEMPLATE_NAME;
            break;

        case DEVICE_TYPE_SMARTPHONE:
            return $isDefault ? SMARTPHONE_DEFAULT_TEMPLATE_NAME : SMARTPHONE_TEMPLATE_NAME;
            break;

        case DEVICE_TYPE_PC:
        default:
        }
        return $isDefault ? DEFAULT_TEMPLATE_NAME : TEMPLATE_NAME;
    }
}
?>
