<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(DATA_PATH . 'module/Net/URL.php');
require_once(CLASS_PATH . "SC_DbConn.php");
require_once(CLASS_PATH . 'SC_Query.php');

/**
 * モバイルのヘルパークラス.
 *
 * @package Helper
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class SC_Helper_Mobile {

    /**
     * EC-CUBE がサポートする携帯端末かどうかをチェックする。
     * 非対応端末の場合は unsupported/index.php へリダイレクトする。
     *
     * @return void
     */
    function lfMobileCheckCompatibility() {
        if (!GC_MobileUserAgent::isSupported()) {
            header('Location: ' . URL_DIR . 'unsupported/index.php');
            exit;
        }
    }

    /**
     * 入力データを内部エンコーディングに変換し、絵文字を除去する。
     *
     * @param string &$value 入力データへの参照
     * @return void
     */
    function lfMobileConvertInputValue(&$value) {
        // Shift JIS から内部エンコーディングに変換する。
        // SoftBank 以外の絵文字は外字領域に含まれるため、この段階で除去される。
        $value = mb_convert_encoding($value, CHAR_CODE, 'SJIS');

        // SoftBank の絵文字を除去する。
        $value = preg_replace('/\\x1b\\$[^\\x0f]*\\x0f/', '', $value);
    }

    /**
     * モバイルサイト用の入力の初期処理を行う。
     *
     * @return void
     */
    function lfMobileInitInput() {
        array_walk($_GET, array($this, 'lfMobileConvertInputValue'));
        array_walk($_POST, array($this, 'lfMobileConvertInputValue'));
        array_walk($_REQUEST, array($this, 'lfMobileConvertInputValue'));
    }

    /**
     * dtb_mobile_ext_session_id テーブルを検索してセッションIDを取得する。
     *
     * @return string|null 取得したセッションIDを返す。
     *                     取得できなかった場合は null を返す。
     */
    function lfMobileGetExtSessionId() {
        if (!preg_match('|^' . URL_DIR . '(.*)$|', $_SERVER['SCRIPT_NAME'], $matches)) {
            return null;
        }

        $url = $matches[1];
        $time = date('Y-m-d H:i:s', time() - MOBILE_SESSION_LIFETIME);
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
     * パラメーターから有効なセッションIDを取得する。
     *
     * @return string|false 取得した有効なセッションIDを返す。
     *                      取得できなかった場合は false を返す。
     */
    function lfMobileGetSessionId() {
        // パラメーターからセッションIDを取得する。
        $sessionId = @$_POST[session_name()];
        if (!isset($sessionId)) {
            $sessionId = @$_GET[session_name()];
        }
        if (!isset($sessionId)) {
            $sessionId = $this->lfMobileGetExtSessionId();
        }
        if (!isset($sessionId)) {
            return false;
        }

        // セッションIDのフォーマットをチェックする。
        if (preg_match('/^[0-9a-zA-Z,-]{32,}$/', $sessionId) < 1) {
            GC_Utils_Ex::gfPrintLog("Invalid session id : sid=$sessionId");
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
     * セッションデータが有効かどうかをチェックする。
     *
     * @return boolean セッションデータが有効な場合は true、無効な場合は false を返す。
     */
    function lfMobileValidateSession() {
        // 配列 mobile が登録されているかどうかをチェックする。
        if (!is_array(@$_SESSION['mobile'])) {
            return false;
        }

        // 有効期限を過ぎていないかどうかをチェックする。
        if (intval(@$_SESSION['mobile']['expires']) < time()) {
            gfPrintLog("Session expired at " .
                       date('Y/m/d H:i:s', @$_SESSION['mobile']['expires']) .
                       ' : sid=' . session_id());

            return false;
        }

        // 携帯端末の機種が一致するかどうかをチェックする。
        $model = GC_MobileUserAgent::getModel();
        if (@$_SESSION['mobile']['model'] != $model) {
            gfPrintLog("User agent model mismatch : " .
                       "\"$model\" != \"" . @$_SESSION['mobile']['model'] .
                       '" (expected), sid=' . session_id());
            return false;
        }

        return true;
    }

    /**
     * モバイルサイト用のセッション関連の初期処理を行う。
     *
     * @return void
     */
    function lfMobileInitSession() {
        // セッションIDの受け渡しにクッキーを使用しない。
        ini_set('session.use_cookies', '0');

        // パラメーターから有効なセッションIDを取得する。
        $sessionId = $this->lfMobileGetSessionId();

        session_start();

        // セッションIDまたはセッションデータが無効な場合は、セッションIDを再生成
        // し、セッションデータを初期化する。
        if ($sessionId === false || !$this->lfMobileValidateSession()) {
            session_regenerate_id();
            $_SESSION = array('mobile' => array('model'    => GC_MobileUserAgent::getModel(),
                                                'phone_id' => GC_MobileUserAgent::getId(),
                                                'expires'  => time() + MOBILE_SESSION_LIFETIME));

            // 新しいセッションIDを付加してリダイレクトする。
            if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                // GET の場合は同じページにリダイレクトする。
                header('Location: ' . $this->gfAddSessionId());
            } else {
                // GET 以外の場合はトップページへリダイレクトする。
                header('Location: ' . URL_SITE_TOP . '?' . SID);
            }
            exit;
        }

        // 携帯端末IDを取得できた場合はセッションデータに保存する。
        $phoneId = GC_MobileUserAgent::getId();
        if ($phoneId !== false) {
            $_SESSION['mobile']['phone_id'] = $phoneId;
        }

        // セッションの有効期限を更新する。
        $_SESSION['mobile']['expires'] = time() + MOBILE_SESSION_LIFETIME;
    }

    /**
     * モバイルサイト用の出力の初期処理を行う。
     *
     * 出力の流れ
     * 1. Smarty
     * 2. 内部エンコーディングから Shift JIS に変換する。
     * 3. 全角カタカナを半角カタカナに変換する。
     * 4. 画像用のタグを調整する。
     * 5. 絵文字タグを絵文字コードに変換する。
     * 6. 出力
     *
     * @return void
     */
    function lfMobileInitOutput() {
        // Smarty 用のディレクトリーを作成する。
        @mkdir(COMPILE_DIR);

        // 出力用のエンコーディングを Shift JIS に固定する。
        mb_http_output('SJIS-win');

        // 絵文字タグを絵文字コードに変換する。
        ob_start(array('GC_MobileEmoji', 'handler'));

        // 端末に合わせて画像サイズを変換する。
        ob_start(array('GC_MobileImage', 'handler'));

        // 全角カタカナを半角カタカナに変換する。
        ob_start(create_function('$buffer', 'return mb_convert_kana($buffer, "k", "SJIS-win");'));

        // 内部エンコーディングから Shift JIS に変換する。
        ob_start('mb_output_handler');
    }

    /**
     * モバイルサイト用の初期処理を行う。
     *
     * @return void
     */
    function sfMobileInit() {
        $this->lfMobileInitInput();

        if (basename(dirname($_SERVER['SCRIPT_NAME'])) != 'unsupported') {
            $this->lfMobileCheckCompatibility();
            $this->lfMobileInitSession();
        }

        $this->lfMobileInitOutput();
    }

    /**
     * Location等でセッションIDを付加する必要があるURLにセッションIDを付加する。
     *
     * @return String
     */
    function gfAddSessionId($url = null) {
        $objURL = new Net_URL($url);
        $objURL->addQueryString(session_name(), session_id());
        return $objURL->getURL();
    }

    /**
     * セッション ID を付加した配列を返す.
     *
     * @param array $array 元となる配列
     * @param array セッション ID を追加した配列
     */
    function sessionIdArray($array = array()) {
        return array_merge($array, array(session_name() => session_id());
    }

    /**
     * 空メール用のトークンを生成する。
     *
     * @return string 生成したトークンを返す。
     */
    function lfGenerateKaraMailToken() {
        $token_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        $token_chars_length = strlen($token_chars);
        $token_length = 10;
        $token = '';

        while ($token_length > 0) {
            $token .= $token_chars{mt_rand(0, $token_chars_length - 1)};
            --$token_length;
        }

        return $token;
    }

    /**
     * 空メール管理テーブルに新規エントリーを登録し、トークンを返す。
     *
     * @param string $next_url 空メール受け付け後に遷移させるページ (モバイルサイトトップからの相対URL)
     * @param string $session_id セッションID (省略した場合は現在のセッションID)
     * @return string|false トークンを返す。エラーが発生した場合はfalseを返す。
     */
    function gfPrepareKaraMail($next_url, $session_id = null) {
        if (!isset($session_id)) {
            $session_id = session_id();
        }

        $objQuery = new SC_Query;

        // GC
        $time = date('Y-m-d H:i:s', time() - MOBILE_SESSION_LIFETIME);
        $objQuery->delete('dtb_mobile_kara_mail', 'email IS NULL AND create_date < ?', array($time));

        $objQuery->delete('dtb_mobile_kara_mail', 'session_id = ?', array($session_id));

        $arrValues = array('session_id' => $session_id,
                           'next_url'   => $next_url);

        $try = 10;

        while ($try > 0) {
            $arrValues['token'] = $token = lfGenerateKaraMailToken();

            $objQuery->insert('dtb_mobile_kara_mail', $arrValues);
            $count = $objQuery->count('dtb_mobile_kara_mail', 'token = ?', array($token));

            if ($count == 1) {
                break;
            }

            $objQuery->delete('dtb_mobile_kara_mail', 'session_id = ?', array($session_id));
            $token = false;
            --$try;
        }

        return $token;
    }

    /**
     * 空メールから取得したメールアドレスを空メール管理テーブルに登録する。
     *
     * @param string $token トークン
     * @param string $email メールアドレス
     * @return boolean 成功した場合はtrue、失敗した場合はfalseを返す。
     */
    function gfRegisterKaraMail($token, $email) {
        $objQuery = new SC_Query;

        // GC
        $time = date('Y-m-d H:i:s', time() - MOBILE_SESSION_LIFETIME);
        $objQuery->delete('dtb_mobile_kara_mail',
                          '(email IS NULL AND create_date < ?) OR (email IS NOT NULL AND receive_date < ?)',
                          array($time, $time));

        $kara_mail_id = $objQuery->get('dtb_mobile_kara_mail', 'kara_mail_id', 'token = ?', array($token));
        if (!isset($kara_mail_id)) {
            return false;
        }

        $arrValues = array('email' => $email);
        $arrRawValues = array('receive_date' => 'now()');
        $objQuery->update('dtb_mobile_kara_mail', $arrValues, 'kara_mail_id = ?', array($kara_mail_id), $arrRawValues);

        return true;
    }

    /**
     * 空メール管理テーブルからトークンが一致する行を削除し、
     * 次に遷移させるページのURLを返す。　
     *
     * メールアドレスは $_SESSION['mobile']['kara_mail_from'] に登録される。
     *
     * @param string $token トークン
     * @return string|false URLを返す。エラーが発生した場合はfalseを返す。
     */
    function gfFinishKaraMail($token) {
        $objQuery = new SC_Query;

        $arrRow = $objQuery->getrow('dtb_mobile_kara_mail', 'session_id, next_url, email',
                                    'token = ? AND email IS NOT NULL AND receive_date >= ?',
                                    array($token, date('Y-m-d H:i:s', time() - MOBILE_SESSION_LIFETIME)));
        if (!isset($arrRow)) {
            return false;
        }

        $objQuery->delete('dtb_mobile_kara_mail', 'token = ?', array($token));

        list($session_id, $next_url, $email) = $arrRow;
        $objURL = new Net_URL(MOBILE_SITE_URL . $next_url);
        $objURL->addQueryString(session_name(), $session_id);
        $url = $objURL->getURL();

        session_id($session_id);
        session_start();
        $_SESSION['mobile']['kara_mail_from'] = $email;
        session_write_close();

        return $url;
    }

    /**
     * 外部サイト連携用にセッションIDとパラメーターの組み合わせを保存する。
     *
     * @param string $param_key パラメーター名
     * @param string $param_value パラメーター値
     * @param string $url URL
     * @return void
     */
    function sfMobileSetExtSessionId($param_key, $param_value, $url) {
        $objQuery = new SC_Query;

        // GC
        $time = date('Y-m-d H:i:s', time() - MOBILE_SESSION_LIFETIME);
        $objQuery->delete('dtb_mobile_ext_session_id', 'create_date < ?', array($time));

        $arrValues = array('session_id'  => session_id(),
                           'param_key'   => $param_key,
                           'param_value' => $param_value,
                           'url'         => $url);

        $objQuery->insert('dtb_mobile_ext_session_id', $arrValues);
    }

    /**
     * メールアドレスが携帯のものかどうかを判別する。
     *
     * @param string $address メールアドレス
     * @return boolean 携帯のメールアドレスの場合はtrue、それ以外の場合はfalseを返す。
     */
    function gfIsMobileMailAddress($address) {
        // pdx.ne.jp(ウィルコム)追加
        $arrMobileMailDomains = array('docomo.ne.jp', 'ezweb.ne.jp', 'softbank.ne.jp', 'vodafone.ne.jp', 'pdx.ne.jp');

        if (defined('MOBILE_ADDITIONAL_MAIL_DOMAINS')) {
            $arrMobileMailDomains = array_merge($arrMobileMailDomains, split('[ ,]+', MOBILE_ADDITIONAL_MAIL_DOMAINS));
        }

        foreach ($arrMobileMailDomains as $domain) {
            $domain = str_replace('.', '\\.', $domain);
            if (preg_match("/@([^@]+\\.)?$domain\$/", $address)) {
                return true;
            }
        }

        return false;
    }
}
?>
