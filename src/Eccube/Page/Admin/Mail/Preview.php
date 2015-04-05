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

namespace Eccube\Page\Admin\Mail;

use Eccube\Application;
use Eccube\Page\Admin\AbstractAdminPage;
use Eccube\Framework\Helper\MailHelper;
use Eccube\Framework\Util\Utils;

/**
 * メルマガプレビュー のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Preview extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_subtitle = 'プレビュー';
        $this->tpl_mainpage = 'mail/preview.tpl';
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
        /* @var $objMailHelper MailHelper */
        $objMailHelper = Application::alias('eccube.helper.mail');

        switch ($this->getMode()) {
            case 'template':
                if (Utils::sfIsInt($_GET['template_id'])) {
                    $arrMail = $objMailHelper->sfGetMailmagaTemplate($_GET['template_id']);
                    $this->mail = $arrMail[0];
                }
                break;
            case 'history';
                if (Utils::sfIsInt($_GET['send_id'])) {
                    $arrMail = $objMailHelper->sfGetSendHistory($_GET['send_id']);
                    $this->mail = $arrMail[0];
                }
                break;
            case 'presend';
                $this->mail['body'] = $_POST['body'];
            default:
                break;
        }

        $this->setTemplate($this->tpl_mainpage);
    }
}
