<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Page\Admin\Mail;

use Eccube\Application;
use Eccube\Page\Admin\AbstractAdminPage;
use Eccube\Framework\Query;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Helper\MailHelper;
use Eccube\Framework\Util\Utils;

/**
 * メールテンプレート設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Template extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'mail/template.tpl';
        $this->tpl_mainno = 'mail';
        $this->tpl_subno = 'template';
        $this->tpl_maintitle = 'メルマガ管理';
        $this->tpl_subtitle = 'テンプレート設定';

        $masterData = Application::alias('eccube.db.master_data');
        $this->arrMagazineType = $masterData->getMasterData('mtb_magazine_type');
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
            case 'delete':
                if (Utils::sfIsInt($_GET['id'])===true) {
                    $this->lfDeleteMailTemplate($_GET['id']);

                    $this->objDisplay->reload(null, true);
                }
                break;
            default:
                break;
        }
        $this->arrTemplates = $objMailHelper->sfGetMailmagaTemplate();
    }

    /**
     * メールテンプレートの削除
     * @param integer 削除したいテンプレートのID
     * @return void
     */
    public function lfDeleteMailTemplate($template_id)
    {
        $objQuery = Application::alias('eccube.query');
        $objQuery->update('dtb_mailmaga_template',
                          array('del_flg' =>1),
                          'template_id = ?',
                          array($template_id));
    }
}
