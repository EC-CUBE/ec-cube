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

namespace Eccube\Framework\Helper;

use Eccube\Application;
use Eccube\Framework\MobileImage;
use Eccube\Framework\MobileUserAgent;
use Eccube\Framework\SessionFactory;
use Eccube\Framework\Query;
use Eccube\Framework\Util\GcUtils;
use Eccube\Framework\DB\MasterData;

/**
 * モバイルのヘルパークラス.
 *
 * @package Helper
 * @author LOCKON CO.,LTD.
 */
class MobileHelper
{
    /** 基本MimeType */
    public $defaultMimeType = 'application/force-download';

    /** 拡張MimeType配列
     * Application/octet-streamで対応出来ないファイルタイプのみ拡張子をキーに記述する
     * 拡張子が本配列に存在しない場合は application/force-download を利用する */
    public $arrMimetypes = array(
            'html'=> 'text/html',
            'css' => 'text/css',
            'hdml'=> 'text/x-hdml',
            'mmf' => 'application/x-smaf',
            'jpeg'=> 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'png' => 'image/png',
            'bmp' => 'image/x-ms-bmp',
            'amc' => 'application/x-mpeg',
            '3g2' => 'video/3gpp2',
            '3gp' => 'video/3gpp',
            'jam' => 'application/x-jam',
            'kjx' => 'application/x-kjx',
            'jar' => 'application/java-archive',
            'jad' => 'text/vnd.sun.j2me.app-descriptor',
            'swf' => 'application/x-shockwave-flash',
            'dmt' => 'application/x-decomail-template',
            'khm' => 'application/x-kddi-htmlmail',
            'hmt' => 'application/x-htmlmail-template',
            'ucm' => 'application/x-ucf-package',
            'ucp' => 'application/x-ucf-package',
            'pdf' => 'application/pdf',
            'wma' => 'audio/x-ms-wma',
            'asf' => 'video/x-ms-asf',
            'wax' => 'audio/x-ms-wax',
            'wvx' => 'video/x-ms-wvx',
            'wmv' => 'video/x-ms-wmv',
            'asx' => 'video/asx',
            'txt' => 'text/plain',
            'exe' => 'application/octet-stream',
            'zip' => 'application/zip',
            'doc' => 'application/msword',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint'
        );

    /**
     * EC-CUBE がサポートする携帯端末かどうかをチェックする。
     * 非対応端末の場合は /unsupported/ へリダイレクトする。
     *
     * @return void
     */
    public function lfMobileCheckCompatibility()
    {
        if (!MobileUserAgent::isSupported()) {
            header('Location: ' . ROOT_URLPATH . 'unsupported/' . DIR_INDEX_PATH);
            exit;
        }
    }

    /**
     * 入力データを内部エンコーディングに変換し、絵文字を除去する。
     *
     * @param string &$value 入力データへの参照
     * @return void
     */
    public function lfMobileConvertInputValue(&$value)
    {
        if (is_array($value)) {
            foreach ($value as $key => $val) {
                $this->lfMobileConvertInputValue($value[$key]);
            }
        } else {
            // Shift JIS から内部エンコーディングに変換する。
            $value = mb_convert_encoding($value, CHAR_CODE, 'SJIS');
            // SoftBank? 以外の絵文字は外字領域に含まれるため、この段階で除去される。
            // SoftBank? の絵文字を除去する。
            $value = preg_replace('/\\x1b\\$[^\\x0f]*\\x0f/', '', $value);
        }
    }

