<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * お届け先指定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Shopping_Deliv extends LC_Page {

    // {{{ properties

    /** フォームパラメータの配列 */
    var $objFormParam;

    /** ログインフォームパラメータ配列 */
    var $objLoginFormParam;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'shopping/deliv.tpl';
        $this->tpl_css = URL_DIR.'css/layout/shopping/index.css';
        $masterData = new SC_DB_MasterData();
        $this->arrPref = $masterData->getMasterData("mtb_pref", array("pref_id", "pref_name", "rank"));
        $this->tpl_title = "お届け先指定";

        $this->allowClientCache();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objView = new SC_SiteView();
        $objSiteSess = new SC_SiteSession();
        $objCartSess = new SC_CartSession();
        $objCampaignSess = new SC_CampaignSession();
        $objCustomer = new SC_Customer();
        // クッキー管理クラス
        $objCookie = new SC_Cookie(COOKIE_EXPIRE);
        // パラメータ管理クラス
        $this->objFormParam = new SC_FormParam();
        // パラメータ情報の初期化
        $this->lfInitParam();
        // POST値の取得
        $this->objFormParam->setParam($_POST);

        $this->objLoginFormParam = new SC_FormParam();	// ログインフォーム用
        $this->lfInitLoginFormParam();						// 初期設定
        $this->objLoginFormParam->setParam($_POST);		// POST値の取得

        // ユーザユニークIDの取得と購入状態の正当性をチェック
        $uniqid = SC_Utils_Ex::sfCheckNormalAccess($objSiteSess, $objCartSess);
        $objPage->tpl_uniqid = $uniqid;

        // ログインチェック
        if($_POST['mode'] != 'login' && !$objCustomer->isLoginSuccess()) {
            // 不正アクセスとみなす
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        }

        switch($_POST['mode']) {
        case 'login':
            $this->objLoginFormParam->toLower('login_email');
            $objPage->arrErr = $this->objLoginFormParam->checkError();
            $arrForm =  $this->objLoginFormParam->getHashArray();
            // クッキー保存判定
            if($arrForm['login_memory'] == "1" && $arrForm['login_email'] != "") {
                $objCookie->setCookie('login_email', $_POST['login_email']);
            } else {
                $objCookie->setCookie('login_email', '');
            }

            if(count($objPage->arrErr) == 0) {
                // ログイン判定
                if(!$objCustomer->getCustomerDataFromEmailPass($arrForm['login_pass'], $arrForm['login_email'])) {
                    // 仮登録の判定
                    $objQuery = new SC_Query;
                    $where = "email = ? AND status = 1 AND del_flg = 0";
                    $ret = $objQuery->count("dtb_customer", $where, array($arrForm['login_email']));

                    if($ret > 0) {
                        SC_Utils_Ex::sfDispSiteError(TEMP_LOGIN_ERROR);
                    } else {
                        SC_Utils_Ex::sfDispSiteError(SITE_LOGIN_ERROR);
                    }
                }
            } else {
                // ログインページに戻る
                $this->sendRedirect(URL_SHOP_TOP);
                exit;
            }
            break;
        // 削除
        case 'delete':
            if (sfIsInt($_POST['other_deliv_id'])) {
                $objQuery = new SC_Query();
                $where = "other_deliv_id = ?";
                $arrRet = $objQuery->delete("dtb_other_deliv", $where, array($_POST['other_deliv_id']));
                $this->objFormParam->setValue('select_addr_id', '');
            }
            break;
        // 会員登録住所に送る
        case 'customer_addr':
            // 会員登録住所がチェックされている場合
            if ($_POST['deliv_check'] == '-1') {
                // 会員情報の住所を受注一時テーブルに書き込む
                $this->lfRegistDelivData($uniqid, $objCustomer);
                // 正常に登録されたことを記録しておく
                $objSiteSess->setRegistFlag();
                // お支払い方法選択ページへ移動
                $this->sendRedirect($this->getLocation(URL_SHOP_PAYMENT, array(), true));
                exit;
            // 別のお届け先がチェックされている場合
            } elseif($_POST['deliv_check'] >= 1) {
                if (sfIsInt($_POST['deliv_check'])) {
                    // 登録済みの別のお届け先を受注一時テーブルに書き込む
                    lfRegistOtherDelivData($uniqid, $objCustomer, $_POST['deliv_check']);
                    // 正常に登録されたことを記録しておく
                    $objSiteSess->setRegistFlag();
                    // お支払い方法選択ページへ移動
                    $this->sendRedirect($this->getLocation(URL_SHOP_PAYMENT, array(), true));
                    exit;
                }
            }else{
                // エラーを返す
                $arrErr['deli'] = '※ お届け先を選択してください。';
            }
            break;
        // 前のページに戻る
        case 'return':
            // 確認ページへ移動
            $this->sendRedirect($this->getLocation(URL_CART_TOP, array(), true));
            exit;
            break;
        default:
            $objQuery = new SC_Query();
            $where = "order_temp_id = ?";
            $arrRet = $objQuery->select("*", "dtb_order_temp", $where, array($uniqid));
            $this->objFormParam->setParam($arrRet[0]);
            break;
        }

        /** 表示処理 **/

        // 会員登録住所の取得
        $col = "name01, name02, pref, addr01, addr02";
        $where = "customer_id = ?";
        $objQuery = new SC_Query();
        $arrCustomerAddr = $objQuery->select($col, "dtb_customer", $where, array($_SESSION['customer']['customer_id']));
        // 別のお届け先住所の取得
        $col = "other_deliv_id, name01, name02, pref, addr01, addr02";
        $objQuery->setorder("other_deliv_id DESC");
        $objOtherAddr = $objQuery->select($col, "dtb_other_deliv", $where, array($_SESSION['customer']['customer_id']));
        $objPage->arrAddr = $arrCustomerAddr;
        $objPage->tpl_addrmax = count($objOtherAddr);
        $cnt = 1;
        foreach($objOtherAddr as $val) {
            $objPage->arrAddr[$cnt] = $val;
            $cnt++;
        }

        // 入力値の取得
        $objPage->arrForm = $this->objFormParam->getFormParamList();
        $objPage->arrErr = $arrErr;

        $objView->assignobj($objPage);
        // フレームを選択(キャンペーンページから遷移なら変更)
        $objCampaignSess->pageView($objView);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /* パラメータ情報の初期化 */
    function lfInitParam() {
        $this->objFormParam->addParam("お名前1", "deliv_name01", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("お名前2", "deliv_name02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("フリガナ1", "deliv_kana01", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("フリガナ2", "deliv_kana02", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("郵便番号1", "deliv_zip01", ZIP01_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
        $this->objFormParam->addParam("郵便番号2", "deliv_zip02", ZIP02_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
        $this->objFormParam->addParam("都道府県", "deliv_pref", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("住所1", "deliv_addr01", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("住所2", "deliv_addr02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("電話番号1", "deliv_tel01", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $this->objFormParam->addParam("電話番号2", "deliv_tel02", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $this->objFormParam->addParam("電話番号3", "deliv_tel03", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $this->objFormParam->addParam("", "deliv_check");
    }

    function lfInitLoginFormParam() {
        $this->objLoginFormParam->addParam("記憶する", "login_memory", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objLoginFormParam->addParam("メールアドレス", "login_email", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        $this->objLoginFormParam->addParam("パスワード", "login_pass", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
    }

    /* DBへデータの登録 */
    function lfRegistNewAddrData($uniqid, $objCustomer) {
        $arrRet = $this->objFormParam->getHashArray();
        $sqlval = $this->objFormParam->getDbArray();
        // 登録データの作成
        $sqlval['deliv_check'] = '1';
        $sqlval['order_temp_id'] = $uniqid;
        $sqlval['update_date'] = 'Now()';
        $sqlval['customer_id'] = $objCustomer->getValue('customer_id');
        $sqlval['order_birth'] = $objCustomer->getValue('birth');

        $objDb = new SC_Helper_DB_Ex();
        $objDb->sfRegistTempOrder($uniqid, $sqlval);
    }

    /* 会員情報の住所を一時受注テーブルへ */
    function lfRegistDelivData($uniqid, $objCustomer) {
        // 登録データの作成
        $sqlval['order_temp_id'] = $uniqid;
        $sqlval['update_date'] = 'Now()';
        $sqlval['customer_id'] = $objCustomer->getValue('customer_id');
        $sqlval['deliv_check'] = '-1';
        $sqlval['deliv_name01'] = $objCustomer->getValue('name01');
        $sqlval['deliv_name02'] = $objCustomer->getValue('name02');
        $sqlval['deliv_kana01'] = $objCustomer->getValue('kana01');
        $sqlval['deliv_kana02'] = $objCustomer->getValue('kana02');
        $sqlval['deliv_zip01'] = $objCustomer->getValue('zip01');
        $sqlval['deliv_zip02'] = $objCustomer->getValue('zip02');
        $sqlval['deliv_pref'] = $objCustomer->getValue('pref');
        $sqlval['deliv_addr01'] = $objCustomer->getValue('addr01');
        $sqlval['deliv_addr02'] = $objCustomer->getValue('addr02');
        $sqlval['deliv_tel01'] = $objCustomer->getValue('tel01');
        $sqlval['deliv_tel02'] = $objCustomer->getValue('tel02');
        $sqlval['deliv_tel03'] = $objCustomer->getValue('tel03');

        $sqlval['deliv_fax01'] = $objCustomer->getValue('fax01');
        $sqlval['deliv_fax02'] = $objCustomer->getValue('fax02');
        $sqlval['deliv_fax03'] = $objCustomer->getValue('fax03');

        $objDb = new SC_Helper_DB_Ex();
        $objDb->sfRegistTempOrder($uniqid, $sqlval);
    }

    /* 別のお届け先住所を一時受注テーブルへ */
    function lfRegistOtherDelivData($uniqid, $objCustomer, $other_deliv_id) {
        // 登録データの作成
        $sqlval['order_temp_id'] = $uniqid;
        $sqlval['update_date'] = 'Now()';
        $sqlval['customer_id'] = $objCustomer->getValue('customer_id');
        $sqlval['order_birth'] = $objCustomer->getValue('birth');

        $objQuery = new SC_Query();
        $where = "other_deliv_id = ?";
        $arrRet = $objQuery->select("*", "dtb_other_deliv", $where, array($other_deliv_id));

        $sqlval['deliv_check'] = $other_deliv_id;
        $sqlval['deliv_name01'] = $arrRet[0]['name01'];
        $sqlval['deliv_name02'] = $arrRet[0]['name02'];
        $sqlval['deliv_kana01'] = $arrRet[0]['kana01'];
        $sqlval['deliv_kana02'] = $arrRet[0]['kana02'];
        $sqlval['deliv_zip01'] = $arrRet[0]['zip01'];
        $sqlval['deliv_zip02'] = $arrRet[0]['zip02'];
        $sqlval['deliv_pref'] = $arrRet[0]['pref'];
        $sqlval['deliv_addr01'] = $arrRet[0]['addr01'];
        $sqlval['deliv_addr02'] = $arrRet[0]['addr02'];
        $sqlval['deliv_tel01'] = $arrRet[0]['tel01'];
        $sqlval['deliv_tel02'] = $arrRet[0]['tel02'];
        $sqlval['deliv_tel03'] = $arrRet[0]['tel03'];
        $objDb = new SC_Helper_DB_Ex();
        $objDb->sfRegistTempOrder($uniqid, $sqlval);
    }

    /* 入力内容のチェック */
    function lfCheckError() {
        // 入力データを渡す。
        $arrRet =  $this->objFormParam->getHashArray();
        $objErr = new SC_CheckError($arrRet);
        $objErr->arrErr = $this->objFormParam->checkError();
        // 複数項目チェック
        if ($_POST['mode'] == 'login'){
            $objErr->doFunc(array("メールアドレス", "login_email", STEXT_LEN), array("EXIST_CHECK"));
            $objErr->doFunc(array("パスワード", "login_pass", STEXT_LEN), array("EXIST_CHECK"));
        }
        $objErr->doFunc(array("TEL", "deliv_tel01", "deliv_tel02", "deliv_tel03", TEL_ITEM_LEN), array("TEL_CHECK"));
        return $objErr->arrErr;
    }
}
?>
