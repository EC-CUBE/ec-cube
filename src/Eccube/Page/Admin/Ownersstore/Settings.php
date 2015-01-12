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

namespace Eccube\Page\Admin\OwnersStore;

use Eccube\Application;
use Eccube\Page\Admin\AbstractAdminPage;
use Eccube\Framework\FormParam;
use Eccube\Framework\Query;

/**
 * オーナーズストア：認証キー設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Settings extends AbstractAdminPage
{
    /** FormParamのインスタンス */
    public $objForm;

    /** リクエストパラメーターを格納する連想配列 */
    public $arrForm;

    /** バリデーションエラー情報を格納する連想配列 */
    public $arrErr;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        $this->tpl_mainpage = 'ownersstore/settings.tpl';
        $this->tpl_mainno   = 'ownersstore';
        $this->tpl_subno    = 'settings';
        $this->tpl_maintitle = 'オーナーズストア';
        $this->tpl_subtitle = '認証キー設定';
        $this->httpCacheControl('nocache');
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
        switch ($this->getMode()) {
            // 入力内容をDBへ登録する
            case 'register':
                $this->execRegisterMode();
                break;
            // 初回表示
            default:
                $this->execDefaultMode();
        }
    }

    /**
     * registerアクションの実行.
     * 入力内容をDBへ登録する.
     *
     * @param void
     * @return void
     */
    public function execRegisterMode()
    {
        // パラメーターオブジェクトの初期化
        $this->initRegisterMode();
        // POSTされたパラメーターの検証
        $arrErr = $this->validateRegistermode();

        // エラー時の処理
        if (!empty($arrErr)) {
            $this->arrErr  = $arrErr;
            $this->arrForm = $this->objForm->getHashArray();

            return;
        }

        // エラーがなければDBへ登録
        $arrForm = $this->objForm->getHashArray();
        $this->registerOwnersStoreSettings($arrForm);

        $this->arrForm = $arrForm;

        $this->tpl_onload = "alert('登録しました。')";
    }

    /**
     * registerアクションの初期化.
     * FormParamを初期化しメンバ変数にセットする.
     *
     * @param void
     * @return void
     */
    public function initRegisterMode()
    {
        // 前後の空白を削除
        if (isset($_POST['public_key'])) {
            $_POST['public_key'] = trim($_POST['public_key']);
        }

        $objForm = Application::alias('eccube.form_param');
        $objForm->addParam('認証キー', 'public_key', LTEXT_LEN, '', array('EXIST_CHECK', 'ALNUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objForm->setParam($_POST);

        $this->objForm = $objForm;
    }

    /**
     * registerアクションのパラメーターを検証する.
     *
     * @param void
     * @return array エラー情報を格納した連想配列
     */
    public function validateRegistermode()
    {
        return $this->objForm->checkError();
    }

    /**
     * defaultアクションの実行.
     * DBから登録内容を取得し表示する.
     *
     * @param void
     * @return void
     */
    public function execDefaultMode()
    {
        $this->arrForm = $this->getOwnersStoreSettings();
    }

    /**
     * DBへ入力内容を登録する.
     *
     * @param  array $arrSettingsData ｵｰﾅｰｽﾞｽﾄｱ設定の連想配列
     * @return void
     */
    public function registerOwnersStoreSettings($arrSettingsData)
    {
        $table = 'dtb_ownersstore_settings';
        $objQuery = Application::alias('eccube.query');
        $exists = $objQuery->exists($table);

        if ($exists) {
            $objQuery->update($table, $arrSettingsData);
        } else {
            $objQuery->insert($table, $arrSettingsData);
        }
    }

    /**
     * DBから登録内容を取得する.
     *
     * @param void
     * @return array
     */
    public function getOwnersStoreSettings()
    {
        $table   = 'dtb_ownersstore_settings';
        $colmuns = '*';

        $objQuery = Application::alias('eccube.query');
        $arrRet = $objQuery->select($colmuns, $table);

        if (isset($arrRet[0])) return $arrRet[0];
        return array();
    }
}
