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

namespace Eccube\Page\Admin\Order;

use Eccube\Application;
use Eccube\Page\Admin\AbstractAdminPage;
use Eccube\Framework\Query;
use Eccube\Framework\Util\Utils;

/**
 * 受注管理メール確認 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class MailView extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'order/mail_view.tpl';
        $this->tpl_subtitle = '受注管理メール確認';
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
        $send_id = $_GET['send_id'];
        if (Utils::sfIsInt($send_id)) {
            $mailHistory = $this->getMailHistory($send_id);
            $this->tpl_subject = $mailHistory[0]['subject'];
            $this->tpl_body = $mailHistory[0]['mail_body'];
        }
        $this->setTemplate($this->tpl_mainpage);
    }

    /**
     *
     * メールの履歴を取り出す。
     * @param int $send_id
     */
    public function getMailHistory($send_id)
    {
        $objQuery = Application::alias('eccube.query');
        $col = 'subject, mail_body';
        $where = 'send_id = ?';
        $mailHistory = $objQuery->select($col, 'dtb_mail_history', $where, array($send_id));

        return $mailHistory;
    }
}
