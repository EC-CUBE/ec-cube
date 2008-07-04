<?php
/**
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
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
require_once(DATA_PATH. 'module/Request.php');

class LC_Helper_Send_Payment {
    function sendPaymentData($module_code, $total) {
        // ostore_url
        define('OSTORE_SSLURL', "https://store.ec-cube.net/");
        // check flag
        $store_host = preg_replace("@(^https?://)|(/+$)@", "", OSTORE_SSLURL);
        $fp = @fsockopen($store_host, 80, $errno, $errstr, 5);

        if ($fp !== false) {
            // set request
            $url = OSTORE_SSLURL. "payment/payment.php";
            $req = new HTTP_Request($url);
            $req->setMethod(HTTP_REQUEST_METHOD_POST);

            // set param
            $arrPost = array(
                "module_code" => $module_code,
                "site_url" => SITE_URL,
                "total" => $total
            );

            // send data
            $req->addPostDataArray($arrPost);
            $response = $req->sendRequest();
            $req->clearPostData();
        }
    }
}
?>