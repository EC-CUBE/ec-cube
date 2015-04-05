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

use Eccube\Application;
use Eccube\Framework\Customer;
use Eccube\Framework\Display;
use Eccube\Framework\Response;
use Eccube\Framework\SendMail;
use Eccube\Framework\Helper\CustomerHelper;
use Eccube\Framework\Helper\DbHelper;
use Eccube\Framework\Helper\MailHelper;
use Eccube\Framework\Helper\SessionHelper;
use Eccube\Framework\Util\Utils;
use Eccube\Framework\View\SiteView;

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
                    Application::alias('eccube.response')->actionExit();
                }

                /* @var $objCustomer Customer */
                $objCustomer = Application::alias('eccube.customer');
                $this->lfSendRefusalMail($objCustomer->getValue('customer_id'));
                $this->lfDeleteCustomer($objCustomer->getValue('customer_id'));
                $objCustomer->EndSession();

                Application::alias('eccube.response')->sendRedirect('refusal_complete.php');
                break;

            default:
                if (Application::alias('eccube.display')->detectDevice() == DEVICE_TYPE_MOBILE) {
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
        return Application::alias('eccube.helper.customer')->delete($customer_id);
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
            $arrCustomerData = Application::alias('eccube.helper.customer')->sfGetCustomerDataFromId($customer_id);
        }
        if (Utils::isBlank($arrCustomerData)) {
            return false;
        }

        $CONF = Application::alias('eccube.helper.db')->getBasisData();

        $objMailText = new SiteView();
        $objMailText->setPage($this);
        $objMailText->assign('CONF', $CONF);
        $objMailText->assign('name01', $arrCustomerData['name01']);
        $objMailText->assign('name02', $arrCustomerData['name02']);
        $objMailText->assignobj($this);

        /* @var $objHelperMail MailHelper */
        $objHelperMail = Application::alias('eccube.helper.mail');
        $objHelperMail->setPage($this);

        $subject        = $objHelperMail->sfMakeSubject('退会手続きのご完了', $objMailText);
        $toCustomerMail = $objMailText->fetch('mail_templates/customer_refusal_mail.tpl');

        /* @var $objMail Sendmail */
        $objMail = Application::alias('eccube.sendmail');
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
