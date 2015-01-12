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
use Eccube\Framework\CheckError;
use Eccube\Framework\FormParam;
use Eccube\Framework\Response;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Helper\FileManagerHelper;
use Eccube\Framework\Helper\PageLayoutHelper;
use Eccube\Framework\Util\Utils;
use Eccube\Framework\Util\GcUtils;

/**
 * CSS設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Css extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'design/css.tpl';
        $this->area_row = 30;
        $this->tpl_subno = 'css';
        $this->tpl_mainno = 'design';
        $this->tpl_maintitle = 'デザイン管理';
        $this->tpl_subtitle = 'CSS設定';
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
        /* @var $objLayout PageLayoutHelper */
        $objLayout = Application::alias('eccube.helper.page_layout');

        $objFormParam = Application::alias('eccube.form_param');
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_REQUEST);
        $objFormParam->convParam();
        $this->arrErr = $objFormParam->checkError();
        $is_error = !Utils::isBlank($this->arrErr);

        // CSSファイル名を取得
        $this->css_name = $objFormParam->getValue('css_name');
        $this->old_css_name = $objFormParam->getValue('old_css_name', $this->css_name);
        $this->device_type_id = $objFormParam->getValue('device_type_id', DEVICE_TYPE_PC);

        $css_dir = $objLayout->getTemplatePath($this->device_type_id, true) . 'css/';
        $css_path = $css_dir . $this->css_name . '.css';

        switch ($this->getMode()) {
            // データ更新処理
            case 'confirm':
                if (!$is_error) {
                    $this->arrErr = array_merge($this->arrErr, $this->lfCheckError($objFormParam, $this->arrErr));
                    if (Utils::isBlank($this->arrErr)) {
                        if ($this->doRegister($css_dir, $this->css_name, $this->old_css_name, $css_path,
                                              $objFormParam->getValue('css_data'))) {
                            $this->tpl_onload = "alert('登録が完了しました。');";
                        }
                    }
                }
                break;
            case 'delete':
                if (!$is_error) {
                    if ($this->doDelete($css_path)) {
                        $arrPram = array(
                            'device_type_id' => $this->device_type_id,
                            'msg' => 'on',
                        );

                        Application::alias('eccube.response')->reload($arrPram, true);
                    }
                }
                break;
            default:
                if (isset($_GET['msg']) && $_GET['msg'] == 'on') {
                    // 完了メッセージ
                    $this->tpl_onload = "alert('登録が完了しました。');";
                }
                break;
        }

        if (Utils::isBlank($this->arrErr) && $this->checkPath($this->css_name)) {
            // CSSファイルの読み込み
            if (!Utils::isBlank($this->css_name)) {
                $objFormParam->setValue('css_data', file_get_contents($css_path));
            }
        } else {
            // 画面にエラー表示しないため, ログ出力
            GcUtils::gfPrintLog('Error: ' . print_r($this->arrErr, true));
        }

        // ファイルリストを取得
        $this->arrCSSList = $this->getCSSList($css_dir);

        $this->tpl_subtitle = $this->arrDeviceType[$this->device_type_id] . '＞' . $this->tpl_subtitle;
        $this->arrForm = $objFormParam->getFormParamList();
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
        $objFormParam->addParam('CSSファイル名', 'css_name', STEXT_LEN, 'a', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('旧CSSファイル名', 'old_css_name', STEXT_LEN, 'a', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('CSSデータ', 'css_data');
    }

    /**
     * 登録を実行する.
     *
     * ファイルの作成に失敗した場合は, エラーメッセージを出力する.
     *
     * @param  string  $css_dir      CSS ディレクトリ
     * @param  string  $css_name     CSSファイル名
     * @param  string  $old_css_name 旧CSSファイル名
     * @param  string  $css_path     CSSファイルの絶対パス
     * @param  string  $css_data     書き込みを行うデータ
     * @return boolean 登録が成功した場合 true; 失敗した場合 false
     */
    public function doRegister($css_dir, $css_name, $old_css_name, $css_path, $css_data)
    {
        if (!Utils::isBlank($old_css_name)
            && $old_css_name != $css_name) {
            if (!unlink($css_dir . $old_css_name . '.css')) {
                $this->arrErr['err'] = '※ 旧ファイルの削除に失敗しました<br />';

                return false;
            }
        }

        if (!Application::alias('eccube.helper.file_manager')->sfWriteFile($css_path, $css_data)) {
            $this->arrErr['err'] = '※ CSSの書き込みに失敗しました<br />';

            return false;
        }

        return true;
    }

    /**
     * 削除を実行する.
     *
     * @param  string  $css_path CSSファイルの絶対パス
     * @return boolean 削除が成功した場合 true; 失敗した場合 false
     */
    public function doDelete($css_path)
    {
        if (!unlink($css_path)) {
            $this->arrErr['err'] = '※ CSSの削除に失敗しました<br />';

            return false;
        }

        return true;
    }

    /**
     * CSSファイルのリストを取得.
     *
     * @param  string $css_dir CSSディレクトリ
     * @return array ファイルリスト
     */
    public function getCSSList($css_dir)
    {
        /* @var $objFileManager FileManagerHelper */
        $objFileManager = Application::alias('eccube.helper.file_manager');

        $arrFileList = $objFileManager->sfGetFileList($css_dir);
        foreach ($arrFileList as $val) {
            if (!$val['is_dir']) {
                $arrCSSList[] = array(
                    'file_name' => $val['file_name'],
                    'css_name'  => preg_replace('/(.+)\.(.+?)$/', '$1', $val['file_name']),
                );
            }
        }

        return $arrCSSList;
    }

    /**
     * エラーチェックを行う.
     *
     * @param  FormParam $objFormParam FormParam インスタンス
     * @return array        エラーメッセージの配列
     */
    public function lfCheckError(&$objFormParam, &$arrErr)
    {
        $arrParams = $objFormParam->getHashArray();
        /* @var $objErr CheckError */
        $objErr = Application::alias('eccube.check_error', $arrParams);
        $objErr->arrErr =& $arrErr;
        $objErr->doFunc(array('CSSファイル名', 'css_name', STEXT_LEN), array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK', 'FILE_NAME_CHECK_BY_NOUPLOAD'));

        $css_name = $objFormParam->getValue('css_name');
        $old_css_name = $objFormParam->getValue('old_css_name');
        $device_type_id = $objFormParam->getValue('device_type_id', DEVICE_TYPE_PC);

        $is_error = false;
        // 重複チェック
        if (Utils::isBlank($objErr->arrErr['css_name'])) {
            $arrCSSList = $this->getCSSList($this->getCSSDir($device_type_id));
            foreach ($arrCSSList as $val) {
                if ($val['css_name'] == $css_name) {
                    if (Utils::isBlank($old_css_name)
                        || $old_css_name != $css_name) {
                        $is_error = true;
                    }
                }
            }
            if ($is_error) {
                $objErr->arrErr['css_name'] = '※ 同じファイル名のデータが存在しています。別の名称を付けてください。<br />';
            }
        }

        return $objErr->arrErr;
    }

    /**
     * CSSディレクトリを取得する.
     *
     * @param  integer $device_type_id 端末種別ID
     * @return string  CSSディレクトリ
     */
    public function getCSSDir($device_type_id)
    {
        return Application::alias('eccube.helper.page_layout')->getTemplatePath($device_type_id, true) . 'css/';
    }

    /**
     * 文字列に[./]表記がないかをチェックします
     * @param  string  $str
     * @return boolean
     */
    public function checkPath($str)
    {
        // 含む場合はfalse
        if (preg_match('|\./|', $str)) {
            return false;
        }

        return true;
    }
}
