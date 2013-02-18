<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
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
require_once 'LC_Page_Upgrade_Base.php';

/**
 * オーナーズストア購入商品一覧を返すページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Upgrade_ProductsList extends LC_Page_Upgrade_Base 
{

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init()
    {
        parent::init();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process($mode)
    {
        $objLog  = new LC_Upgrade_Helper_Log;
        $objJson = new LC_Upgrade_Helper_Json;

        $objLog->start($mode);

        // 管理画面ログインチェック
        $objLog->log('* admin auth start');
        if ($this->isLoggedInAdminPage() !== true) {
            $objJson->setError(OSTORE_E_C_ADMIN_AUTH);
            $objJson->display();
            $objLog->error(OSTORE_E_C_ADMIN_AUTH);
            return;
        }

        // 認証キーの取得
        $public_key = $this->getPublicKey();
        $sha1_key = $this->createSeed();

        $objLog->log('* public key check start');
        if (empty($public_key)) {
            $objJson->setError(OSTORE_E_C_NO_KEY);
            $objJson->display();
            $objLog->error(OSTORE_E_C_NO_KEY);
            return;
        }

        // リクエストを開始
        $objLog->log('* http request start');
        $arrPostData = array(
            'eccube_url' => HTTP_URL,
            'public_key' => sha1($public_key . $sha1_key),
            'sha1_key'   => $sha1_key,
            'ver'        => ECCUBE_VERSION
        );
        $objReq = $this->request('products_list', $arrPostData);

        // リクエストチェック
        $objLog->log('* http request check start');
        if (PEAR::isError($objReq)) {
            $objJson->setError(OSTORE_E_C_HTTP_REQ);
            $objJson->display();
            $objLog->error(OSTORE_E_C_HTTP_REQ, $objReq);
            return;
        }

        // レスポンスチェック
        $objLog->log('* http response check start');
        if ($objReq->getResponseCode() !== 200) {
            $objJson->setError(OSTORE_E_C_HTTP_RESP);
            $objJson->display();
            $objLog->error(OSTORE_E_C_HTTP_RESP, $objReq);
            return;
        }

        $body = $objReq->getResponseBody();
        $objRet = $objJson->decode($body);

        // JSONデータのチェック
        $objLog->log('* json deta check start');
        if (empty($objRet)) {
            $objJson->setError(OSTORE_E_C_FAILED_JSON_PARSE);
            $objJson->display();
            $objLog->error(OSTORE_E_C_FAILED_JSON_PARSE, $objReq);
            return;
        }

        // ステータスチェック
        $objLog->log('* json status check start');
        if ($objRet->status === OSTORE_STATUS_SUCCESS) {
            $objLog->log('* get products list ok');

            $arrProducts = array();

            foreach ($objRet->data as $product) {
                $arrProducts[] = get_object_vars($product);
            }
            $objView = new SC_AdminView_Ex();
            $objView->assign('arrProducts', $arrProducts);

            $template = 'ownersstore/products_list.tpl';

            if (!$objView->_smarty->template_exists($template)) {
                $objLog->log('* template not exist, use default template');
                // デフォルトテンプレートを使用
                $template = DATA_REALDIR . 'Smarty/templates/default/admin/' . $template;
            }

            $html = $objView->fetch('ownersstore/products_list.tpl');
            $objJson->setSuccess(array(), $html);
            $objJson->display();
            $objLog->end();
            return;
        } else {
            // 配信サーバー側でエラーを補足
            echo $body;
            $objLog->error($objRet->errcode, $objReq);
            return;
        }
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy()
    {
        parent::destroy();
    }
}
