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

namespace Eccube\Framework;

use Eccube\Application;
use Eccube\Framework\Util\Utils;
use Eccube\Framework\Util\GcUtils;

// テキスト/HTML　メール送信
class SendMail
{
    public $to;            // 送信先
    public $subject;       // 題名
    public $body;          // 本文
    public $cc;            // CC
    public $bcc;           // BCC
    public $replay_to;     // replay_to
    public $return_path;   // return_path
    public $objMail;

    /**
     * コンストラクタ
     *
     * @return void
     */
    public function __construct()
    {
        $this->arrRecip = array();
        $this->to = '';
        $this->subject = '';
        $this->body = '';
        $this->cc = '';
        $this->bcc = '';
        $this->replay_to = '';
        $this->return_path = '';
        $this->backend = MAIL_BACKEND;
        $this->host = SMTP_HOST;
        $this->port = SMTP_PORT;

        // \PEAR::Mailを使ってメール送信オブジェクト作成
        $this->objMail = \Mail::factory($this->backend,
                                        $this->getBackendParams($this->backend));
        if (\PEAR::isError($this->objMail)) {
            // XXX 環境によっては文字エンコードに差異がないか些か心配
            trigger_error($this->objMail->getMessage(), E_USER_ERROR);
        }
    }

    // 送信先の設定

    /**
     * @param string $key
     */
    public function setRecip($key, $recipient)
    {
        $this->arrRecip[$key] = $recipient;
    }

    // 宛先の設定
    public function setTo($to, $to_name = '')
    {
        if ($to != '') {
            $this->to = $this->getNameAddress($to_name, $to);
            $this->setRecip('To', $to);
        }
    }

    // 送信元の設定
    public function setFrom($from, $from_name = '')
    {
        $this->from = $this->getNameAddress($from_name, $from);
    }

    // CCの設定

    /**
     * @param string $cc
     */
    public function setCc($cc, $cc_name = '')
    {
        if ($cc != '') {
            $this->cc = $this->getNameAddress($cc_name, $cc);
            $this->setRecip('Cc', $cc);
        }
    }

    // BCCの設定

    /**
     * @param string $bcc
     */
    public function setBCc($bcc)
    {
        if ($bcc != '') {
            $this->bcc = $bcc;
            $this->setRecip('Bcc', $bcc);
        }
    }

    // Reply-Toの設定

    /**
     * @param string $reply_to
     */
    public function setReplyTo($reply_to)
    {
        if ($reply_to != '') {
            $this->reply_to = $reply_to;
        }
    }

    // Return-Pathの設定
    public function setReturnPath($return_path)
    {
        $this->return_path = $return_path;
    }

    // 件名の設定
    public function setSubject($subject)
    {
        $this->subject = mb_encode_mimeheader($subject, 'JIS', 'B', "\n");
        $this->subject = str_replace(array("\r\n", "\r"), "\n", $this->subject);
    }

    // 本文の設定
    public function setBody($body)
    {
        // iso-2022-jpだと特殊文字が？で送信されるのでJISを使用する
        $this->body = mb_convert_encoding($body, 'JIS', CHAR_CODE);
        $this->body = str_replace(array("\r\n", "\r"), "\n", $this->body);
    }

    /**
     * 前方互換用
     *
     * @deprecated 2.12.2 (#1912)
     */
    public function setHost($host)
    {
        trigger_error('前方互換用メソッドが使用されました。', E_USER_WARNING);
        $this->host = $host;
        $arrHost = array(
                'host' => $this->host,
                'port' => $this->port
        );
        // \PEAR::Mailを使ってメール送信オブジェクト作成
        $this->objMail = \Mail::factory('smtp', $arrHost);
    }

    /**
     * 前方互換用
     *
     * @deprecated 2.12.2 (#1912)
     */
    public function setPort($port)
    {
        trigger_error('前方互換用メソッドが使用されました。', E_USER_WARNING);
        $this->port = $port;
        $arrHost = array(
                'host' => $this->host,
                'port' => $this->port
        );
        // \PEAR::Mailを使ってメール送信オブジェクト作成
        $this->objMail = \Mail::factory('smtp', $arrHost);
    }

    // 名前<メールアドレス>の形式を生成

    /**
     * @param string $name
     */
    public function getNameAddress($name, $mail_address)
    {
            if ($name != '') {
                // 制御文字を変換する。
                $_name = $name;
                $_name = mb_encode_mimeheader($_name, 'JIS', 'B', "\n");
                $_name = str_replace('"', '\"', $_name);
                $name_address = sprintf('"%s" <%s>', $_name, $mail_address);
            } else {
                $name_address = $mail_address;
            }

            return $name_address;
    }

    public function setItem($to, $subject, $body, $fromaddress, $from_name, $reply_to='', $return_path='', $errors_to='', $bcc='', $cc ='')
    {
        $this->setBase($to, $subject, $body, $fromaddress, $from_name, $reply_to, $return_path, $errors_to, $bcc, $cc);
    }

    public function setItemHtml($to, $subject, $body, $fromaddress, $from_name, $reply_to='', $return_path='', $errors_to='', $bcc='', $cc ='')
    {
        $this->setBase($to, $subject, $body, $fromaddress, $from_name, $reply_to, $return_path, $errors_to, $bcc, $cc);
    }

