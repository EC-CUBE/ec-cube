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
require_once 'utils/LC_Utils_Upgrade.php';
require_once 'utils/LC_Utils_Upgrade_Log.php';

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
        $this->objLog  = new LC_Utils_Upgrade_Log('Products List');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->objLog->start();

        // 管理画面ログインチェック
        $this->objLog->log('* admin auth start');
        if (LC_Utils_Upgrade::isLoggedInAdminPage() !== true) {
            $arrErr = array(
                'status'  => OWNERSSTORE_STATUS_ERROR,
                'errcode' => OWNERSSTORE_ERR_PL_ADMIN_AUTH,
                'body' => LC_Utils_Upgrade::getErrMessage(OWNERSSTORE_ERR_PL_ADMIN_AUTH)
            );
            echo $this->objJson->encode($arrErr);
            $this->objLog->errLog($arrErr['errcode']);
            exit;
        }

        // リクエストを開始
        $this->objLog->log('* http request start');
        $objReq = LC_Utils_Upgrade::request('products_list');

        // リクエストチェック
        $this->objLog->log('* http request check start');
        if (PEAR::isError($objReq)) {
            $arrErr = array(
                'status'  => OWNERSSTORE_STATUS_ERROR,
                'errcode' => OWNERSSTORE_ERR_PL_HTTP_REQ,
                'body' => LC_Utils_Upgrade::getErrMessage(OWNERSSTORE_ERR_PL_HTTP_REQ)
            );
            echo $this->objJson->encode($arrErr);
            $this->objLog->errLog($arrErr['errcode'], $objReq);
            exit;
        }

        // レスポンスチェック
        $this->objLog->log('* http response check start');
        if ($objReq->getResponseCode() !== 200) {
            $arrErr = array(
                'status'  => OWNERSSTORE_STATUS_ERROR,
                'errcode' => OWNERSSTORE_ERR_PL_HTTP_RESP_CODE,
                'body' => LC_Utils_Upgrade::getErrMessage(OWNERSSTORE_ERR_PL_HTTP_RESP_CODE)
            );
            echo $this->objJson->encode($arrErr);
            $this->objLog->errLog($arrErr['errcode'], $objReq);
            exit;
        }

        $body = $objReq->getResponseBody();
        $objRet = $this->objJson->decode($body);

        // JSONデータのチェック
        $this->objLog->log('* json deta check start');
        if (empty($objRet)) {
            $arrErr = array(
                'status'  => OWNERSSTORE_STATUS_ERROR,
                'errcode' => OWNERSSTORE_ERR_PL_INVALID_JSON_DATA,
                'body' => LC_Utils_Upgrade::getErrMessage(OWNERSSTORE_ERR_PL_INVALID_JSON_DATA)
            );
            echo $this->objJson->encode($arrErr);
            $this->objLog->errLog($arrErr['errcode'], $body);
            exit;
        }

        // ステータスチェック
        $this->objLog->log('* json status check start');
        if ($objRet->status === OWNERSSTORE_STATUS_SUCCESS) {
            $this->objLog->log('* get products list ok');
            echo $body;
            exit;
        } else {
            echo $body;
            $this->objLog->errLog($objRet->errcode, $objReq);
            exit;
        }
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        $this->objLog->end();
    }
}
?>
