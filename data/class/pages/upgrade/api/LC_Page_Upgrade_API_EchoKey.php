<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once CLASS_PATH . 'pages/LC_Page.php';
require_once DATA_PATH  . 'module/Services/JSON.php';

/**
 * XXX のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Upgrade_API_EchoKey extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objJSON = new Services_JSON();
        // リクエストの検証
        if ($this->isValidRequest() !== true) {
            // TODO Bad Requestを返すように変更する
            exit;
        }

        $public_key = $this->getPublicKey();

        // 認証キーが設定されていない場合
        if (is_null($public_key)) {
            // TODO データ形式の統一が必要
            echo serialize('エラーメッセージ');
            exit;
        }

        // 認証キー + 配信サーバから送られるランダムな値をsha1()にかけechoする
        echo $objJSON->encode(array('body' => sha1($public_key . $_POST['seed'])));
        exit;
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /**
     * リクエストの検証
     *
     * 下の3条件を満たすとき, trueを返す
     * 1)POSTメソッドである
     * 2)$_POST['seed']に値がある
     * 3)ロックオンのIPからのアクセスである
     *
     * @param void
     * @return boolean
     */
    function isValidRequest() {
        // TODO 未実装
        return true;
    }

    /**
     * DBから認証キーを取得する
     * 無い場合はnullを返す
     *
     * @param void
     * @return string|null 認証キー
     */
    function getPublicKey() {
        $table  = 'dtb_application_settings';
        $col    = 'public_key';
        $where  = 'application_settings_id = 1';

        $objQuery = new SC_Query();

        $arrRet = $objQuery->select($col, $table, $where);

        if (isset($arrRet[0]['public_key'])) {
            return $arrRet[0]['public_key'];
        }

        return null;
    }
}
?>
