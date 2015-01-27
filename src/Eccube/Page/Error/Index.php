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
use Eccube\Page\AbstractPage;
use Eccube\Framework\Display;
use Eccube\Framework\Response;
use Eccube\Framework\SiteSession;
use Eccube\Framework\Helper\DbHelper;
use Eccube\Framework\Helper\HandleErrorHelper;
use Eccube\Framework\Helper\PluginHelper;

/**
 * エラー表示のページクラス
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Index extends AbstractPage
{
    /** エラー種別 */
    public $type;

    /** SiteSession インスタンス */
    public $objSiteSess;

    /** TOPへ戻るフラグ */
    public $return_top = false;

    /** エラーメッセージ */
    public $err_msg = '';

    /** モバイルサイトの場合 true */
    public $is_mobile = false;

    /** @var Display */
    public $objDisplay;

    /**
     * Page を初期化する.
     *
     * DBエラー発生時, エラーページを表示しようした際の DB 接続を防ぐため,
     * ここでは, parent::init() を行わない.
     * @return void
     */
    public function init()
    {
        HandleErrorHelper::$under_error_handling = true;

        $this->tpl_mainpage = 'error.tpl';
        $this->tpl_title = 'エラー';
        // ディスプレイクラス生成
        $this->objDisplay = Application::alias('eccube.display');

        $objHelperPlugin = PluginHelper::getSingletonInstance($this->plugin_activate_flg);
        if (is_object($objHelperPlugin)) {
            // transformでフックしている場合に, 再度エラーが発生するため, コールバックを無効化.
            $objHelperPlugin->arrRegistedPluginActions = array();
        }

        // キャッシュから店舗情報取得（DBへの接続は行わない）
        $this->arrSiteInfo = Application::alias('eccube.helper.db')->getBasisDataCache(false);
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        parent::process();
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
            case PRODUCT_NOT_FOUND:
                $this->tpl_error='ご指定のページはございません。';
                Application::alias('eccube.response')->sendHttpStatus(404);
                break;
            case PAGE_ERROR:
                $this->tpl_error='不正なページ移動です。';
                break;
            case CART_EMPTY:
                $this->tpl_error='カートに商品ががありません。';
                break;
            case CART_ADD_ERROR:
                $this->tpl_error='購入処理中は、カートに商品を追加することはできません。';
                break;
            case CANCEL_PURCHASE:
                $this->tpl_error='この手続きは無効となりました。以下の要因が考えられます。<br />・セッション情報の有効期限が切れてる場合<br />・購入手続き中に新しい購入手続きを実行した場合<br />・すでに購入手続きを完了している場合';
                break;
            case CATEGORY_NOT_FOUND:
                $this->tpl_error='ご指定のカテゴリは存在しません。';
                Application::alias('eccube.response')->sendHttpStatus(404);
                break;
            case SITE_LOGIN_ERROR:
                $this->tpl_error='メールアドレスもしくはパスワードが正しくありません。';
                break;
            case TEMP_LOGIN_ERROR:
                $this->tpl_error='メールアドレスもしくはパスワードが正しくありません。<br />本登録がお済みでない場合は、仮登録メールに記載されている<br />URLより本登録を行ってください。';
                break;
            case CUSTOMER_ERROR:
                $this->tpl_error='不正なアクセスです。';
                break;
            case SOLD_OUT:
                $this->tpl_error='申し訳ございませんが、ご購入の直前で売り切れた商品があります。この手続きは無効となりました。';
                break;
            case CART_NOT_FOUND:
                $this->tpl_error='申し訳ございませんが、カート内の商品情報の取得に失敗しました。この手続きは無効となりました。';
                break;
            case LACK_POINT:
                $this->tpl_error='申し訳ございませんが、ポイントが不足しております。この手続きは無効となりました。';
                break;
            case FAVORITE_ERROR:
                $this->tpl_error='既にお気に入りに追加されている商品です。';
                break;
            case EXTRACT_ERROR:
                $this->tpl_error="ファイルの解凍に失敗しました。\n指定のディレクトリに書き込み権限が与えられていない可能性があります。";
                break;
            case FTP_DOWNLOAD_ERROR:
                $this->tpl_error='ファイルのFTPダウンロードに失敗しました。';
                break;
            case FTP_LOGIN_ERROR:
                $this->tpl_error='FTPログインに失敗しました。';
                break;
            case FTP_CONNECT_ERROR:
                $this->tpl_error='FTPログインに失敗しました。';
                break;
            case CREATE_DB_ERROR:
                $this->tpl_error="DBの作成に失敗しました。\n指定のユーザーには、DB作成の権限が与えられていない可能性があります。";
                break;
            case DB_IMPORT_ERROR:
                $this->tpl_error="データベース構造のインポートに失敗しました。\nsqlファイルが壊れている可能性があります。";
                break;
            case FILE_NOT_FOUND:
                $this->tpl_error='指定のパスに、設定ファイルが存在しません。';
                break;
            case WRITE_FILE_ERROR:
                $this->tpl_error="設定ファイルに書き込めません。\n設定ファイルに書き込み権限を与えてください。";
                break;
            case DOWNFILE_NOT_FOUND:
                $this->tpl_error='ダウンロードファイルが存在しません。<br />申し訳ございませんが、店舗までお問合わせ下さい。';
                break;
            case FREE_ERROR_MSG:
                $this->tpl_error=$this->err_msg;
                break;
            default:
                $this->tpl_error='エラーが発生しました。';
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
