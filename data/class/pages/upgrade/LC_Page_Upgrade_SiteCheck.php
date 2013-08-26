<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
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

require_once 'LC_Page_Upgrade_Base.php';

/**
 * サイトチェック用クラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Upgrade_SiteCheck extends LC_Page_Upgrade_Base
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process($mode)
    {
        $objLog  = new LC_Upgrade_Helper_LOG;
        $objJson = new LC_Upgrade_Helper_Json;

        $objLog->start($mode);

        if ($this->isValidIP() !== true) {
            $objJson->setError(OSTORE_E_C_INVALID_ACCESS);
            $objJson->display();
            $objLog->error(OSTORE_E_C_INVALID_ACCESS);

            return;
        }

        $dbFactory = SC_DB_DBFactory_Ex::getInstance();
        $arrSystemInfo = array(
            'eccube_version' => ECCUBE_VERSION,
            'php_version'    => phpversion(),
            'db_version'     => $dbFactory->sfGetDBVersion()
        );
        $objJson->setSuccess($arrSystemInfo);
        $objJson->display();
        $objLog->end();
    }

    /**
     * デストラクタ
     *
     * XXX 旧実装が親クラスのデストラクタを呼んでいなかったので、その仕様を維持している。
     * @return void
     */
    public function __destruct()
    {
    }
}
