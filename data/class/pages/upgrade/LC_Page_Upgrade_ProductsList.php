<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once CLASS_PATH . 'pages/LC_Page.php';
require_once DATA_PATH  . 'module/Services/JSON.php';
require_once DATA_PATH  . 'module/Request.php';

/**
 * XXX のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Upgrade_ProductsList extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objSess = new SC_Session();
        if ( $objSess->isSuccess() !== true) {
            // TODO エラー処理
        }

        // TODO CSRF対策が必須

        $objReq = new HTTP_Request();
        $objReq->setUrl('http://cube-shopaccount/upgrade/index.php');
        $objReq->setMethod('POST');
        $objReq->addPostData('mode', 'products_list');
        $objReq->addPostData('site_url', SITE_URL);
        $objReq->addPostData('ssl_url', SSL_URL);

        if (PEAR::isError($objReq->sendRequest())) {
            // TODO エラー処理
        }

        if ($objReq->getResponseCode() !== 200) {
            // TODO エラー処理
        }

        echo $objReq->getResponseBody();
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }


    function createResponceHTML() {}
}
?>
