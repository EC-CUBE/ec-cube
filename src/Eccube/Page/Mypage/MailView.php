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
use Eccube\Framework\Query;
use Eccube\Framework\Response;
use Eccube\Framework\Util\Utils;

/**
 * 受注管理メール確認 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class MailView extends AbstractMypage
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
        $this->httpCacheControl('nocache');
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
        /* @var $objCustomer Customer */
        $objCustomer = Application::alias('eccube.customer');
        if (!Utils::sfIsInt($_GET['send_id'])) {
            Utils::sfDispSiteError(CUSTOMER_ERROR);
        }

        $arrMailView = $this->lfGetMailView($_GET['send_id'], $objCustomer->getValue('customer_id'));

        if (empty($arrMailView)) {
            Utils::sfDispSiteError(CUSTOMER_ERROR);
        }

        $this->tpl_subject  = $arrMailView[0]['subject'];
        $this->tpl_body     = $arrMailView[0]['mail_body'];

        if (Application::alias('eccube.display')->detectDevice() === DEVICE_TYPE_PC) {
            $this->setTemplate('mypage/mail_view.tpl');
        } else {
            $this->tpl_title    = 'メール履歴詳細';
            $this->tpl_mainpage = 'mypage/mail_view.tpl';
        }

        switch ($this->getMode()) {
            case 'getDetail':

                echo Utils::jsonEncode($arrMailView);
                Application::alias('eccube.response')->actionExit();
                break;
            default:
                break;
        }

    }

    /**
     * GETで指定された受注idのメール送信内容を返す
     *
     * @param mixed $send_id
     * @param mixed $customer_id
     * @access private
     * @return array
     */
    public function lfGetMailView($send_id, $customer_id)
    {
        $objQuery   = Application::alias('eccube.query');
        $col        = 'subject, mail_body';
        $where      = 'send_id = ? AND customer_id = ?';
        $arrWhereVal = array($send_id, $customer_id);

        return $objQuery->select($col, 'dtb_mail_history LEFT JOIN dtb_order ON dtb_mail_history.order_id = dtb_order.order_id', $where, $arrWhereVal);
    }
}