    /**
     * モバイルサイト用の入力の初期処理を行う。
     *
     * @return void
     */
    public function lfMobileInitInput()
    {
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
    public function lfMobileGetExtSessionId()
    {
        if (!preg_match('|^' . ROOT_URLPATH . '(.*)$|', $_SERVER['SCRIPT_NAME'], $matches)) {
            return null;
        }

        $url = $matches[1];
        $time = date('Y-m-d H:i:s', time() - MOBILE_SESSION_LIFETIME);
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
     * パラメーターから有効なセッションIDを取得する。
     *
     * @return string|false 取得した有効なセッションIDを返す。
     *                      取得できなかった場合は false を返す。
     */
    public function lfMobileGetSessionId()
    {
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

        // セッションIDの存在をチェックする。
        $sessionFactory = SessionFactory::getInstance();
        if ($sessionFactory->sfSessRead($sessionId) === null) {
            GcUtils::gfPrintLog("Non-existent session id : sid=$sessionId");

            return false;
        }

        return session_id($sessionId);
    }

    /**
     * セッションデータが有効かどうかをチェックする。
     *
     * FIXME '@' でエラーを抑制するのは良くない
     *
     * @return boolean セッションデータが有効な場合は true、無効な場合は false を返す。
     */
    public function lfMobileValidateSession()
    {
        // 配列 mobile が登録されているかどうかをチェックする。
        if (!is_array(@$_SESSION['mobile'])) {
            return false;
        }

        // 有効期限を過ぎていないかどうかをチェックする。
        if (intval(@$_SESSION['mobile']['expires']) < time()) {
            $msg = 'Session expired at ' . date('Y/m/d H:i:s', @$_SESSION['mobile']['expires'])
                 . ' : sid=' . session_id();
            GcUtils::gfPrintLog($msg);

            return false;
        }

        // 携帯端末の機種が一致するかどうかをチェックする。
        $model = MobileUserAgent::getModel();
        if (@$_SESSION['mobile']['model'] != $model) {
            $msg = 'User agent model mismatch : '
                 . '"$model" != "' . @$_SESSION['mobile']['model']
                 . '" (expected), sid=' . session_id();
            GcUtils::gfPrintLog($msg);

            return false;
        }

        return true;
    }

    /**
     * モバイルサイト用の出力の初期処理を行う。
     *
     * 出力の流れ
     * 1. ページクラスでの出力
     * 2. 全角カタカナを半角カタカナに変換する。
     * 3. 内部エンコーディングから Shift JIS に変換する。
     * 4. 画像を調整する。
     * 5. 絵文字タグを絵文字コードに変換する。(require.php で設定)
     *
     * @return void
     */
    public function lfMobileInitOutput()
    {
        // 出力用のエンコーディングを Shift JIS に固定する。
        mb_http_output('SJIS-win');

        // 端末に合わせて画像サイズを変換する。
        ob_start(function ($buffer) {
            return MobileImage::handler($buffer);
        });

        // 内部エンコーディングから Shift JIS に変換する。
        ob_start('mb_output_handler');

        //download.phpに対してカタカナ変換をするとファイルが壊れてしまうため回避する
        if ($_SERVER['SCRIPT_FILENAME'] != HTML_REALDIR . 'mypage/download.php') {
            // 全角カタカナを半角カタカナに変換する。
            ob_start(function ($buffer) {
                return mb_convert_kana($buffer, "k");
            });
        }
    }

    /**
     * モバイルサイト用の初期処理を行う。
     *
     * @return void
     */
    public function sfMobileInit()
    {
        $this->lfMobileInitInput();

        if (basename(dirname($_SERVER['SCRIPT_NAME'])) != 'unsupported') {
            $this->lfMobileCheckCompatibility();
        }

        $this->lfMobileInitOutput();
    }

    /**
     * Location等でセッションIDを付加する必要があるURLにセッションIDを付加する。
     *
     * @return String
     */
    public function gfAddSessionId($url = null)
    {
        $objURL = new \Net_URL($url);
        $objURL->addQueryString(session_name(), session_id());

        return $objURL->getURL();
    }

    /**
     * セッション ID を付加した配列を返す.
     *
     * @param array $array 元となる配列
     * @param array セッション ID を追加した配列
     */
    public function sessionIdArray($array = array())
    {
        return array_merge($array, array(session_name() => session_id()));
    }

    /**
     * 空メール用のトークンを生成する。
     *
     * @return string 生成したトークンを返す。
     */
    public function lfGenerateKaraMailToken()
    {
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
     * @param  string       $next_url   空メール受け付け後に遷移させるページ (モバイルサイトトップからの相対URL)
     * @param  string       $session_id セッションID (省略した場合は現在のセッションID)
     * @return string|false トークンを返す。エラーが発生した場合はfalseを返す。
     */
    public function gfPrepareKaraMail($next_url, $session_id = null)
    {
        if (!isset($session_id)) {
            $session_id = session_id();
        }

        $objQuery = Application::alias('eccube.query');

        // GC
        $time = date('Y-m-d H:i:s', time() - MOBILE_SESSION_LIFETIME);
        $objQuery->delete('dtb_mobile_kara_mail', 'email IS NULL AND create_date < ?', array($time));

        $objQuery->delete('dtb_mobile_kara_mail', 'session_id = ?', array($session_id));

        $arrValues = array(
            'session_id' => $session_id,
            'next_url'   => $next_url,
        );

        $try = 10;

        while ($try > 0) {
            $arrValues['token'] = $token = $this->lfGenerateKaraMailToken();

            $arrValues['kara_mail_id'] = $objQuery->nextVal('dtb_mobile_kara_mail_kara_mail_id');
            $objQuery->insert('dtb_mobile_kara_mail', $arrValues);
            $exists = $objQuery->exists('dtb_mobile_kara_mail', 'token = ?', array($token));

            if ($exists) {
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
     * @param  string  $token トークン
     * @param  string  $email メールアドレス
     * @return boolean 成功した場合はtrue、失敗した場合はfalseを返す。
     */
    public function gfRegisterKaraMail($token, $email)
    {
        $objQuery = Application::alias('eccube.query');

        // GC
        $time = date('Y-m-d H:i:s', time() - MOBILE_SESSION_LIFETIME);
        $objQuery->delete('dtb_mobile_kara_mail',
                          '(email IS NULL AND create_date < ?) OR (email IS NOT NULL AND receive_date < ?)',
                          array($time, $time));

        $kara_mail_id = $objQuery->get('kara_mail_id', 'dtb_mobile_kara_mail', 'token = ?', array($token));
        if (!isset($kara_mail_id)) {
            return false;
        }

        $arrValues = array('email' => $email);
        $arrRawValues = array('receive_date' => 'CURRENT_TIMESTAMP');
        $objQuery->update('dtb_mobile_kara_mail', $arrValues, 'kara_mail_id = ?', array($kara_mail_id), $arrRawValues);

        return true;
    }

    /**
     * 空メール管理テーブルからトークンが一致する行を削除し、
     * 次に遷移させるページのURLを返す。　
     *
     * メールアドレスは $_SESSION['mobile']['kara_mail_from'] に登録される。
     *
     * @param  string       $token トークン
     * @return string|false URLを返す。エラーが発生した場合はfalseを返す。
     */
    public function gfFinishKaraMail($token)
    {
        $objQuery = Application::alias('eccube.query');

        $arrRow = $objQuery->getRow(
            'session_id, next_url, email',
            'dtb_mobile_kara_mail',
            'token = ? AND email IS NOT NULL AND receive_date >= ?',
            array($token, date('Y-m-d H:i:s', time() - MOBILE_SESSION_LIFETIME)),
            DB_FETCHMODE_ORDERED
        );

        if (!isset($arrRow)) {
            return false;
        }

        $objQuery->delete('dtb_mobile_kara_mail', 'token = ?', array($token));

        list($session_id, $next_url, $email) = $arrRow;
        $objURL = new \Net_URL(HTTP_URL . $next_url);
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
     * @param  string $param_key   パラメーター名
     * @param  string $param_value パラメーター値
     * @param  string $url         URL
     * @return void
     */
    public function sfMobileSetExtSessionId($param_key, $param_value, $url)
    {
        $objQuery = Application::alias('eccube.query');

        // GC
        $time = date('Y-m-d H:i:s', time() - MOBILE_SESSION_LIFETIME);
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
     * メールアドレスが携帯のものかどうかを判別する。
     *
     * @param  string  $address メールアドレス
     * @return boolean 携帯のメールアドレスの場合はtrue、それ以外の場合はfalseを返す。
     */
    public function gfIsMobileMailAddress($address)
    {
        $masterData = Application::alias('eccube.db.master_data');
        $arrMobileMailDomains = $masterData->getMasterData('mtb_mobile_domain');

        foreach ($arrMobileMailDomains as $domain) {
            $domain = preg_quote($domain, '/');
            if (preg_match("/@([^@]+\\.)?$domain\$/", $address)) {
                return true;
            }
        }

        return false;
    }

    /**
     * ファイルのMIMEタイプを判別する
     *
     * @param  string $filename ファイル名
     * @return string MIMEタイプ
     */
    public function getMimeType($filename)
    {
        //ファイルの拡張子からコンテンツタイプを決定する
        $file_extension = strtolower(substr(strrchr($filename, '.'), 1));
        $mime_type = $this->defaultMimeType;
        if (array_key_exists($file_extension, $this->arrMimetypes)) {
            $mime_type = $this->arrMimetypes[$file_extension];
        }

        return $mime_type;
    }
}
