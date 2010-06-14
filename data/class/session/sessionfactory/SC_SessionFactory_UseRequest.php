<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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
require_once CLASS_PATH . 'session/SC_SessionFactory.php';
require_once CLASS_EX_PATH . "helper_extends/SC_Helper_Mobile_Ex.php";

/**
 * Cookieを使用せず、リクエストパラメータによりセッションを継続する設定を行うクラス.
 *
 * このクラスを直接インスタンス化しないこと.
 * 必ず SC_SessionFactory クラスを経由してインスタンス化する.
 * また, SC_SessionFactory クラスの関数を必ずオーバーライドしている必要がある.
 *
 * @package SC_SessionFactory
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class SC_SessionFactory_UseRequest extends SC_SessionFactory {

    var $state = null;

    // }}}
    // {{{ functions

    /**
     * PC/モバイルのセッション管理オブジェクトを切り替える
     *
     * @param string $state
     */
    function setState($state = 'pc') {
        switch($state) {
        case 'mobile':
            $this->state = new LC_UseRequest_State_Mobile;
            break;

        case 'pc':
        default:
            $this->state = new LC_UseRequest_State_PC;
            break;
        }
    }

    /**
     * Cookieを使用するかどうか
     *
     * @return boolean 常にfalseを返す
     */
    function useCookie() {
        return false;
    }

    /**
     * dtb_mobile_ext_session_id テーブルを検索してセッションIDを取得する。
     * PCサイトでもモバイルサイトでもこのテーブルを利用する.
     *
     * @return string|null 取得したセッションIDを返す。
     *                     取得できなかった場合は null を返す。
     */
    function getExtSessionId() {
        if (!preg_match('|^' . URL_DIR . '(.*)$|', $_SERVER['SCRIPT_NAME'], $matches)) {
            return null;
        }

        $url = $matches[1];
        $lifetime = $this->state->getLifeTime();
        $time = date('Y-m-d H:i:s', time() - $lifetime);
        $objQuery = new SC_Query;

        foreach ($_REQUEST as $key => $value) {
            $session_id = $objQuery->get('dtb_mobile_ext_session_id', 'session_id',
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
     * @param string $param_key パラメーター名
     * @param string $param_value パラメーター値
     * @param string $url URL
     * @return void
     */
    function setExtSessionId($param_key, $param_value, $url) {
        $objQuery = new SC_Query;

        // GC
        $lifetime = $this->state->getLifeTime();
        $time = date('Y-m-d H:i:s', time() - $lifetime);
        $objQuery->delete('dtb_mobile_ext_session_id', 'create_date < ?', array($time));

        $arrValues = array('session_id'  => session_id(),
                           'param_key'   => $param_key,
                           'param_value' => $param_value,
                           'url'         => $url);

        $objQuery->insert('dtb_mobile_ext_session_id', $arrValues);
    }

    /**
     * セッションデータが有効かどうかをチェックする。
     *
     * @return boolean セッションデータが有効な場合は true、無効な場合は false を返す。
     */
    function validateSession() {
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
    function getSessionId() {
        // パラメーターからセッションIDを取得する。
        $sessionId = @$_POST[session_name()];
        if (!isset($sessionId)) {
            $sessionId = @$_GET[session_name()];
        }
        if (!isset($sessionId)) {
            $sessionId = $this->getExtSessionId();
        }
        if (!isset($sessionId)) {
            return false;
        }

        // セッションIDの存在をチェックする。
        $objSession = new SC_Helper_Session_Ex();
        if ($objSession->sfSessRead($sessionId) === null) {
            GC_Utils_Ex::gfPrintLog("Non-existent session id : sid=$sessionId");
            return false;
        }
        return session_id($sessionId);
    }

    /**
     * セッション初期処理を行う。
     *
     * @return void
     */
    function initSession() {
        // セッションIDの受け渡しにクッキーを使用しない。
        ini_set('session.use_cookies', '0');
        ini_set('session.use_trans_sid', '1');

        // パラメーターから有効なセッションIDを取得する。
        $sessionId = $this->getSessionId();

        if (!$sessionId) {
            session_start();
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
                $objMobile = new SC_Helper_Mobile_Ex;
                header('Location: ' . $objMobile->gfAddSessionId());
            } else {
                // GET 以外の場合はトップページへリダイレクトする。
                header('Location: ' . URL_SITE_TOP . '?' . SID);
            }
            exit;
        }

        // 有効期限を更新する.
        $this->state->updateExpire();
    }
}
/**
 * セッションデータ管理クラスの基底クラス
 *
 */
class LC_UseRequest_State {
    /** 名前空間(pc/mobile) */
    var $namespace = '';
    /** 有効期間 */
    var $lifetime  = 0;
    /** エラーチェック関数名の配列 */
    var $validate  = array();

    /**
     * 名前空間を取得する
     *
     * @return string
     */
    function getNameSpace() { return $this->namespace; }

    /**
     * 有効期間を取得する
     *
     * @return integer
     */
    function getLifeTime() { return $this->lifetime; }

    /**
     * セッションデータが設定されているかを判定する.
     * $_SESSION[$namespace]の値が配列の場合に
     * trueを返す.
     *
     * @return boolean
     */
    function validateNameSpace() {
        $namespace = $this->getNameSpace();
        if (isset($_SESSION[$namespace]) && is_array($_SESSION[$namespace])) {
            return true;
        }
        GC_Utils_Ex::gfPrintLog("NameSpace $namespace not found in session data : sid=" . session_id());
        return false;
    }

    /**
     * セッションのデータを取得する
     * 取得するデータは$_SESSION[$namespace][$key]となる.
     *
     * @param string $key
     * @return mixed|null
     */
    function getValue($key) {
        $namespace = $this->getNameSpace();
        return isset($_SESSION[$namespace][$key])
            ? $_SESSION[$namespace][$key]
            : null;
    }

    /**
     * セッションにデータを登録する.
     * $_SESSION[$namespace][$key] = $valueの形で登録される.
     *
     * @param string $key
     * @param mixed $value
     */
    function setValue($key, $value) {
        $namespace = $this->getNameSpace();
        $_SESSION[$namespace][$key] = $value;
    }

    /**
     * 有効期限を取得する.
     *
     * @return integer
     */
    function getExpire() {
        return $this->getValue('expires');
    }

    /**
     * 有効期限を設定する.
     *
     */
    function updateExpire() {
        $lifetime = $this->getLifeTime();
        $this->setValue('expires', time() + $lifetime);
    }

    /**
     * 有効期限内かどうかを判定する.
     *
     * @return boolean
     */
    function validateExpire() {
        $expire = $this->getExpire();
        if (intval($expire) > time()) {
            return true;
        }
        $date = date('Y/m/d H:i:s', $expire);
        GC_Utils_Ex::gfPrintLog("Session expired at $date : sid=" . session_id());
        return false;
    }

    /**
     * IPアドレスを取得する.
     *
     * @return string
     */
    function getIp() {
        return $this->getValue('ip');
    }

    /**
     * IPアドレスを設定する.
     *
     */
    function updateIp() {
        $this->setValue('ip', $_SERVER['REMOTE_ADDR']);
    }

    /**
     * REMOTE_ADDRとセッション中のIPが同じかどうかを判定する.
     * 同じ場合にtrueが返る
     *
     * @return boolean
     */
    function validateIp() {
        $ip = $this->getIp();
        if (!empty($_SERVER['REMOTE_ADDR'])
         && $ip === $_SERVER['REMOTE_ADDR']) {

            return true;
        }

        $msg = sprintf('Ip Addr mismatch : %s != %s(expected) : sid=%s',
                       $_SERVER['REMOTE_ADDR'], $ip, session_id());
        GC_Utils_Ex::gfPrintLog($msg);
        return false;
    }

    /**
     * UserAgentもしくは携帯の機種名を取得する.
     *
     * @return string
     */
    function getModel() {
        return $this->getValue('model');
    }

    /**
     * セッション中のデータ検証する
     *
     * @return boolean
     */
    function validateSessionData() {
        foreach ($this->validate as $method) {
            $method = 'validate' . $method;
            if (!$this->$method()) {
                return false;
            }
        }
        return true;
    }

    /**
     * セッションデータを初期化する.
     *
     */
    function inisializeSessionData() {}
}

/**
 * PCサイト用のセッションデータ管理クラス
 *
 */
class LC_UseRequest_State_PC extends LC_UseRequest_State {

    /**
     * コンストラクタ
     * セッションのデータ構造は下のようになる.
     * $_SESSION["pc"]=> array(
     *     ["model"]   => "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)"
     *     ["ip"]      => "127.0.0.1"
     *     ["expires"] => 1204699031
     * )
     *
     * @return LC_UseRequest_State_PC
     */
    function LC_UseRequest_State_PC() {
        $this->namespace = 'pc';
        $this->lifetime  = SESSION_LIFETIME;
        $this->validate  = array('NameSpace', 'Model', 'Ip', 'Expire');
    }

    /**
     * セッションにUserAgentを設定する.
     *
     */
    function updateModel() {
        $this->setValue('model', $_SERVER['HTTP_USER_AGENT']);
    }

    /**
     * UserAgentを検証する.
     *
     * @return boolean
     */
    function validateModel() {
        $ua = $this->getModel();
        if (!empty($_SERVER['HTTP_USER_AGENT'])
         && $_SERVER['HTTP_USER_AGENT'] === $ua) {

            return true;
        }
        $msg = sprintf("User agent model mismatch : %s != %s(expected), sid=%s",
                       $_SERVER['HTTP_USER_AGENT'], $ua, session_id());
        GC_Utils_Ex::gfPrintLog($msg);
        return false;
    }

    /**
     * セッションデータを初期化する.
     *
     */
    function inisializeSessionData() {
        $_SESSION = array();
        $this->updateModel();
        $this->updateIp();
        $this->updateExpire();
    }
}

/**
 * モバイルサイト用のセッションデータ管理クラス
 *
 */
class LC_UseRequest_State_Mobile extends LC_UseRequest_State {

    /**
     * コンストラクタ
     * セッションのデータ構造は下のようになる.
     * $_SESSION["mobile"]=> array(
     *     ["model"]   => 901sh
     *     ["ip"]      => 127.0.0.1
     *     ["expires"] => 1204699031
     *     ["phone_id"]=> ****
     * )
     *
     * @return LC_UseRequest_State_Mobile
     */
    function LC_UseRequest_State_Mobile() {
        $this->namespace = 'mobile';
        $this->lifetime  = MOBILE_SESSION_LIFETIME;
        $this->validate  = array('NameSpace', 'Model', 'Expire');
    }

    /**
     * 携帯の機種名を設定する
     *
     */
    function updateModel() {
        $this->setValue('model', SC_MobileUserAgent::getModel());
    }

    /**
     * セッション中の携帯機種名と、アクセスしてきたブラウザの機種名が同じかどうかを判定する
     *
     * @return boolean
     */
    function validateModel() {
        $modelInSession = $this->getModel();
        $model = SC_MobileUserAgent::getModel();
        if (!empty($model)
         && $model === $modelInSession) {

            return true;
        }
        return false;
    }

    /**
     * 携帯のIDを取得する
     *
     * @return string
     */
    function getPhoneId() {
        return $this->getValue('phone_id');
    }

    /**
     * 携帯のIDを登録する.
     *
     */
    function updatePhoneId() {
        $this->setValue('phone_id', SC_MobileUserAgent::getId());
    }

    /**
     * セッションデータを初期化する.
     *
     */
    function inisializeSessionData() {
        $_SESSION = array();
        $this->updateModel();
        $this->updateIp();
        $this->updateExpire();
        $this->updatePhoneId();
    }
}
/*
 * Local variables:
 * coding: utf-8
 * End:
 */
?>
