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

namespace Eccube\Page\Mypage;

use Eccube\Common\Customer;
use Eccube\Common\Display;
use Eccube\Common\Response;
use Eccube\Common\SendMail;
use Eccube\Common\Helper\CustomerHelper;
use Eccube\Common\Helper\DbHelper;
use Eccube\Common\Helper\MailHelper;
use Eccube\Common\Helper\SessionHelper;
use Eccube\Common\Util\Utils;
use Eccube\Common\View\SiteView;

/**
 * 退会手続き のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Refusal extends AbstractMypage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_subtitle = '退会手続き(入力ページ)';
        $this->tpl_mypageno = 'refusal';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        parent::process();
    }

    /**
     * Page のAction.
     *
     * @return void
     */
    public function action()
    {
        switch ($this->getMode()) {
            case 'confirm':
                // トークンを設定
                $this->refusal_transactionid = $this->getRefusalToken();

                $this->tpl_mainpage     = 'mypage/refusal_confirm.tpl';
                $this->tpl_subtitle     = '退会手続き(確認ページ)';
                break;

            case 'complete':
                // トークン入力チェック
                if (!$this->isValidRefusalToken()) {
                    // エラー画面へ遷移する
                    Utils::sfDispSiteError(PAGE_ERROR, '', true);
                    Response::actionExit();
                }

                $objCustomer = new Customer();
                $this->lfSendRefusalMail($objCustomer->getValue('customer_id'));
                $this->lfDeleteCustomer($objCustomer->getValue('customer_id'));
                $objCustomer->EndSession();

                Response::sendRedirect('refusal_complete.php');
                break;

            default:
                if (Display::detectDevice() == DEVICE_TYPE_MOBILE) {
                    $this->refusal_transactionid = $this->getRefusalToken();
                }
                break;
        }

    }

    /**
     * トランザクショントークンを取得する
     *
     * @return string
     */
    function getRefusalToken() {
        if (empty($_SESSION['refusal_transactionid'])) {
            $_SESSION['refusal_transactionid'] = SessionHelper::createToken();
        }
        return $_SESSION['refusal_transactionid'];
    }

    /**
     * トランザクショントークンのチェックを行う
     */
    function isValidRefusalToken() {
        if (empty($_POST['refusal_transactionid'])) {
            $ret = false;
        } else {
            $ret = $_POST['refusal_transactionid'] === $_SESSION['refusal_transactionid'];
        }

        return $ret;
    }

    /**
     * トランザクショントークを破棄する
     */
    function destroyRefusalToken() {
        unset($_SESSION['refusal_transactionid']);
    }

    /**
     * 会員情報を削除する
     *
     * @access private
     * @return boolean
     */
    public function lfDeleteCustomer($customer_id)
    {
        return CustomerHelper::delete($customer_id);
    }

    /**
     * 退会手続き完了メール送信する
     *
     * @access private
     * @param integer $customer_id 会員ID
     * @return void
     */
    public function lfSendRefusalMail($customer_id)
    {
        // 会員データの取得
        if (Utils::sfIsInt($customer_id)) {
            $arrCustomerData = CustomerHelper::sfGetCustomerDataFromId($customer_id);
        }
        if (Utils::isBlank($arrCustomerData)) {
            return false;
        }

        $CONF = DbHelper::getBasisData();

        $objMailText = new SiteView();
        $objMailText->setPage($this);
        $objMailText->assign('CONF', $CONF);
        $objMailText->assign('name01', $arrCustomerData['name01']);
        $objMailText->assign('name02', $arrCustomerData['name02']);
        $objMailText->assignobj($this);

        $objHelperMail  = new MailHelper();
        $objHelperMail->setPage($this);

        $subject        = $objHelperMail->sfMakeSubject('退会手続きのご完了', $objMailText);
        $toCustomerMail = $objMailText->fetch('mail_templates/customer_refusal_mail.tpl');

        $objMail = new SendMail();
        $objMail->setItem(
            '',                     // 宛先
            $subject,               // サブジェクト
            $toCustomerMail,        // 本文
            $CONF['email03'],       // 配送元アドレス
            $CONF['shop_name'],     // 配送元 名前
            $CONF['email03'],       // reply_to
            $CONF['email04'],       // return_path
            $CONF['email04'],       // Errors_to
            $CONF['email01']        // Bcc
        );
        $objMail->setTo($arrCustomerData['email'], $arrCustomerData['name01'] . $arrCustomerData['name02'] .' 様');

        $objMail->sendMail();
    }

}
