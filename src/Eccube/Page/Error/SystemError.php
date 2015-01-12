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
use Eccube\Framework\Response;
use Eccube\Framework\Util\GcUtils;

/**
 * システムエラー表示のページクラス
 * システムエラーや例外が発生した場合の表示ページ
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class SystemError extends Index
{
    /** \PEAR_Error */
    public $pearResult;

    /** \PEAR_Error がセットされていない場合用のバックトレーススタック */
    public $backtrace;

    /** デバッグ用のメッセージ配列 */
    public $arrDebugMsg = array();

    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_title = 'システムエラー';
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

        $this->tpl_error = 'システムエラーが発生しました。<br />大変お手数ですが、サイト管理者までご連絡ください。';

        if (DEBUG_MODE) {
            echo '<div class="debug">';
            echo '<div>▼▼▼ デバッグ情報ここから ▼▼▼</div>';
            echo '<pre>';
            echo htmlspecialchars($this->sfGetErrMsg(), ENT_QUOTES, CHAR_CODE);
            echo '</pre>';
            echo '<div>▲▲▲ デバッグ情報ここまで ▲▲▲</div>';
            echo '</div>';
        }

    }

    /**
     * Page のレスポンス送信.
     *
     * @return void
     */
    public function sendResponse()
    {
        $this->adminPage = GcUtils::isAdminFunction();

        if ($this->adminPage) {
            $this->tpl_mainpage = 'login_error.tpl';
            $this->template = LOGIN_FRAME;
            $this->objDisplay->prepare($this, true);
        } else {
            $this->objDisplay->prepare($this);
        }

        $this->objDisplay->response->write();
    }

    /**
     * トランザクショントークンに関して処理しないようにオーバーライド
     */
    public function doValidToken()
    {
    }

    /**
     * トランザクショントークンに関して処理しないようにオーバーライド
     */
    public function setTokenTo()
    {
    }

    /**
     * エラーメッセージを生成する
     *
     * @return string
     */
    public function sfGetErrMsg()
    {
        $errmsg = '';
        $errmsg .= $this->lfGetErrMsgHead();
        $errmsg .= "\n";

        // デバッグ用のメッセージが指定されている場合
        if (!empty($this->arrDebugMsg)) {
            $errmsg .= implode("\n\n", $this->arrDebugMsg) . "\n";
        }

        // PEAR エラーを伴う場合
        if (!is_null($this->pearResult)) {
            $errmsg .= $this->pearResult->message . "\n\n";
            $errmsg .= $this->pearResult->userinfo . "\n\n";
            $errmsg .= GcUtils::toStringBacktrace($this->pearResult->backtrace);
        // (上に該当せず)バックトレーススタックが指定されている場合
        } else if (is_array($this->backtrace)) {
            $errmsg .= GcUtils::toStringBacktrace($this->backtrace);
        } else {
            $arrBacktrace = GcUtils::getDebugBacktrace();
            $errmsg .= GcUtils::toStringBacktrace($arrBacktrace);
        }

        return $errmsg;
    }

    /**
     * エラーメッセージの冒頭部を生成する
     *
     * @return string
     */
    public function lfGetErrMsgHead()
    {
        $errmsg = '';
        $errmsg .= GcUtils::getUrl() . "\n";
        $errmsg .= "\n";
        $errmsg .= 'SERVER_ADDR: ' . $_SERVER['SERVER_ADDR'] . "\n";
        $errmsg .= 'REMOTE_ADDR: ' . $_SERVER['REMOTE_ADDR'] . "\n";
        $errmsg .= 'USER_AGENT: ' . $_SERVER['HTTP_USER_AGENT'] . "\n";

        return $errmsg;
    }

    /**
     * デバッグ用のメッセージを追加
     *
     * @param string $debugMsg
     * @return void
     */
    public function addDebugMsg($debugMsg)
    {
        $this->arrDebugMsg[] = rtrim($debugMsg, "\n");
    }
}
