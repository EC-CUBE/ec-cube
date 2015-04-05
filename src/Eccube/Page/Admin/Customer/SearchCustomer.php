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

namespace Eccube\Page\Admin\Customer;

use Eccube\Application;
use Eccube\Page\Admin\AbstractAdminPage;
use Eccube\Framework\FormParam;
use Eccube\Framework\Helper\CustomerHelper;
use Eccube\Framework\Util\Utils;

/**
 * Admin_Customer_SearchCustomer のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class SearchCustomer extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'customer/search_customer.tpl';
        $this->tpl_subtitle = '会員検索';
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
        // パラメーター管理クラス
        $objFormParam = Application::alias('eccube.form_param');
        // パラメーター設定
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        $objFormParam->convParam();
        // パラメーター読み込み
        $this->arrForm = $objFormParam->getFormParamList();
        // 入力パラメーターチェック
        $this->arrErr = $this->lfCheckError($objFormParam);
        if (Utils::isBlank($this->arrErr)) {
            // POSTのモードがsearchなら会員検索開始
            switch ($this->getMode()) {
                case 'search':
                    list($this->tpl_linemax, $this->arrCustomer, $this->objNavi) = $this->lfDoSearch($objFormParam->getHashArray());
                    $this->tpl_strnavi = $this->objNavi->strnavi;
                    break;
                default:
                    break;
            }
        }
        $this->setTemplate($this->tpl_mainpage);
    }

    /**
     * パラメーター情報の初期化
     *
     * @param  FormParam $objFormParam フォームパラメータークラス
     * @return void
     */
    public function lfInitParam(&$objFormParam)
    {
        Application::alias('eccube.helper.customer')->sfSetSearchParam($objFormParam);
    }

    /**
     * エラーチェック
     *
     * @param  FormParam $objFormParam フォームパラメータークラス
     * @return array エラー配列
     */
    public function lfCheckError(&$objFormParam)
    {
        return Application::alias('eccube.helper.customer')->sfCheckErrorSearchParam($objFormParam);
    }

    /**
     * 会員一覧を検索する処理
     *
     * @param  array  $arrParam 検索パラメーター連想配列
     * @return array( integer 全体件数, mixed 会員データ一覧配列, mixed PageNaviオブジェクト)
     */
    public function lfDoSearch($arrParam)
    {
        return Application::alias('eccube.helper.customer')->sfGetSearchData($arrParam);
    }
}
