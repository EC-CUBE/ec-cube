<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * 入力内容確認のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_Shopping_Confirm.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class LC_Page_Shopping_Confirm extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'shopping/confirm.tpl';
        $this->tpl_css = URL_DIR.'css/layout/shopping/confirm.css';
        $this->tpl_title = "ご入力内容のご確認";
        $masterData = new SC_DB_MasterData();
        $this->arrPref = $masterData->getMasterData("mtb_pref", array("pref_id", "pref_name", "rank"));
        $this->arrSex = $masterData->getMasterData("mtb_sex");
        $this->arrMAILMAGATYPE = $masterData->getMasterData("mtb_mail_magazine_type");
        $this->arrReminder = $masterData->getMasterData("mtb_reminder");

        $this->allowClientCache();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objView = new SC_SiteView();
        $objCartSess = new SC_CartSession();
        $objSiteInfo = $objView->objSiteInfo;
        $objSiteSess = new SC_SiteSession();
        $objCampaignSess = new SC_CampaignSession();
        $objCustomer = new SC_Customer();
        $arrInfo = $objSiteInfo->data;
        $objQuery = new SC_Query();
        $objDb = new SC_Helper_DB_Ex();

        // 前のページで正しく登録手続きが行われた記録があるか判定
        SC_Utils_Ex::sfIsPrePage($objSiteSess);

        // ユーザユニークIDの取得と購入状態の正当性をチェック
        $uniqid = SC_Utils_Ex::sfCheckNormalAccess($objSiteSess, $objCartSess);
        $this->tpl_uniqid = $uniqid;

        // カート集計処理
        $objDb->sfTotalCart($this, $objCartSess, $arrInfo);
        // 一時受注テーブルの読込
        $arrData = $objDb->sfGetOrderTemp($uniqid);
        // カート集計を元に最終計算
        $arrData = $objDb->sfTotalConfirm($arrData, $this, $objCartSess, $arrInfo, $objCustomer, $objCampaignSess);
        // キャンペーンからの遷移で送料が無料だった場合の処理
        if($objCampaignSess->getIsCampaign()) {
            $deliv_free_flg = $objQuery->get("dtb_campaign", "deliv_free_flg", "campaign_id = ?", array($objCampaignSess->getCampaignId()));
            // 送料無料が設定されていた場合
            if($deliv_free_flg) {
                $arrData['payment_total'] -= $arrData['deliv_fee'];
                $arrData['deliv_fee'] = 0;
            }
        }


        // カート内の商品の売り切れチェック
        $objCartSess->chkSoldOut($objCartSess->getCartList());

        // 会員ログインチェック
        if($objCustomer->isLoginSuccess()) {
            $this->tpl_login = '1';
            $this->tpl_user_point = $objCustomer->getValue('point');
        }

        // 決済区分を取得する
        $payment_type = "";
        if($objDb->sfColumnExists("dtb_payment", "memo01")){
            // MEMO03に値が入っている場合には、モジュール追加されたものとみなす
            $sql = "SELECT memo03 FROM dtb_payment WHERE payment_id = ?";
            $arrPayment = $objQuery->getall($sql, array($arrData['payment_id']));
            $payment_type = $arrPayment[0]["memo03"];
        }
        $this->payment_type = $payment_type;

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        switch($_POST['mode']) {
        // 前のページに戻る
        case 'return':
            // 正常な推移であることを記録しておく
            $objSiteSess->setRegistFlag();
            $this->sendRedirect($this->getLocation(URL_SHOP_PAYMENT));
            exit;
            break;
        case 'confirm':
            // この時点でオーダーIDを確保しておく（クレジット、コンビニ決済で必要なため）
            // postgresqlとmysqlとで処理を分ける
            if (DB_TYPE == "pgsql") {
                $order_id = $objQuery->nextval("dtb_order","order_id");
            }elseif (DB_TYPE == "mysql") {
                $order_id = $objQuery->get_auto_increment("dtb_order");
            }
            $arrData["order_id"] = $order_id;

            // セッション情報を保持
            $arrData['session'] = serialize($_SESSION);

            // 集計結果を受注一時テーブルに反映
            $objDb->sfRegistTempOrder($uniqid, $arrData);
            // 正常に登録されたことを記録しておく
            $objSiteSess->setRegistFlag();

            // 決済方法により画面切替
            if($payment_type != "") {
                // TODO 決済方法のモジュールは Plugin として実装したい
                $_SESSION["payment_id"] = $arrData['payment_id'];
                $this->sendRedirect($this->getLocation(URL_SHOP_MODULE));
            }else{
                $this->sendRedirect($this->getLocation(URL_SHOP_COMPLETE));
            }
            break;
        default:
            break;
        }

        $this->arrData = $arrData;
        $this->arrInfo = $arrInfo;
        $objView->assignobj($this);
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
}
?>
