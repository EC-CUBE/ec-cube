<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once CLASS_PATH . 'pages/upgrade/LC_Page_Upgrade_Base.php';
error_reporting(E_ALL);

/**
 * ダウンロード処理を担当する.
 *
 * TODO 要リファクタリング
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Upgrade_Config extends LC_Page_Upgrade_Base {

    /** SC_Sessionオブジェクト */
    var $objSession = null;
    /** Services_Jsonオブジェクト */
    var $objJson = null;
    /** HTTP_Requestオブジェクト */
    var $objReq = null;
    /** SC_FromParamオブジェクト */
    var $objForm = null;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        $this->objSess = new SC_Session();
        $this->objJson = new Services_Json();
        $rhis->objReq  = new HTTP_Request();
        $this->objForm = new SC_FormParam();
        $this->objForm->addParam(
            'product_id', 'product_id', INT_LEN, '',
            array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK')
        );
        $this->objForm->setParam($_POST);
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $errFormat = '* error! code:%s / debug:%s';

        GC_Utils::gfPrintLog('###Config Start###');

        GC_Utils::gfPrintLog('* admin auth start');
        if ($this->objSess->isSuccess() !== SUCCESS) {
            $arrErr = array(
                'status'  => OWNERSSTORE_STATUS_ERROR,
                'errcode' => '###',
                'body'    => '###'
            );
            echo $this->json->encode($arrErr);
            exit;
        }

        GC_Utils::gfPrintLog('* post param check start');
        if ($this->objForm->checkError()) {
            $arrErr = array(
                'status'  => OWNERSSTORE_STATUS_ERROR,
                'errcode' => '###',
                'body'    => '###'
            );
            echo $this->json->encode($arrErr);
            exit;
        }

        $objReq = new HTTP_Request();
        $objReq->setURL('http://cube-shopaccount/upgrade/index.php');
        $objReq->setMethod('POST');
        $objReq->addPostData('site_url', SITE_URL);
        $objReq->addPostData('ssl_url', SSL_URL);
        $objReq->addPostData('product_id', $objForm->getValue('product_id'));

        GC_Utils::gfPrintLog('* http request start');
        $e = $objReq->sendRequest();

        GC_Utils::gfPrintLog('* http request check start');
        if (PEAR::isError($e)) {
            $arrErr = array(
                'status'  => OWNERSSTORE_STATUS_ERROR,
                'errcode' => '###',
                'body'    => '###'
            );
            echo $this->json->encode($arrErr);
            exit;
        }

        GC_Utils::gfPrintLog('* http response check start');
        if ($objReq->getResponseCode() !== 200) {
            $arrErr = array(
                'status'  => OWNERSSTORE_STATUS_ERROR,
                'errcode' => '###',
                'body'    => '###'
            );
            echo $this->json->encode($arrErr);
            exit;
        }

        $jsonData = $objReq->getResponseBody();
        $decodedData = $this->objJson->decode($jsonData);

        GC_Utils::gfPrintLog('* json data check start');
        if (empty($decodedData)) {
            $arrErr = array(
                'status'  => OWNERSSTORE_STATUS_ERROR,
                'errcode' => '###',
                'body'    => '###'
            );
            echo $this->json->encode($arrErr);
            exit;
        }

        GC_Utils::gfPrintLog('* status check start');
        if ($decodedData->status === OWNERSSTORE_STATUS_SUCCESS) {
            echo $jsonData;
            exit;
        } else {
            echo $jsonData;
            exit;
        }
    }

    /**
     * デストラクタ
     *
     * @return void
     */
    function destroy() {
        GC_Utils::gfPrintLog('###Config End###');
    }
}
?>
