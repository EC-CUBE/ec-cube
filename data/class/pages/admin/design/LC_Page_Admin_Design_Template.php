<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
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

require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * テンプレート設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Design_Template extends LC_Page_Admin_Ex
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'design/template.tpl';
        $this->tpl_subno    = 'template';
        $this->tpl_mainno   = 'design';
        $this->tpl_maintitle = 'デザイン管理';
        $this->tpl_subtitle = 'テンプレート設定';
        $this->arrErr  = array();
        $this->arrForm = array();
        ini_set('max_execution_time', 300);
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrDeviceType = $masterData->getMasterData('mtb_device_type');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    public function action()
    {
        $objFormParam = new SC_FormParam_Ex();
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_REQUEST);
        $objFormParam->convParam();

        $this->device_type_id = $objFormParam->getValue('device_type_id', DEVICE_TYPE_PC);
        $this->tpl_select = $this->getTemplateName($this->device_type_id);
        $template_code = $objFormParam->getValue('template_code');

        switch ($this->getMode()) {
            // 登録ボタン押下時
            case 'register':
                $this->arrErr = $objFormParam->checkError();
                if (SC_Utils_Ex::isBlank($this->arrErr)) {
                    if ($this->doRegister($template_code, $this->device_type_id)) {
                        $this->tpl_select = $template_code;
                        $this->tpl_onload = "alert('登録が完了しました。');";
                    }
                }
                break;

            // 削除ボタン押下時
            case 'delete':
                if ($objFormParam->checkError()) {
                    SC_Utils_Ex::sfDispError('');
                }
                $this->arrErr = $objFormParam->checkError();
                if (SC_Utils_Ex::isBlank($this->arrErr)) {
                    if ($this->doDelete($template_code, $this->device_type_id)) {
                        $this->tpl_onload = "alert('削除が完了しました。');";
                    }
                }
                break;

            // downloadボタン押下時
            case 'download':
                $this->arrErr = $objFormParam->checkError();
                if (SC_Utils_Ex::isBlank($this->arrErr)) {
                    if ($this->doDownload($template_code) !== false) {
                        // ブラウザに出力し, 終了する
                        SC_Response_Ex::actionExit();
                    }
                }
                break;

            default:
                break;
        }

        $this->templates = $this->getAllTemplates($this->device_type_id);
        $this->tpl_subtitle = $this->arrDeviceType[$this->device_type_id] . '＞' . $this->tpl_subtitle;
    }

    /**
     * パラメーター情報の初期化
     *
     * @param  SC_FormParam_Ex $objFormParam SC_FormParamインスタンス
     * @return void
     */
    public function lfInitParam(&$objFormParam)
    {
        $objFormParam->addParam('端末種別ID', 'device_type_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('template_code', 'template_code', STEXT_LEN, 'a', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK', 'ALNUM_CHECK'));
    }

    /**
     * 使用するテンプレートを設定する.
     *
     * テンプレートをマスターデータに登録する.
     *
     * @param  string  $template_code  テンプレートコード
     * @param  integer $device_type_id 端末種別ID
     * @return void
     */
    public function doUpdateMasterData($template_code, $device_type_id)
    {
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
                break;
        }

        // DBのデータを更新
        $arrData = array($defineName => var_export($template_code, true));
        $masterData->updateMasterData('mtb_constants', array(), $arrData);

        // キャッシュを生成
        $masterData->createCache('mtb_constants', array(), true, array('id', 'remarks'));
    }

    /**
     * ブロック位置の更新.
     *
     * ブロック位置を更新する SQL を実行する.
     * この SQL は, 各端末に合わせて実行する必要がある
     *
     * @param  string $filepath SQLのファイルパス
     * @return void
     */
    public function updateBloc($filepath)
    {
        $sql = file_get_contents($filepath);
        if ($sql !== false) {
            // 改行、タブを1スペースに変換
            $sql = preg_replace("/[\r\n\t]/", ' ', $sql);
            $sql_split = explode(';', $sql);
            $objQuery =& SC_Query_Ex::getSingletonInstance();
            foreach ($sql_split as $val) {
                if (trim($val) != '') {
                    $objQuery->query($val);
                }
            }
        }
    }

    /**
     * テンプレートパッケージの削除.
     *
     * @param  string  $template_code  テンプレートコード
     * @param  integer $device_type_id 端末種別ID
     * @return boolean 成功した場合 true; 失敗した場合 false
     */
    public function doDelete($template_code, $device_type_id)
    {
        if ($template_code == $this->getTemplateName($device_type_id)
                || $template_code == $this->getTemplateName($device_type_id, true)) {
            $this->arrErr['err'] = '※ デフォルトテンプレートと、選択中のテンプレートは削除出来ません<br />';

            return false;
        } else {
            $objQuery =& SC_Query_Ex::getSingletonInstance();
            $objQuery->begin();
            $objQuery->delete('dtb_templates', 'template_code = ? AND device_type_id = ?',
                              array($template_code, $device_type_id));

            $error =  '※ テンプレートの削除ができませんでした<br />';
            // テンプレート削除
            $templates_dir = SMARTY_TEMPLATES_REALDIR . $template_code. '/';
            if (SC_Helper_FileManager_Ex::deleteFile($templates_dir) === false) {
                $this->arrErr['err'] = $error;
                $objQuery->rollback();

                return false;
            }
            // ユーザーデータ削除
            $user_dir = USER_TEMPLATE_REALDIR. $template_code. '/';
            if (SC_Helper_FileManager_Ex::deleteFile($user_dir) === false) {
                $this->arrErr['err'] = $error;
                $objQuery->rollback();

                return false;
            }

            // コンパイル削除
            $templates_c_dir = DATA_REALDIR. 'Smarty/templates_c/'. $template_code. '/';
            if (SC_Helper_FileManager_Ex::deleteFile($templates_c_dir) === false) {
                $this->arrErr['err'] = $error;
                $objQuery->rollback();

                return false;
            }
            $objQuery->commit();

            return true;
        }
    }

    /**
     * 登録を実行する.
     *
     * 失敗した場合は, エラーメッセージを出力し, false を返す.
     *
     * @param  string  $template_code  テンプレートコード
     * @param  integer $device_type_id 端末種別ID
     * @return boolean 成功した場合 true; 失敗した場合 false
     */
    public function doRegister($template_code, $device_type_id)
    {
        $tpl_dir = USER_TEMPLATE_REALDIR . $template_code . '/';
        if (!is_dir($tpl_dir)) {
            $this->arrErr['err'] = '※ ' . $tpl_dir . 'が見つかりません<br />';

            return false;
        }

        // 更新SQLファイルが存在する場合はブロック位置を更新
        $sql_file = $tpl_dir . 'sql/update_bloc.sql';
        if (file_exists($sql_file)) {
            $this->updateBloc($sql_file);
        }

        $this->doUpdateMasterData($template_code, $device_type_id);
        // コンパイルファイルのクリア処理
        $objView = new SC_AdminView_Ex();
        $objView->_smarty->clear_compiled_tpl();

        return true;
    }

    /**
     * ダウンロードを実行する.
     *
     * 指定のテンプレートをアーカイブし, ブラウザに出力する.
     * 失敗した場合は, エラーメッセージを出力し, false を返す.
     *
     * @param  string  $template_code テンプレートコード
     * @return boolean 成功した場合 true; 失敗した場合 false
     */
    public function doDownload($template_code)
    {
        $from_dir = USER_TEMPLATE_REALDIR . $template_code . '/';
        $to_dir = SMARTY_TEMPLATES_REALDIR . $template_code . '/_packages/';
        if (SC_Utils_Ex::recursiveMkdir($to_dir) === false) {
            $this->arrErr['err'] = '※ ディレクトリの作成に失敗しました<br />';

            return false;
        }
        SC_Utils_Ex::sfCopyDir($from_dir, $to_dir);
        if (SC_Helper_FileManager_Ex::downloadArchiveFiles(SMARTY_TEMPLATES_REALDIR . $template_code, $template_code) === false) {
            $this->arrErr['err'] = '※ アーカイブファイルの作成に失敗しました<br />';

            return false;
        }

        return true;
    }

    /**
     * テンプレート情報を取得する.
     *
     * @param  integer $device_type_id 端末種別ID
     * @return array   テンプレート情報の配列
     */
    public function getAllTemplates($device_type_id)
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        return $objQuery->select('*', 'dtb_templates', 'device_type_id = ?', array($device_type_id));
    }

    /**
     * テンプレート名を返す.
     *
     * @param  integer $device_type_id 端末種別ID
     * @param  boolean $isDefault      デフォルトテンプレート名を返す場合 true
     * @return string  テンプレート名
     */
    public function getTemplateName($device_type_id, $isDefault = false)
    {
        switch ($device_type_id) {
            case DEVICE_TYPE_MOBILE:
                return $isDefault ? MOBILE_DEFAULT_TEMPLATE_NAME : MOBILE_TEMPLATE_NAME;

            case DEVICE_TYPE_SMARTPHONE:
                return $isDefault ? SMARTPHONE_DEFAULT_TEMPLATE_NAME : SMARTPHONE_TEMPLATE_NAME;

            case DEVICE_TYPE_PC:
            default:
                break;
        }

        return $isDefault ? DEFAULT_TEMPLATE_NAME : TEMPLATE_NAME;
    }
}
