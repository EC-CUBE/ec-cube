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

namespace Eccube\Page\Api;

use Eccube\Application;
use Eccube\Page\AbstractPage;
use Eccube\Framework\Api\Operation;
use Eccube\Framework\Response;

/**
 * APIのページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Xml extends AbstractPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        $this->action();
//        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    public function action()
    {
        $arrParam = $_REQUEST;

        list($response_outer, $arrResponse) = Operation::doApiAction($arrParam);
        Operation::sendApiResponse('xml', $response_outer, $arrResponse);
        Application::alias('eccube.response')->actionExit();
    }
}
