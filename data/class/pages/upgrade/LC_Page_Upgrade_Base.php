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
