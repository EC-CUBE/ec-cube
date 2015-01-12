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
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Helper\FileManagerHelper;
use Eccube\Framework\Helper\PageLayoutHelper;
use Eccube\Framework\Util\Utils;
use Eccube\Framework\Util\GcUtils;

/**
 * ヘッダ, フッタ編集 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Header extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'design/header.tpl';
        $this->header_row = 13;
        $this->footer_row = 13;
        $this->tpl_subno = 'header';
        $this->tpl_mainno = 'design';
        $this->tpl_maintitle = 'デザイン管理';
        $this->tpl_subtitle = 'ヘッダー/フッター設定';
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
        $this->arrErr = $objFormParam->checkError();
        $is_error = (!Utils::isBlank($this->arrErr));

        $this->device_type_id = $objFormParam->getValue('device_type_id', DEVICE_TYPE_PC);

        switch ($this->getMode()) {
            // 登録
            case 'regist':
                $this->arrErr = $this->lfCheckError($objFormParam, $this->arrErr);
                if (Utils::isBlank($this->arrErr)) {
                    if ($this->doRegister($objFormParam)) {
                        $this->tpl_onload = "alert('登録が完了しました。');";
                    }
                }
                break;

            default:
                break;
        }

        if (!$is_error) {
            // テキストエリアに表示
            $header_path = $this->getTemplatePath($this->device_type_id, 'header');
            $footer_path = $this->getTemplatePath($this->device_type_id, 'footer');
            if ($header_path === false || $footer_path === false) {
                $this->arrErr['err'] = '※ ファイルの取得に失敗しました<br />';
            } else {
                $this->header_data = file_get_contents($header_path);
                $this->footer_data = file_get_contents($footer_path);
            }
        } else {
            // 画面にエラー表示しないため, ログ出力
            GcUtils::gfPrintLog('Error: ' . print_r($this->arrErr, true));
        }

        //サブタイトルの追加
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
        $objFormParam->addParam('division', 'division', STEXT_LEN, 'a', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('ヘッダデータ', 'header');
        $objFormParam->addParam('フッタデータ', 'footer');
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
        $objErr->doFunc(array('division', 'division', STEXT_LEN), array('EXIST_CHECK'));

        return $objErr->arrErr;
    }

    /**
     * 登録を実行する.
     *
     * ファイルの作成に失敗した場合は, エラーメッセージを出力する.
     *
     * @param  FormParam    $objFormParam FormParam インスタンス
     * @return boolean 登録が成功した場合 true; 失敗した場合 false
     */
    public function doRegister(&$objFormParam)
    {
        $division = $objFormParam->getValue('division');
        $contents = $objFormParam->getValue($division);
        $tpl_path = $this->getTemplatePath($objFormParam->getValue('device_type_id'), $division);
        if ($tpl_path === false
            || !Application::alias('eccube.helper.file_manager')->sfWriteFile($tpl_path, $contents)) {
            $this->arrErr['err'] = '※ ファイルの書き込みに失敗しました<br />';

            return false;
        }

        return true;
    }

    /**
     * テンプレートパスを取得する.
     *
     * @param  integer        $device_type_id 端末種別ID
     * @param  string         $division       'header' or 'footer'
     * @return string|false 成功した場合, テンプレートのパス; 失敗した場合 false
     */
    public function getTemplatePath($device_type_id, $division)
    {
        $tpl_path = Application::alias('eccube.helper.page_layout')->getTemplatePath($device_type_id) . '/' . $division . '.tpl';
        if (file_exists($tpl_path)) {
            return $tpl_path;
        } else {
            return false;
        }
    }
}
