<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Page\Upgrade;

use Eccube\Application;
use Eccube\Page\Upgrade\Helper\LogHelper;
use Eccube\Page\Upgrade\Helper\JsonHelper;
use Eccube\Framework\View\AdminView;

/**
 * オーナーズストア購入商品一覧を返すページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class ProductsList extends AbstractUpgrade
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process($mode)
    {
        $objLog  = new LogHelper;
        $objJson = new JsonHelper;

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
        if (\PEAR::isError($objReq)) {
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
            $objView = new AdminView();
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
}
