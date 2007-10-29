<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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
require_once CLASS_PATH . 'pages/LC_Page.php';

/**
 * オーナーズストア購入商品一覧を返すページクラス.
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
        $this->objJson = new Services_Json();
        $this->objSess = new SC_Session();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $errFormat = '* error! code:%s / debug:%s';

        GC_Utils::gfPrintLog('###ProductsList Start###');

        // 管理画面ログインチェック
        GC_Utils::gfPrintLog('* admin auth start');
        if ($this->objSess->isSuccess() !== SUCCESS) {
            $arrErr = array(
                'status'  => OWNERSSTORE_STATUS_ERROR,
                'errcode' => OWNERSSTORE_ERR_PL_ADMIN_AUTH,
                'body' => '配信サーバとの通信中にエラーが発生しました。エラーコード:' . OWNERSSTORE_ERR_PL_ADMIN_AUTH
            );
            echo $this->objJson->encode($arrErr);
            GC_Utils::gfPrintLog(
                sprintf($errFormat, $arrErr['errcode'], serialize($this->objSess))
            );
            exit;
        }

        // TODO CSRF対策が必須

        $objReq = new HTTP_Request();
        $objReq->setUrl('http://cube-shopaccount/upgrade/index.php'); // TODO URL定数化
        $objReq->setMethod('POST');
        $objReq->addPostData('mode', 'products_list');
        $objReq->addPostData('site_url', SITE_URL);
        $objReq->addPostData('ssl_url', SSL_URL);

        // リクエストを開始
        GC_Utils::gfPrintLog('* http request start');
        if (PEAR::isError($e = $objReq->sendRequest())) {
            $arrErr = array(
                'status'  => OWNERSSTORE_STATUS_ERROR,
                'errcode' => OWNERSSTORE_ERR_DL_HTTP_REQ,
                'body' => '配信サーバとの通信中にエラーが発生しました。エラーコード:' . OWNERSSTORE_ERR_DL_HTTP_REQ
            );
            echo $this->objJson->encode($arrErr);
            GC_Utils::gfPrintLog(
                sprintf($errFormat, $arrErr['errcode'], serialize($e))
            );
            exit;
        }

        GC_Utils::gfPrintLog('* http response check start');
        if ($objReq->getResponseCode() !== 200) {
            $arrErr = array(
                'status'  => OWNERSSTORE_STATUS_ERROR,
                'errcode' => OWNERSSTORE_ERR_DL_HTTP_RESP_CODE,
                'body' => '配信サーバとの通信中にエラーが発生しました。エラーコード:' . OWNERSSTORE_ERR_DL_HTTP_RESP_CODE
            );
            echo $this->objJson->encode($arrErr);
            GC_Utils::gfPrintLog(
                sprintf($errFormat, $arrErr['errcode'], serialize($objReq))
            );
            exit;
        }

        $body = $objReq->getResponseBody();
        $jsonData = $this->objJson->decode($body);
        GC_Utils::gfPrintLog('* json deta check start');
        if (empty($jsonData)) {
            $arrErr = array(
                'status'  => OWNERSSTORE_STATUS_ERROR,
                'errcode' => OWNERSSTORE_ERR_PL_INVALID_JSON_DATA,
                'body' => '配信サーバとの通信中にエラーが発生しました。エラーコード:' . OWNERSSTORE_ERR_PL_INVALID_JSON_DATA
            );
            echo $this->objJson->encode($arrErr);
            GC_Utils::gfPrintLog(
                sprintf($errFormat, $arrErr['errcode'], serialize($body))
            );
            exit;
        }
        GC_Utils::gfPrintLog('* json status check start');
        if ($jsonData->status === OWNERSSTORE_STATUS_SUCCESS) {
            GC_Utils::gfPrintLog('* get products list ok');
            echo $body;
            exit;
        } else {
            echo $body;
            GC_Utils::gfPrintLog(
                sprintf($errFormat, $jsonData->errcode, serialize($objReq))
            );
            exit;
        }
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        GC_Utils::gfPrintLog('###ProductsList END###');
    }
}
?>
