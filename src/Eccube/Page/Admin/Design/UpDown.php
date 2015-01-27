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
use Eccube\Framework\UploadFile;
use Eccube\Framework\Query;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Helper\FileManagerHelper;
use Eccube\Framework\Util\Utils;

/**
 * テンプレートアップロード のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class UpDown extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'design/up_down.tpl';
        $this->tpl_subno    = 'up_down';
        $this->tpl_mainno   = 'design';
        $this->tpl_maintitle = 'デザイン管理';
        $this->tpl_subtitle = 'テンプレート追加';
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
     * FIXME ロジックを見直し
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

        switch ($this->getMode()) {
            // アップロードボタン押下時の処理
            case 'upload':
                $objUpFile = $this->lfInitUploadFile($objFormParam);
                $this->arrErr = $this->lfCheckError($objFormParam, $objUpFile);
                if (Utils::isBlank($this->arrErr)) {
                    if ($this->doUpload($objFormParam, $objUpFile)) {
                        $this->tpl_onload = "alert('テンプレートファイルをアップロードしました。');";
                        $objFormParam->setValue('template_name', '');
                        $objFormParam->setValue('template_code', '');
                    }
                }
                break;

            default:
                break;
        }
        //サブタイトルの追加
        $this->tpl_subtitle = $this->arrDeviceType[$this->device_type_id] . '＞' . $this->tpl_subtitle;
        $this->arrForm = $objFormParam->getFormParamList();
    }

    /**
     * UploadFileクラスの初期化.
     *
     * @param  FormParam $objForm FormParamのインスタンス
     * @return UploadFile UploadFileのインスタンス
     */
    public function lfInitUploadFile($objForm)
    {
        $pkg_dir = SMARTY_TEMPLATES_REALDIR . $objForm->getValue('template_code');
        $objUpFile = new UploadFile(TEMPLATE_TEMP_REALDIR, $pkg_dir);
        $objUpFile->addFile('テンプレートファイル', 'template_file', array(), TEMPLATE_SIZE, true, 0, 0, false);

        return $objUpFile;
    }

    /**
     * FormParamクラスの初期化.
     *
     * @param  FormParam $objFormParam FormParamのインスタンス
     * @return void
     */
    public function lfInitParam(&$objFormParam)
    {
        $objFormParam->addParam('テンプレートコード', 'template_code', STEXT_LEN, 'a', array('EXIST_CHECK', 'SPTAB_CHECK','MAX_LENGTH_CHECK', 'ALNUM_CHECK'));
        $objFormParam->addParam('テンプレート名', 'template_name', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'SPTAB_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam('端末種別ID', 'device_type_id', INT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
    }

    /**
     * uploadモードのパラメーター検証を行う.
     *
     * @param  FormParam $objFormParam FormParamのインスタンス
     * @param  UploadFile $objUpFile    UploadFileのインスタンス
     * @return array  エラー情報を格納した連想配列, エラーが無ければ(多分)nullを返す
     */
    public function lfCheckError(&$objFormParam, &$objUpFile)
    {
        $arrErr = $objFormParam->checkError();
        $template_code = $objFormParam->getValue('template_code');

        // 同名のフォルダが存在する場合はエラー
        if (file_exists(USER_TEMPLATE_REALDIR . $template_code) && $template_code != "") {
            $arrErr['template_code'] = '※ 同名のファイルがすでに存在します。<br/>';
        }

        // 登録不可の文字列チェック
        $arrIgnoreCode = array('admin',
                               MOBILE_DEFAULT_TEMPLATE_NAME,
                               SMARTPHONE_DEFAULT_TEMPLATE_NAME,
                               DEFAULT_TEMPLATE_NAME);
        if (in_array($template_code, $arrIgnoreCode)) {
            $arrErr['template_code'] = '※ このテンプレートコードは使用できません。<br/>';
        }

        // DBにすでに登録されていないかチェック
        $objQuery = Application::alias('eccube.query');
        $exists = $objQuery->exists('dtb_templates', 'template_code = ?', array($template_code));
        if ($exists) {
            $arrErr['template_code'] = '※ すでに登録されているテンプレートコードです。<br/>';
        }

        /*
         * ファイル形式チェック
         * ファイルが壊れていることも考慮して, 展開可能かチェックする.
         */
        $tar = new Archive_Tar($_FILES['template_file']['tmp_name'], true);
        $arrArchive = $tar->listContent();
        if (!is_array($arrArchive)) {
            $arrErr['template_file'] = '※ テンプレートファイルが解凍できません。許可されている形式は、tar/tar.gzです。<br />';
        } else {
            $make_temp_error = $objUpFile->makeTempFile('template_file', false);
            if (!Utils::isBlank($make_temp_error)) {
                $arrErr['template_file'] = $make_temp_error;
            }
        }

        return $arrErr;
    }

    /**
     * DBおよびファイルシステムにテンプレートパッケージを追加する.
     *
     * エラーが発生した場合は, エラーを出力し, false を返す.
     *
     * @param  FormParam  $objFormParam FormParamのインスタンス
     * @param  UploadFile  $objUpFile    UploadFileのインスタンス
     * @return boolean 成功した場合 true; 失敗した場合 false
     */
    public function doUpload($objFormParam, $objUpFile)
    {
        $template_code = $objFormParam->getValue('template_code');
        $template_name = $objFormParam->getValue('template_name');
        $device_type_id = $objFormParam->getValue('device_type_id');

        $template_dir = SMARTY_TEMPLATES_REALDIR . $template_code;
        $compile_dir  = DATA_REALDIR . 'Smarty/templates_c/' . $template_code;

        $objQuery = Application::alias('eccube.query');
        $objQuery->begin();

        $arrValues = array(
            'template_code' => $template_code,
            'device_type_id' => $device_type_id,
            'template_name' => $template_name,
            'create_date' => 'CURRENT_TIMESTAMP',
            'update_date' => 'CURRENT_TIMESTAMP',
        );
        $objQuery->insert('dtb_templates', $arrValues);

        // フォルダ作成
        if (!file_exists($template_dir)) {
            if (!mkdir($template_dir)) {
                $this->arrErr['err'] = '※ テンプレートフォルダが作成できませんでした。<br/>';
                $objQuery->rollback();

                return false;
            }
        }
        if (!file_exists($compile_dir)) {
            if (!mkdir($compile_dir)) {
                $this->arrErr['err'] = '※ Smarty コンパイルフォルダが作成できませんでした。<br/>';
                $objQuery->rollback();

                return false;
            }
        }

        // 一時フォルダから保存ディレクトリへ移動
        $objUpFile->moveTempFile();

        // 解凍
        if (!Application::alias('eccube.helper.file_manager')->unpackFile($template_dir . '/' . $_FILES['template_file']['name'])) {
            $this->arrErr['err'] = '※ テンプレートファイルの解凍に失敗しました。<br/>';
            $objQuery->rollback();

            return false;
        }
        // ユーザデータの下のファイルをコピーする
        $from_dir = SMARTY_TEMPLATES_REALDIR . $template_code . '/_packages/';
        $to_dir = USER_REALDIR . 'packages/' . $template_code . '/';
        if (!Utils::recursiveMkdir($to_dir)) {
            $this->arrErr['err'] = '※ ' . $to_dir . ' の作成に失敗しました。<br/>';
            $objQuery->rollback();

            return false;
        }
        Utils::sfCopyDir($from_dir, $to_dir);

        $objQuery->commit();

        return true;
    }
}
