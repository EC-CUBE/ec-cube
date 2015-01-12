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

namespace Eccube\Page\Regist;

use Eccube\Application;
use Eccube\Page\AbstractPage;
use Eccube\Framework\CheckError;
use Eccube\Framework\Customer;
use Eccube\Framework\Query;
use Eccube\Framework\Response;
use Eccube\Framework\SendMail;
use Eccube\Framework\Helper\CustomerHelper;
use Eccube\Framework\Helper\DbHelper;
use Eccube\Framework\Helper\MailHelper;
use Eccube\Framework\Util\Utils;
use Eccube\Framework\View\SiteView;

/**
 * 会員登録のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Index extends AbstractPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        $this->skip_load_page_layout = true;
        parent::init();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        parent::process();
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のAction.
     *
     * @return void
     */
    public function action()
    {
        switch ($this->getMode()) {
            case 'regist':
            //--　本登録完了のためにメールから接続した場合
                //-- 入力チェック
                $this->arrErr       = $this->lfCheckError($_GET);
                if ($this->arrErr) Utils::sfDispSiteError(FREE_ERROR_MSG, '', true, $this->arrErr['id']);

                $registSecretKey    = $this->lfRegistData($_GET);   //本会員登録（フラグ変更）
                $this->lfSendRegistMail($registSecretKey);          //本会員登録完了メール送信

                Application::alias('eccube.response')->sendRedirect('complete.php', array('ci' => Application::alias('eccube.helper.customer')->sfGetCustomerId($registSecretKey)));
                break;
            //--　それ以外のアクセスは無効とする
            default:
                Utils::sfDispSiteError(FREE_ERROR_MSG, '', true, '無効なアクセスです。');
                break;
        }

    }

    /**
     * 仮会員を本会員にUpdateする
     *
     * @param mixed $array
     * @access private
     * @return string $arrRegist['secret_key'] 本登録ID
     */
    public function lfRegistData($array)
    {
        $objQuery                   = Application::alias('eccube.query');
        $arrRegist['secret_key']    = Application::alias('eccube.helper.customer')->sfGetUniqSecretKey(); //本登録ID発行
        $arrRegist['status']        = 2;
        $arrRegist['update_date']   = 'CURRENT_TIMESTAMP';

        $objQuery->begin();
        $objQuery->update('dtb_customer', $arrRegist, 'secret_key = ? AND status = 1', array($array['id']));
        $objQuery->commit();

        return $arrRegist['secret_key'];
    }

    /**
     * 入力エラーチェック
     *
     * @param mixed $array
     * @access private
     * @return array エラーの配列
     */
    public function lfCheckError($array)
    {
        /* @var $objErr CheckError */
        $objErr = Application::alias('eccube.check_error', $array);

        if (preg_match("/^[[:alnum:]]+$/", $array['id'])) {
            if (!is_numeric(Application::alias('eccube.helper.customer')->sfGetCustomerId($array['id'], true))) {
                $objErr->arrErr['id'] = '※ 既に会員登録が完了しているか、無効なURLです。<br>';
            }

        } else {
            $objErr->arrErr['id'] = '無効なURLです。メールに記載されている本会員登録用URLを再度ご確認ください。';
        }

        return $objErr->arrErr;
    }

    /**
     * 正会員登録完了メール送信
     *
     * @param string $registSecretKey
     * @access private
     * @return void
     */
    public function lfSendRegistMail($registSecretKey)
    {
        $objQuery       = Application::alias('eccube.query');
        /* @var $objCustomer Customer */
        $objCustomer = Application::alias('eccube.customer');
        /* @var $objHelperMail MailHelper */
        $objHelperMail = Application::alias('eccube.helper.mail');
        $objHelperMail->setPage($this);
        $CONF           = Application::alias('eccube.helper.db')->getBasisData();

        //-- 会員データを取得
        $arrCustomer    = $objQuery->select('*', 'dtb_customer', 'secret_key = ?', array($registSecretKey));
        $data           = $arrCustomer[0];
        $objCustomer->setLogin($data['email']);

        //--　メール送信
        $objMailText    = new SiteView();
        $objMailText->setPage($this);
        $objMailText->assign('CONF', $CONF);
        $objMailText->assign('name01', $data['name01']);
        $objMailText->assign('name02', $data['name02']);
        $toCustomerMail = $objMailText->fetch('mail_templates/customer_regist_mail.tpl');
        $subject = $objHelperMail->sfMakesubject('会員登録が完了しました。');

        /* @var $objMail Sendmail */
        $objMail = Application::alias('eccube.sendmail');
        $objMail->setItem(
                            '',                         // 宛先
                            $subject,                   // サブジェクト
                            $toCustomerMail,            // 本文
                            $CONF['email03'],           // 配送元アドレス
                            $CONF['shop_name'],         // 配送元 名前
                            $CONF['email03'],           // reply_to
                            $CONF['email04'],           // return_path
                            $CONF['email04']            // Errors_to
        );
        // 宛先の設定
        $name = $data['name01'] . $data['name02'] .' 様';
        $objMail->setTo($data['email'], $name);
        $objMail->sendMail();
    }
}
