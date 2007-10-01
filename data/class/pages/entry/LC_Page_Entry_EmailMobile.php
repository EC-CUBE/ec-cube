<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * 空メール会員登録(モバイル) のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Entry_EmailMobile extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'entry/email_mobile.tpl';
        $this->tpl_title = '携帯メール登録';
        $this->allowClientCache();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objView = new SC_MobileView;
        $objCustomer = new SC_Customer;
        $objFormParam = new SC_FormParam;

        if (isset($_SESSION['mobile']['kara_mail_from'])) {
            $_SERVER['REQUEST_METHOD'] = 'POST';
            $_POST['email_mobile'] = $_SESSION['mobile']['kara_mail_from'];
        }

        $this->lfInitParam($objFormParam);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $objFormParam->setParam($_POST);
            $objFormParam->convParam();
            $this->arrErr = $this->lfCheckError($objFormParam, $objCustomer);

            if (empty($this->arrErr)) {
                $this->lfRegister($objFormParam, $objCustomer);
                $this->tpl_mainpage = 'entry/email_mobile_complete.tpl';
                $this->tpl_title = '携帯メール登録完了';
            }
        }

        // 空メール用のトークンを作成する。
        if (MOBILE_USE_KARA_MAIL) {
            $objMobile = new SC_Helper_Mobile_Ex();
            $token = $objMobile->gfPrepareKaraMail('entry/email_mobile.php');
            if ($token !== false) {
                $this->tpl_kara_mail_to = MOBILE_KARA_MAIL_ADDRESS_USER . MOBILE_KARA_MAIL_ADDRESS_DELIMITER . 'entry_' . $token . '@' . MOBILE_KARA_MAIL_ADDRESS_DOMAIN;
            }
        }

        $this->tpl_name = $objCustomer->getValue('name01');
        $this->arrForm = $objFormParam->getFormParamList();

        $objView->assignobj($this);
        $objView->display(SITE_FRAME);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }


    function lfInitParam(&$objFormParam) {
        $objFormParam->addParam('メールアドレス', 'email_mobile', MTEXT_LEN, 'a',
                                array('NO_SPTAB', 'EXIST_CHECK', 'MAX_LENGTH_CHECK', 'CHANGE_LOWER', 'EMAIL_CHAR_CHECK', 'MOBILE_EMAIL_CHECK'));
    }

    function lfCheckError(&$objFormParam, &$objCustomer) {
        $arrRet = $objFormParam->getHashArray();
        $objErr = new SC_CheckError($arrRet);
        $objErr->arrErr = $objFormParam->checkError();

        if (count($objErr->arrErr) > 0) {
            return $objErr->arrErr;
        }

        $email_mobile = strtolower($objFormParam->getValue('email_mobile'));
        $customer_id = $objCustomer->getValue('customer_id');
        $objQuery = new SC_Query();
        // TODO ORDER BY del_flg は必要?
        $arrRet = $objQuery->select('email, email_mobile, update_date, del_flg', 'dtb_customer', '(email = ? OR email_mobile = ?) AND customer_id <> ? ORDER BY del_flg', array($email_mobile, $email_mobile, $customer_id));

        if (count($arrRet) > 0) {
            if ($arrRet[0]['del_flg'] != '1') {
                // 会員である場合
                $objErr->arrErr['email_mobile'] .= '※ すでに登録されているメールアドレスです。<br>';
            } else {
                // 退会した会員である場合
                $leave_time = SC_Utils_Ex::sfDBDatetoTime($arrRet[0]['update_date']);
                $now_time = time();
                $pass_time = $now_time - $leave_time;
                // 退会から何時間-経過しているか判定する。
                $limit_time = ENTRY_LIMIT_HOUR * 3600;
                if ($pass_time < $limit_time) {
                    $objErr->arrErr['email_mobile'] .= '※ 退会から一定期間の間は、同じメールアドレスを使用することはできません。<br>';
                }
            }
        }

        return $objErr->arrErr;
    }

    function lfRegister(&$objFormParam, &$objCustomer) {
        $customer_id = $objCustomer->getValue('customer_id');
        $email_mobile = strtolower($objFormParam->getValue('email_mobile'));

        $objQuery = new SC_Query();
        $objQuery->update('dtb_customer', array('email_mobile' => $email_mobile), 'customer_id = ?', array($customer_id));

        $objCustomer->setValue('email_mobile', $email_mobile);
    }
}
?>
