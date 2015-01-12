<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Page\Admin\Design;

use Eccube\Application;
use Eccube\Page\Admin\AbstractAdminPage;
use Eccube\Framework\FormParam;
use Eccube\Framework\Query;
use Eccube\Framework\Response;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Helper\FileManagerHelper;
use Eccube\Framework\Util\Utils;
use Eccube\Framework\View\AdminView;

/**
 * テンプレート設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Template extends AbstractAdminPage
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
        $masterData = Application::alias('eccube.db.master_data');
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
        $objFormParam = Application::alias('eccube.form_param');
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
                if (Utils::isBlank($this->arrErr)) {
                    if ($this->doRegister($template_code, $this->device_type_id)) {
                        $this->tpl_select = $template_code;
                        $this->tpl_onload = "alert('登録が完了しました。');";
                    }
                }
                break;

            // 削除ボタン押下時
            case 'delete':
                if ($objFormParam->checkError()) {
                    Utils::sfDispError('');
                }
                $this->arrErr = $objFormParam->checkError();
                if (Utils::isBlank($this->arrErr)) {
                    if ($this->doDelete($template_code, $this->device_type_id)) {
                        $this->tpl_onload = "alert('削除が完了しました。');";
                    }
                }
                break;

            // downloadボタン押下時
            case 'download':
                $this->arrErr = $objFormParam->checkError();
                if (Utils::isBlank($this->arrErr)) {
                    if ($this->doDownload($template_code) !== false) {
                        // ブラウザに出力し, 終了する
                        Application::alias('eccube.response')->actionExit();
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
     * @param  FormParam $objFormParam FormParamインスタンス
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
        $masterData = Application::alias('eccube.db.master_data');

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
            $objQuery = Application::alias('eccube.query');
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
            $objQuery = Application::alias('eccube.query');
            $objQuery->begin();
            $objQuery->delete('dtb_templates', 'template_code = ? AND device_type_id = ?',
                              array($template_code, $device_type_id));

            $error =  '※ テンプレートの削除ができませんでした<br />';
            // テンプレート削除
            $templates_dir = SMARTY_TEMPLATES_REALDIR . $template_code. '/';
            if (Application::alias('eccube.helper.file_manager')->deleteFile($templates_dir) === false) {
                $this->arrErr['err'] = $error;
                $objQuery->rollback();

                return false;
            }
            // ユーザーデータ削除
            $user_dir = USER_TEMPLATE_REALDIR. $template_code. '/';
            if (Application::alias('eccube.helper.file_manager')->deleteFile($user_dir) === false) {
                $this->arrErr['err'] = $error;
                $objQuery->rollback();

                return false;
            }

            // コンパイル削除
            $templates_c_dir = DATA_REALDIR. 'Smarty/templates_c/'. $template_code. '/';
            if (Application::alias('eccube.helper.file_manager')->deleteFile($templates_c_dir) === false) {
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
        $objView = new AdminView();
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
        if (Utils::recursiveMkdir($to_dir) === false) {
            $this->arrErr['err'] = '※ ディレクトリの作成に失敗しました<br />';

            return false;
        }
        Utils::sfCopyDir($from_dir, $to_dir);
        if (Application::alias('eccube.helper.file_manager')->downloadArchiveFiles(SMARTY_TEMPLATES_REALDIR . $template_code, $template_code) === false) {
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
        $objQuery = Application::alias('eccube.query');

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
