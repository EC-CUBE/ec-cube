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

namespace Eccube\Framework\SessionFactory;

use Eccube\Application;
use Eccube\Framework\MobileUserAgent;
use Eccube\Framework\Query;
use Eccube\Framework\SessionFactory;
use Eccube\Framework\Helper\MobileHelper;
use Eccube\Framework\Util\GcUtils;
use Eccube\Framework\SessionFactory\UseRequest\PcState;
use Eccube\Framework\SessionFactory\UseRequest\MobileState;

/**
 * Cookieを使用せず、リクエストパラメーターによりセッションを継続する設定を行うクラス.
 *
 * このクラスを直接インスタンス化しないこと.
 * 必ず SessionFactory クラスを経由してインスタンス化する.
 * また, SessionFactory クラスの関数を必ずオーバーライドしている必要がある.
 *
 * @package SessionFactory
 * @author LOCKON CO.,LTD.
 */
class UseRequestSessionFactory extends SessionFactory
{
    /** @var MobileState|PcState  */
    public $state = null;

    /**
     * PC/モバイルのセッション管理オブジェクトを切り替える
     *
     * @param string $state
     */
    public function setState($state = 'pc')
    {
        switch ($state) {
            case 'mobile':
                $this->state = new MobileState;
                break;

            case 'pc':
            default:
                $this->state = new PcState;
                break;
        }
    }

    /**
     * Cookieを使用するかどうか
     *
     * @return boolean 常にfalseを返す
     */
    public function useCookie()
    {
        return false;
    }

    /**
     * dtb_mobile_ext_session_id テーブルを検索してセッションIDを取得する。
     * PCサイトでもモバイルサイトでもこのテーブルを利用する.
     *
     * @return string|null 取得したセッションIDを返す。
     *                     取得できなかった場合は null を返す。
     */
    public function getExtSessionId()
    {
        if (!preg_match('|^' . ROOT_URLPATH . '(.*)$|', $_SERVER['SCRIPT_NAME'], $matches)) {
            return null;
        }

        $url = $matches[1];
        $lifetime = $this->state->getLifeTime();
        $time = date('Y-m-d H:i:s', time() - $lifetime);
        $objQuery = Application::alias('eccube.query');

        foreach ($_REQUEST as $key => $value) {
            $session_id = $objQuery->get('session_id', 'dtb_mobile_ext_session_id',
                                         'param_key = ? AND param_value = ? AND url = ? AND create_date >= ?',
                                         array($key, $value, $url, $time));
            if (isset($session_id)) {
                return $session_id;
            }
        }

        return null;
    }

    /**
     * 外部サイト連携用にセッションIDとパラメーターの組み合わせを保存する。
     *
     * @param  string $param_key   パラメーター名
     * @param  string $param_value パラメーター値
     * @param  string $url         URL
     * @return void
     */
    public function setExtSessionId($param_key, $param_value, $url)
    {
        $objQuery = Application::alias('eccube.query');

        // GC
        $lifetime = $this->state->getLifeTime();
        $time = date('Y-m-d H:i:s', time() - $lifetime);
        $objQuery->delete('dtb_mobile_ext_session_id', 'create_date < ?', array($time));

        $arrValues = array(
            'session_id'  => session_id(),
            'param_key'   => $param_key,
            'param_value' => $param_value,
            'url'         => $url,
        );

        $objQuery->insert('dtb_mobile_ext_session_id', $arrValues);
    }

    /**
     * セッションデータが有効かどうかをチェックする。
     *
     * @return boolean セッションデータが有効な場合は true、無効な場合は false を返す。
     */
    public function validateSession()
    {
        /**
         * PCサイトでは
         *  ・セッションデータが適切に設定されているか
         *  ・UserAgent
         *  ・IPアドレス
         *  ・有効期限
         * モバイルサイトでは
         *  ・セッションデータが適切に設定されているか
         *  ・機種名
         *  ・IPアドレス
         *  ・有効期限
         *  ・phone_id
         * がチェックされる
         */

        return $this->state->validateSessionData();
    }

    /**
     * パラメーターから有効なセッションIDを取得する。
     *
     * @return string|false 取得した有効なセッションIDを返す。
     *                      取得できなかった場合は false を返す。
     */
    public function getSessionId()
    {
        // パラメーターからセッションIDを取得する。
        $sessionId = @$_POST[session_name()];
        if (!isset($sessionId)) {
            $sessionId = @$_GET[session_name()];
            // AU動画音声ファイルダウンロード対策
            // キャリアがAUで、動画、音声ファイルをダウンロードする際に
            // SESSIONIDの後に余計なパラメータが付与され、セッションが無効になるケースがある
            if (MobileUserAgent::getCarrier() == 'ezweb') {
                $idArray = split("\?", $sessionId);
                $sessionId = $idArray[0];
            }
        }
        if (!isset($sessionId)) {
            $sessionId = $this->getExtSessionId();
        }
        if (!isset($sessionId)) {
            return false;
        }

        // セッションIDの存在をチェックする。
        if ($this->sfSessRead($sessionId) === null) {
            GcUtils::gfPrintLog("Non-existent session id : sid=$sessionId");

            return false;
        }

        return session_id($sessionId);
    }

    /**
     * セッション初期処理を行う。
     *
     * @return void
     */
    public function initSession()
    {
        parent::initSession();

        // セッションIDの受け渡しにクッキーを使用しない。
        ini_set('session.use_cookies', '0');
        ini_set('session.use_trans_sid', '1');
        ini_set('session.use_only_cookies', '0');

        // パラメーターから有効なセッションIDを取得する。
        $sessionId = $this->getSessionId();

        if (!$sessionId) {
            session_start();
        }

        /*
         * PHP4 では session.use_trans_sid が PHP_INI_PREDIR なので
         * ini_set() で設定できない
         */
        if (!ini_get('session.use_trans_sid')) {
            output_add_rewrite_var(session_name(), session_id());
        }

        // セッションIDまたはセッションデータが無効な場合は、セッションIDを再生成
        // し、セッションデータを初期化する。
        if ($sessionId === false || !$this->validateSession()) {
            session_regenerate_id(true);
            // セッションデータの初期化
            $this->state->inisializeSessionData();

            // 新しいセッションIDを付加してリダイレクトする。
            if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                // GET の場合は同じページにリダイレクトする。
                $objMobile = new MobileHelper;
                header('Location: ' . $objMobile->gfAddSessionId());
            } else {
                // GET 以外の場合はトップページへリダイレクトする。
                header('Location: ' . TOP_URL . '?' . SID);
            }
            exit;
        }

        // 有効期限を更新する.
        $this->state->updateExpire();
    }
}
