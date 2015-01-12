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
