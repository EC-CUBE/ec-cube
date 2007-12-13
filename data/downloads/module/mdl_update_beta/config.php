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
//$_SERVER['REQUEST_METHOD'] = 'POST';
//$_POST['mode'] = 'products_list';


require_once DATA_PATH . 'module/Request.php';
require_once CLASS_PATH . 'pages/upgrade/helper/LC_Upgrade_Helper_Log.php';
require_once CLASS_PATH . 'pages/upgrade/helper/LC_Upgrade_Helper_Json.php';

/*
        $objReq = new HTTP_Request();
        
        $objReq->setUrl(SITE_URL . 'upgrade/index.php');
        $objReq->setMethod('POST');
        //$objReq->addPostData('mode', 'patch_download');
        $objReq->addPostData('mode', 'auto_update');
        
        
        $public_key = '2a30f7e92eaae68fe21caff07b0de80ddf808002';
        $sha1_key = 'aaaaa';
        $arrPostData = array(
            'eccube_url' => SITE_URL,
            'public_key' => sha1($public_key . $sha1_key),
            'sha1_key'   => $sha1_key,
            'patch_code' => 'latest',
            'product_id' => '0'
        );
        $objReq->addPostDataArray($arrPostData);
        $e = $objReq->sendRequest();
        /*
        if (PEAR::isError($e)) {
            return $e;
        } else {
            return $objReq;
        }
        */
        $body = $objReq->getResponseBody();
        $objJson = new LC_Upgrade_Helper_Json;
        $objRet = $objJson->decode($body);
  
        print_r($objRet);
*/

?>