    /*  ヘッダ等を格納
         $to            -> 送信先メールアドレス
         $subject       -> メールのタイトル
         $body          -> メール本文
         $fromaddress   -> 送信元のメールアドレス
         $header        -> ヘッダー
         $from_name     -> 送信元の名前（全角OK）
         $reply_to      -> reply_to設定
         $return_path   -> return-pathアドレス設定（エラーメール返送用）
         $cc            -> カーボンコピー
         $bcc           -> ブラインドカーボンコピー
    */
    public function setBase($to, $subject, $body, $fromaddress, $from_name, $reply_to='', $return_path='', $errors_to='', $bcc='', $cc ='')
    {
        // 宛先設定
        $this->setTo($to);
        // 件名設定
        $this->setSubject($subject);
        // 本文設定
        $this->setBody($body);
        // 送信元設定
        $this->setFrom($fromaddress, $from_name);
        // 返信先設定
        $this->setReplyTo($reply_to);
        // CC設定
        $this->setCc($cc);
        // BCC設定
        $this->setBcc($bcc);

        // Errors-Toは、ほとんどのSMTPで無視され、Return-Pathが優先されるためReturn_Pathに設定する。
        if ($errors_to != '') {
            $this->return_path = $errors_to;
        } elseif ($return_path != '') {
            $this->return_path = $return_path;
        } else {
            $this->return_path = $fromaddress;
        }
    }

    // ヘッダーを返す
    public function getBaseHeader()
    {
        // 送信するメールの内容と送信先
        $arrHeader = array();
        $arrHeader['MIME-Version'] = '1.0';
        $arrHeader['To'] = $this->to;
        $arrHeader['Subject'] = $this->subject;
        $arrHeader['From'] = $this->from;
        $arrHeader['Return-Path'] = $this->return_path;
        if ($this->reply_to != '') {
            $arrHeader['Reply-To'] = $this->reply_to;
        }
        if ($this->cc != '') {
            $arrHeader['Cc'] = $this->cc;
        }
        if ($this->bcc != '') {
            $arrHeader['Bcc'] = $this->bcc;
        }
        $arrHeader['Date'] = date('D, j M Y H:i:s O');
        $arrHeader['Content-Transfer-Encoding'] = '7bit';

        return $arrHeader;
    }

    // ヘッダーを返す
    public function getTEXTHeader()
    {
        $arrHeader = $this->getBaseHeader();
        $arrHeader['Content-Type'] = 'text/plain; charset="ISO-2022-JP"';

        return $arrHeader;
    }

    // ヘッダーを返す
    public function getHTMLHeader()
    {
        $arrHeader = $this->getBaseHeader();
        $arrHeader['Content-Type'] = 'text/html; charset="ISO-2022-JP"';

        return $arrHeader;
    }

    /**
     * メーラーバックエンドに応じた送信先を返す
     *
     * @return array|string メーラーバックエンドに応じた送信先
     */
    public function getRecip()
    {
        switch ($this->backend) {
            // \PEAR::Mail_mail#send は、(他のメーラーバックエンドと異なり) 第1引数を To: として扱う。Cc: や Bcc: は、ヘッダー情報から処理する。
            case 'mail':
                return $this->to;

            case 'sendmail':
            case 'smtp':
            default:
                return $this->arrRecip;
        }
    }

    /**
     * TXTメール送信を実行する.
     *
     * 設定された情報を利用して, メールを送信する.
     *
     * @return boolean
     */
    public function sendMail($isHtml = false)
    {
        $header = $isHtml ? $this->getHTMLHeader() : $this->getTEXTHeader();
        $recip = $this->getRecip();
        // メール送信
        $result = $this->objMail->send($recip, $header, $this->body);
        if (\PEAR::isError($result)) {
            // XXX Windows 環境では SJIS でメッセージを受け取るようなので変換する。
            $msg = mb_convert_encoding($result->getMessage(), CHAR_CODE, 'auto');
            $msg = 'メール送信に失敗しました。[' . $msg . ']';
            trigger_error($msg, E_USER_WARNING);
            GcUtils::gfDebugLog($header);

            return false;
        }

        return true;
    }

    /**
     * HTMLメール送信を実行する.
     *
     * @return boolean
     */
    public function sendHtmlMail()
    {
        return $this->sendMail(true);
    }

    /**
     * メーラーバックエンドに応じたパラメーターを返す.
     *
     * @param  string $backend Pear::Mail のバックエンド
     * @return array  メーラーバックエンドに応じたパラメーターの配列
     */
    public function getBackendParams($backend)
    {
        switch ($backend) {
            case 'mail':
                $arrParams = array();
                break;

            case 'sendmail':
                $arrParams = array(
                    'sendmail_path' => '/usr/bin/sendmail',
                    'sendmail_args' => '-i',
                );
                break;

            case 'smtp':
                $arrParams = array(
                    'host' => $this->host,
                    'port' => $this->port,
                );
                if (defined('SMTP_USER')
                    && defined('SMTP_PASSWORD')
                    && !Utils::isBlank(SMTP_USER)
                    && !Utils::isBlank(SMTP_PASSWORD)) {
                    $arrParams['auth'] = true;
                    $arrParams['username'] = SMTP_USER;
                    $arrParams['password'] = SMTP_PASSWORD;
                }
                break;

            default:
                trigger_error('不明なバックエンド。[$backend = ' . var_export($backend, true) . ']', E_USER_ERROR);
                exit;
        }

        return $arrParams;
    }
}
