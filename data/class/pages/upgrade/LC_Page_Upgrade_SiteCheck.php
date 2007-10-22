<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once CLASS_PATH . 'pages/LC_Page.php';

/**
 * サイトチェック用クラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Upgrade_SiteCheck extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {}

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        if ($this->isValidIP() !== true) {
            exit;
        }

        $objReq = new HTTP_Request();
        $objReq->setUrl(OWNERSSTORE_URL . 'upgrade/index.php');
        $objReq->setMethod('POST');
        $objReq->addPostData('mode', 'site_check');
        $objReq->addPostData('eccube_version', ECCUBE_VERSION);

        if (PEAR::isError($e = $objReq->sendRequest())) {
            exit;
        }

        if ($objReq->getResponseCode() !== 200) {
            exit;
        }

        $objJson = new Services_JSON();
        $objRet  = $objJson->decode($objReq->getResponseBody());

        if (!empty($objRet) && $objRet->status == OWNERSSTORE_STATUS_SUCCESS) {
            $arrParam = array(
                'status' => OWNERSSTORE_STATUS_SUCCESS,
                'id'     => $objRet->id,
            );
            echo $objJson->encode($arrParam);
            exit;
        }
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {}

    function isValidIP() {
        if ($_SERVER['REMOTE_ADDR'] === OWNERSSTORE_IP) {
            return true;
        }
        return false;
    }
}
?>
