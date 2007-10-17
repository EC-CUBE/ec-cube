<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once CLASS_PATH . 'pages/LC_Page.php';

/**
 * オーナーズストア連携の基底クラス
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Upgrade_Base extends LC_Page {

    /** Services_Jsonオブジェクト */
    var $objJson = null;
    /** HTTP_Requestオブジェクト */
    var $objReq  = null;

    // }}}
    // {{{ functions

    function LC_Page_Upgrade_Base() {
        $this->objJson = new Services_Json();
    }
    /**
     * 配信サーバへリクエストを送信する.
     *
     * @param string $mode
     * @param array $arrParams 追加パラメータ.連想配列で渡す.
     * @return string|object レスポンスボディ|エラー時にはPEAR::Errorオブジェクトを返す.
     */
    function request($mode, $arrParams = array()) {
        $objReq = new HTTP_Request();
        $objReq->setUrl('http://cube-shopaccount/upgrade/index.php');
        $objReq->setMethod('POST');
        $objReq->addPostData('mode', $mode);
        $objReq->addPostData('site_url', SITE_URL);
        $objReq->addPostData('ssl_url', SSL_URL);
        $objReq->addPostDataArray($arrParams);

        $e = $objReq->sendRequest();
        if (PEAR::isError($e)) {
            return $e;
        } else {
            return $objReq;
        }
    }
}
?>
