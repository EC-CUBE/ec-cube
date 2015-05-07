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


namespace Eccube\Page\Admin\OwnersStore;

use Eccube\Application;
use Eccube\Page\Admin\AbstractAdminPage;
use Eccube\Framework\FormParam;
use Eccube\Framework\Query;
use Eccube\Framework\Util\Utils;

/**
 * オーナーズストア：インストールログ のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Log extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        $this->tpl_mainpage = 'ownersstore/log.tpl';
        $this->tpl_mainno   = 'ownersstore';
        $this->tpl_subno    = 'log';
        $this->tpl_maintitle = 'オーナーズストア';
        $this->tpl_subtitle = 'ログ管理';
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
        switch ($this->getMode()) {
            case 'detail':
                $objForm = $this->initParam();
                if ($objForm->checkError()) {
                    Utils::sfDispError('');
                }
                $this->arrLogDetail = $this->getLogDetail($objForm->getValue('log_id'));
                if (count($this->arrLogDetail) == 0) {
                    Utils::sfDispError('');
                }
                $this->tpl_mainpage = 'ownersstore/log_detail.tpl';
                break;
            default:
                break;
        }
        $this->arrInstallLogs = $this->getLogs();
    }

    public function getLogs()
    {
        $sql =<<<END
SELECT
    *
FROM
    dtb_module_update_logs JOIN (
    SELECT
        module_id,
        module_name
    FROM
        dtb_module
    ) AS modules ON dtb_module_update_logs.module_id = modules.module_id
ORDER BY update_date DESC
END;
        $objQuery = Application::alias('eccube.query');
        $arrRet = $objQuery->getAll($sql);

        return isset($arrRet) ? $arrRet : array();
    }

    public function initParam()
    {
        $objForm = Application::alias('eccube.form_param');
        $objForm->addParam('log_id', 'log_id', INT_LEN, '', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objForm->setParam($_GET);

        return $objForm;
    }

    public function getLogDetail($log_id)
    {
            $sql =<<<END
SELECT
    *
FROM
    dtb_module_update_logs JOIN (
    SELECT
        module_id,
        module_name
    FROM
        dtb_module
    ) AS modules ON dtb_module_update_logs.module_id = modules.module_id
WHERE
    log_id = ?
END;
        $objQuery = Application::alias('eccube.query');
        $arrRet = $objQuery->getAll($sql, array($log_id));

        return isset($arrRet[0]) ? $arrRet[0] : array();
    }
}
