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

namespace Eccube\Page\Error;

use Eccube\Application;
use Eccube\Page\Admin\AbstractAdminPage;
use Eccube\Framework\Display;
use Eccube\Framework\Response;
use Eccube\Framework\Helper\DbHelper;
use Eccube\Framework\Helper\HandleErrorHelper;
use Eccube\Framework\Helper\PluginHelper;

/**
 * エラー表示のページクラス
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class DispError extends AbstractAdminPage
{

    /** @var Display */
    public $objDisplay;

    /**
     * Page を初期化する.
     * LC_Page_Adminクラス内でエラーページを表示しようとした際に無限ループに陥るのを防ぐため,
     * ここでは, parent::init() を行わない.(フロントのエラー画面出力と同様の仕様)
     *
     * @return void
     */
    public function init()
    {
        HandleErrorHelper::$under_error_handling = true;

        $this->template = LOGIN_FRAME;
        $this->tpl_mainpage = 'login_error.tpl';
        $this->tpl_title = 'ログインエラー';
        // ディスプレイクラス生成
        $this->objDisplay = Application::alias('eccube.display');

        // transformでフックしている場合に, 再度エラーが発生するため, コールバックを無効化.
        $objHelperPlugin = PluginHelper::getSingletonInstance($this->plugin_activate_flg);
        $objHelperPlugin->arrRegistedPluginActions = array();

        // キャッシュから店舗情報取得（DBへの接続は行わない）
        $this->arrSiteInfo = Application::alias('eccube.helper.db')->getBasisDataCache(false);
    }

    /**
     * Page のプロセス。
     *
     * @return void
     */
    public function process()
    {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のプロセス。
     *
     * @return void
     */
    public function action()
    {
        Application::alias('eccube.response')->sendHttpStatus(500);

        switch ($this->type) {
            case LOGIN_ERROR:
                $this->tpl_error='ＩＤまたはパスワードが正しくありません。<br />もう一度ご確認のうえ、再度入力してください。';
                break;
            case ACCESS_ERROR:
                $this->tpl_error='ログイン認証の有効期限切れの可能性があります。<br />もう一度ご確認のうえ、再度ログインしてください。';
                break;
            case AUTH_ERROR:
                $this->tpl_error='このページにはアクセスできません';
                Application::alias('eccube.response')->sendHttpStatus(403);
                break;
            case INVALID_MOVE_ERRORR:
                $this->tpl_error='不正なページ移動です。<br />もう一度ご確認のうえ、再度入力してください。';
                break;
            default:
                $this->tpl_error='エラーが発生しました。<br />もう一度ご確認のうえ、再度ログインしてください。';
                break;
        }

    }

    /**
     * エラーページではトランザクショントークンの自動検証は行わない
     */
    public function doValidToken()
    {
        // queit.
    }
}
