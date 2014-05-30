<?php
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';
require_once CLASS_REALDIR . 'pages/upgrade/helper/LC_Upgrade_Helper_Log.php';
require_once CLASS_REALDIR . 'pages/upgrade/helper/LC_Upgrade_Helper_Json.php';

/**
 * オーナーズストアページクラスの基底クラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Upgrade_Base extends LC_Page_Ex
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
        $objQuery =& SC_Query_Ex::getSingletonInstance();
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
     * @return string|object レスポンスボディ|エラー時にはPEAR::Errorオブジェクトを返す.
     */
    public function request($mode, $arrParams = array(), $arrCookies = array())
    {
        $objReq = new HTTP_Request();
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
        if (PEAR::isError($e)) {
            return $e;
        } else {
            return $objReq;
        }
    }

    public function isLoggedInAdminPage()
    {
        $objSess = new SC_Session_Ex();

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
        $objQuery =& SC_Query_Ex::getSingletonInstance();
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
