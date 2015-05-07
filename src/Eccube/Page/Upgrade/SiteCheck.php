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


namespace Eccube\Page\Upgrade;

use Eccube\Application;
use Eccube\Page\Upgrade\Helper\LogHelper;
use Eccube\Page\Upgrade\Helper\JsonHelper;
use Eccube\Framework\DB\DBFactory;

/**
 * サイトチェック用クラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class SiteCheck extends AbstractUpgrade
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
        $objLog  = new LogHelper;
        $objJson = new JsonHelper;

        $objLog->start($mode);

        $dbFactory = Application::alias('eccube.db.factory');
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
