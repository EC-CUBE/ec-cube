<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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

// {{{ requires
require_once(CLASS_EX_REALDIR . "page_extends/admin/LC_Page_Admin_Ex.php");

/**
 * アプリケーション管理:インストールログ のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_OwnersStore_Log extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();

        $this->tpl_mainpage = 'ownersstore/log.tpl';
        $this->tpl_mainno   = 'ownersstore';
        $this->tpl_subno    = 'log';
        $this->tpl_subtitle = 'ログ管理';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        switch ($this->getMode()) {
        case 'detail':
            $objForm = $this->initParam();
            if ($objForm->checkError()) {
                SC_Utils_Ex::sfDispError('');
            }
            $this->arrLogDetail = $this->getLogDetail($objForm->getValue('log_id'));
            if (count($this->arrLogDetail) == 0) {
                SC_Utils_Ex::sfDispError('');
            }
            $this->tpl_mainpage = 'ownersstore/log_detail.tpl';
            break;
        default:
            break;
        }
        $this->arrInstallLogs = $this->getLogs();
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    function getLogs() {
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
    ) AS modules USING(module_id)
ORDER BY update_date DESC
END;
        $objQuery = new SC_Query;
        $arrRet = $objQuery->getAll($sql);
        return isset($arrRet) ? $arrRet : array();
    }

    function initParam() {
        $objForm = new SC_FormParam();
        $objForm->addParam('log_id', 'log_id', INT_LEN, '', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objForm->setParam($_GET);
        return $objForm;
    }

    function getLogDetail($log_id) {
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
    ) AS modules USING(module_id)
WHERE
    log_id = ?
END;
        $objQuery = new SC_Query;
        $arrRet = $objQuery->getAll($sql, array($log_id));
        return isset($arrRet[0]) ? $arrRet[0] : array();
    }
}
?>
