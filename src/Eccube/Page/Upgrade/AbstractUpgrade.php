<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
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


namespace Eccube\Page\Upgrade;

use Eccube\Application;
use Eccube\Page\AbstractPage;
use Eccube\Framework\Query;
use Eccube\Framework\Session;

/**
 * オーナーズストアページクラスの基底クラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
abstract class AbstractUpgrade extends AbstractPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        $this->skip_load_page_layout = true;
        parent::init();
    }

    /**
     * 自動アップデートが有効かどうかを判定する.
     *
     * @param  integer $product_id
     * @return boolean
     */
    public function autoUpdateEnable($product_id)
    {
        $where = 'module_id = ?';
        $objQuery = Application::alias('eccube.query');
        $arrRet = $objQuery->select('auto_update_flg', 'dtb_module', $where, array($product_id));

        if (isset($arrRet[0]['auto_update_flg'])
        && $arrRet[0]['auto_update_flg'] === '1') {
            return true;
        }

        return false;
    }

    /**
     * 配信サーバーへリクエストを送信する.
     *
     * @param  string        $mode
     * @param  array         $arrParams 追加パラメーター.連想配列で渡す.
     * @return string|object レスポンスボディ|エラー時には\PEAR::Errorオブジェクトを返す.
     */
    public function request($mode, $arrParams = array(), $arrCookies = array())
    {
        $objReq = new \HTTP_Request();
        $objReq->setUrl(OSTORE_URL . 'upgrade/index.php');
        $objReq->setMethod('POST');
        $objReq->addPostData('mode', $mode);
        foreach ($arrParams as $key => $val) {
            $objReq->addPostData($key, $val);
        }

        foreach ($arrCookies as $cookie) {
            $objReq->addCookie($cookie['name'], $cookie['value']);
        }

        $e = $objReq->sendRequest();
        if (\PEAR::isError($e)) {
            return $e;
        } else {
            return $objReq;
        }
    }

    public function isLoggedInAdminPage()
    {
        $objSess = new Session();

        if ($objSess->isSuccess() === SUCCESS) {
            return true;
        }

        return false;
    }

    /**
     * 予測されにくいランダム値を生成する.
     *
     * @return string
     */
    public function createSeed()
    {
        return sha1(uniqid(rand(), true) . time());
    }

    public function getPublicKey()
    {
        $objQuery = Application::alias('eccube.query');
        $arrRet = $objQuery->select('*', 'dtb_ownersstore_settings');

        return isset($arrRet[0]['public_key'])
            ? $arrRet[0]['public_key']
            : null;
    }

    /**
     * オーナーズストアからの POST のため, トークンチェックしない.
     */
    public function doValidToken()
    {
        // nothing.
    }
}
