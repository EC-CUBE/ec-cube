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

class LC_Utils_Upgrade {
    /**
     * ロックオンのIPかどうかを判定する.
     *
     * @return boolean
     */
    function isValidIP() {
        if (isset($_SERVER['REMOTE_ADDR'])
        && $_SERVER['REMOTE_ADDR'] == OWNERSSTORE_IP) {

            return true;
        }

        return false;
    }

    /**
     * 自動アップデートが有効かどうかを判定する.
     *
     * @param integer $product_id
     * @return boolean
     */
    function autoUpdateEnable($product_id) {
        $where = 'product_id = ?';
        $objQuery = new SC_Query();
        $arrRet = $objQuery->select('auto_update_flg', 'dtb_module', $where, array($product_id));

        if (isset($arrRet[0]['auto_update_flg'])
        && $arrRet[0]['auto_update_flg'] === '1') {

            return true;
        }

        return false;
    }

    /**
     * 配信サーバへリクエストを送信する.
     *
     * @param string $mode
     * @param array $arrParams 追加パラメータ.連想配列で渡す.
     * @return string|object レスポンスボディ|エラー時にはPEAR::Errorオブジェクトを返す.
     */
    function request($mode, $arrParams = array(), $arrCookies = array()) {
        $objReq = new HTTP_Request();
        $objReq->setUrl(OWNERSSTORE_URL . 'upgrade/index.php');
        $objReq->setMethod('POST');
        $objReq->addPostData('mode', $mode);
        $objReq->addPostData('site_url', SITE_URL);
        $objReq->addPostData('ssl_url', SSL_URL);
        $objReq->addPostDataArray($arrParams);

        foreach ($arrCookies as $cookie) {
            $objReq->addCookie($cookie['name'], $cookie['value']);
        }

        $e = $objReq->sendRequest();
        if (PEAR::isError($e)) {
            return $e;
        } else {
            return $objReq;
        }
    }

    function isLoggedInAdminPage() {
        $objSess = new SC_Session;

        if ($objSess->isSuccess() === SUCCESS) {
            return true;
        }
        return false;
    }

    function getErrMessage($errcode) {
        $masterData = new SC_DB_MasterData();
        $arrErrMsg = $masterData->getMasterData("mtb_ownersstore_err");
        return isset($arrErrMsg[$errcode])
            ? $arrErrMsg[$errcode] . " エラーコード:$errcode"
            : "配信サーバとの通信中にエラーが発生しました。エラーコード:$errcode";
    }
}
?>
