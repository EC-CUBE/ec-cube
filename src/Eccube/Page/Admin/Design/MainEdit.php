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
use Eccube\Framework\Query;
use Eccube\Framework\Response;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Helper\FileManagerHelper;
use Eccube\Framework\Helper\PageLayoutHelper;
use Eccube\Framework\Util\Utils;
use Eccube\Framework\Util\GcUtils;

/**
 * メイン編集 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class MainEdit extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'design/main_edit.tpl';
        $this->text_row     = 13;
        $this->tpl_subno = 'main_edit';
        $this->tpl_mainno = 'design';
        $this->tpl_maintitle = 'デザイン管理';
        $this->tpl_subtitle = 'ページ詳細設定';
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
        $is_error = (!Utils::isBlank($this->arrErr));

        $this->device_type_id = $objFormParam->getValue('device_type_id', DEVICE_TYPE_PC);
        $this->page_id = $objFormParam->getValue('page_id');

        switch ($this->getMode()) {
            // 削除
            case 'delete':
                if (!$is_error) {
                    if ($objLayout->isEditablePage($this->device_type_id, $this->page_id)) {
                        $objLayout->lfDelPageData($this->page_id, $this->device_type_id);

                        Application::alias('eccube.response')->reload(array('device_type_id' => $this->device_type_id,
                                                     'msg' => 'on'), true);
                        Application::alias('eccube.response')->actionExit();
                    }
                }
                break;

            // 登録/編集
            case 'confirm':
                if (!$is_error) {
                    $this->arrErr = $this->lfCheckError($objFormParam, $this->arrErr);
                    if (Utils::isBlank($this->arrErr)) {
                        $result = $this->doRegister($objFormParam, $objLayout);
                        if ($result !== false) {
                            $arrQueryString = array(
                                'device_type_id' => $this->device_type_id,
                                'page_id' => $result,
                                'msg' => 'on',
                            );
                            Application::alias('eccube.response')->reload($arrQueryString, true);
                            Application::alias('eccube.response')->actionExit();
                        }
                    }
                }
                break;

            default:
                if (isset($_GET['msg']) && $_GET['msg'] == 'on') {
                    $this->tpl_onload = "alert('登録が完了しました。');";
                }
                break;
        }

        if (!$is_error) {
            $this->arrPageList = $objLayout->getPageProperties($this->device_type_id, null);
            // page_id が指定されている場合にはテンプレートデータの取得
            if (!Utils::isBlank($this->page_id)) {
                $arrPageData = $this->getTplMainpage($this->device_type_id, $this->page_id, $objLayout);
                $objFormParam->setParam($arrPageData);
            }
        } else {
            // 画面にエラー表示しないため, ログ出力
            GcUtils::gfPrintLog('Error: ' . print_r($this->arrErr, true));
        }
        $this->tpl_subtitle = $this->arrDeviceType[$this->device_type_id] . '＞' . $this->tpl_subtitle;
        $this->arrForm = $objFormParam->getFormParamList();
    }

    /**
     * パラメーター情報の初期化
     *
     * XXX URL のフィールドは, 実際は filename なので注意
     *
     * @param  FormParam $objFormParam FormParamインスタンス
     * @return void
     */
    public function lfInitParam(&$objFormParam)
    {
        $objFormParam->addParam('ページID', 'page_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('端末種別ID', 'device_type_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('名称', 'page_name', STEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('URL', 'filename', STEXT_LEN, 'a', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('ヘッダチェック', 'header_chk', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('フッタチェック', 'footer_chk', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('修正フラグ', 'edit_flg', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('TPLデータ', 'tpl_data');
        $objFormParam->addParam('meta タグ:author', 'author', MTEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('meta タグ:description', 'description', MTEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('meta タグ:keyword', 'keyword', MTEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('meta タグ:robots', 'meta_robots', MTEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
    }

    /**
     * ページデータを取得する.
     *
     * @param  integer              $device_type_id 端末種別ID
     * @param  integer              $page_id        ページID
     * @param  PageLayoutHelper $objLayout      PageLayoutHelper インスタンス
     * @return array                ページデータの配列
     */
    public function getTplMainpage($device_type_id, $page_id, &$objLayout)
    {
        $arrPageData = $objLayout->getPageProperties($device_type_id, $page_id);

        $templatePath = $objLayout->getTemplatePath($device_type_id);
        $filename = $templatePath . $arrPageData[0]['filename'] . '.tpl';
        if (file_exists($filename)) {
            $arrPageData[0]['tpl_data'] = file_get_contents($filename);
        }
        // ファイル名を画面表示用に加工しておく
        $arrPageData[0]['filename'] = preg_replace('|^' . preg_quote(USER_DIR) . '|', '', $arrPageData[0]['filename']);

        return $arrPageData[0];
    }

    /**
     * 登録を実行する.
     *
     * ファイルの作成に失敗した場合は, エラーメッセージを出力し,
     * データベースをロールバックする.
     *
     * @param  FormParam         $objFormParam FormParam インスタンス
     * @param  PageLayoutHelper $objLayout    PageLayoutHelper インスタンス
     * @return integer|boolean      登録が成功した場合, 登録したページID;
     *                         失敗した場合 false
     */
    public function doRegister(&$objFormParam, &$objLayout)
    {
        $filename = $objFormParam->getValue('filename');
        $arrParams['device_type_id'] = $objFormParam->getValue('device_type_id');
        $arrParams['page_id'] = $objFormParam->getValue('page_id');
        $arrParams['header_chk'] = intval($objFormParam->getValue('header_chk')) === 1 ? 1 : 2;
        $arrParams['footer_chk'] = intval($objFormParam->getValue('footer_chk')) === 1 ? 1 : 2;
        $arrParams['tpl_data'] = $objFormParam->getValue('tpl_data');
        $arrParams['page_name'] = $objFormParam->getValue('page_name');
        $arrParams['url'] = USER_DIR . $filename . '.php';
        $arrParams['filename'] = USER_DIR . $filename;
        $arrParams['author']        = $objFormParam->getValue('author');
        $arrParams['description']   = $objFormParam->getValue('description');
        $arrParams['keyword']       = $objFormParam->getValue('keyword');
        $arrParams['meta_robots']   = $objFormParam->getValue('meta_robots');

        $objQuery = Application::alias('eccube.query');
        $objQuery->begin();

        $page_id = $this->registerPage($arrParams, $objLayout);

        /*
         * 新規登録時
         * or 編集可能な既存ページ編集時かつ, PHP ファイルが存在しない場合に,
         * PHP ファイルを作成する.
         */
        if (Utils::isBlank($arrParams['page_id'])
            || $objLayout->isEditablePage($arrParams['device_type_id'], $arrParams['page_id'])) {
            if (!$this->createPHPFile($filename)) {
                $this->arrErr['err'] = '※ PHPファイルの作成に失敗しました<br />';
                $objQuery->rollback();

                return false;
            }
            // 新規登録時のみ $page_id を代入
            $arrParams['page_id'] = $page_id;
        }

        if ($objLayout->isEditablePage($arrParams['device_type_id'], $page_id)) {
            $tpl_path = $objLayout->getTemplatePath($arrParams['device_type_id']) . $arrParams['filename'] . '.tpl';
        } else {
            $tpl_path = $objLayout->getTemplatePath($arrParams['device_type_id']) . $filename . '.tpl';
        }

        if (!Application::alias('eccube.helper.file_manager')->sfWriteFile($tpl_path, $arrParams['tpl_data'])) {
            $this->arrErr['err'] = '※ TPLファイルの書き込みに失敗しました<br />';
            $objQuery->rollback();

            return false;
        }

        $objQuery->commit();

        return $arrParams['page_id'];
    }

    /**
     * 入力内容をデータベースに登録する.
     *
     * @param  array                $arrParams フォームパラメーターの配列
     * @param  PageLayoutHelper $objLayout PageLayoutHelper インスタンス
     * @return integer              ページID
     */
    public function registerPage($arrParams, &$objLayout)
    {
        $objQuery = Application::alias('eccube.query');

        // ページIDが空の場合は新規登録
        $is_new = Utils::isBlank($arrParams['page_id']);
        // 既存ページの存在チェック
        if (!$is_new) {
            $arrExists = $objLayout->getPageProperties($arrParams['device_type_id'], $arrParams['page_id']);
        }

        $table = 'dtb_pagelayout';
        $arrValues = $objQuery->extractOnlyColsOf($table, $arrParams);
        $arrValues['update_url'] = $_SERVER['HTTP_REFERER'];
        $arrValues['update_date'] = 'CURRENT_TIMESTAMP';

        // 新規登録
        if ($is_new || Utils::isBlank($arrExists)) {
            $objQuery->setOrder('');
            $arrValues['page_id'] = 1 + $objQuery->max('page_id', $table, 'device_type_id = ?',
                                                       array($arrValues['device_type_id']));
            $arrValues['create_date'] = 'CURRENT_TIMESTAMP';
            $objQuery->insert($table, $arrValues);
        // 更新
        } else {
            // 編集不可ページは更新しない
            if (!$objLayout->isEditablePage($arrValues['device_type_id'], $arrValues['page_id'])) {
                unset($arrValues['page_name']);
                unset($arrValues['filename']);
                unset($arrValues['url']);
            }

            $objQuery->update($table, $arrValues, 'page_id = ? AND device_type_id = ?',
                              array($arrValues['page_id'], $arrValues['device_type_id']));
        }

        return $arrValues['page_id'];
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
        $objErr->doFunc(array('名称', 'page_name', STEXT_LEN), array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objErr->doFunc(array('URL', 'filename', STEXT_LEN), array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));

        /*
         * URL チェック
         * ここでチェックするのは, パスのみなので CheckError::URL_CHECK()
         * は使用しない
         */
        $valid_url = true;
        foreach (explode('/', $arrParams['filename']) as $val) {
            if (!preg_match('/^[a-zA-Z0-9:_~\.\-]+$/', $val)) {
                $valid_url = false;
            }
            if ($val == '.' || $val == '..') {
                $valid_url = false;
            }
        }
        if (!$valid_url) {
            $objErr->arrErr['filename'] = '※ URLを正しく入力してください。<br />';
        }
        // 同一URLの存在チェック
        $where = 'page_id <> 0 AND device_type_id = ? AND filename = ?';
        $arrValues = array($arrParams['device_type_id'], USER_DIR . $arrParams['filename']);
        // 変更の場合は自 URL を除外
        if (!Utils::isBlank($arrParams['page_id'])) {
            $where .= ' AND page_id <> ?';
            $arrValues[] = $arrParams['page_id'];
        }

        $objQuery = Application::alias('eccube.query');
        $exists = $objQuery->exists('dtb_pagelayout', $where, $arrValues);
        if ($exists) {
            $objErr->arrErr['filename'] = '※ 同じURLのデータが存在しています。別のURLを入力してください。<br />';
        }

        return $objErr->arrErr;
    }

    /**
     * PHP ファイルを生成する.
     *
     * 既に同名の PHP ファイルが存在する場合は何もせず true を返す.(#831)
     *
     * @param  string  $filename フォームパラメーターの filename
     * @return boolean 作成に成功した場合 true
     */
    public function createPHPFile($filename)
    {
        $path = USER_REALDIR . $filename . '.php';

        if (file_exists($path)) {
            return true;
        }

        if (file_exists(USER_DEF_PHP_REALFILE)) {
            $php_contents = file_get_contents(USER_DEF_PHP_REALFILE);
        } else {
            return false;
        }

        // require.php の PATH を書き換える
        $defaultStrings = "exit; // Don't rewrite. This line is rewritten by EC-CUBE.";
        $replaceStrings = "require_once '" . str_repeat('../', substr_count($filename, '/')) . "../require.php';";
        $php_contents = str_replace($defaultStrings, $replaceStrings, $php_contents);

        return Application::alias('eccube.helper.file_manager')->sfWriteFile($path, $php_contents);
    }
}
