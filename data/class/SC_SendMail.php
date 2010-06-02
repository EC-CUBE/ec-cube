<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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

require_once(dirname(__FILE__) . '/../module/Mail.php');
require_once(dirname(__FILE__) . '/../module/Mail/mime.php');

//--- テキスト/HTML　メール送信
class SC_SendMail {

    var $to;            //  送信先
    var $subject;       //  題名
    var $body;          //  本文
    var $cc;            // CC
    var $bcc;           // BCC
    var $replay_to;     // replay_to
    var $return_path;   // return_path
    var $arrEncode;
    var $objMailMime;
    var $arrTEXTEncode;
    var $arrHTMLEncode;
    var $objMail;

    // コンストラクタ
    function SC_SendMail() {
        $this->arrRecip = array();
        $this->to = "";
        $this->subject = "";
        $this->body = "";
        $this->cc = "";
        $this->bcc = "";
        $this->replay_to = "";
        $this->return_path = "";
        $this->arrEncode = array();
        $this->backend = MAIL_BACKEND;
        $this->host = SMTP_HOST;
        $this->port = SMTP_PORT;
        $this->objMailMime = new Mail_mime();
        mb_language( "Japanese" );

        //-- PEAR::Mailを使ってメール送信オブジェクト作成
        $this->objMail =& Mail::factory($this->backend,
                                        $this->getBackendParams($this->backend));
    }

    // 送信先の設定
    function setRecip($key, $recipient) {
        $this->arrRecip[$key] = $recipient;
    }
    
    // 宛先の設定
    function setTo($to, $to_name = "") {
        if($to != "") {
            $this->to = $this->getNameAddress($to_name, $to);
            $this->setRecip("To", $to);
        }
    }

    // 送信元の設定
    function setFrom($from, $from_name = "") {
        $this->from = $this->getNameAddress($from_name, $from);
    }

    // CCの設定
    function setCc($cc, $cc_name = "") {
        if($cc != "") {
            $this->cc = $this->getNameAddress($cc_name, $cc);
            $this->setRecip("Cc", $cc);
        }
    }

    // BCCの設定
    function setBCc($bcc) {
        if($bcc != "") {
            $this->bcc = $bcc;
            $this->setRecip("Bcc", $bcc);
        }
    }

    // Reply-Toの設定
    function setReplyTo($reply_to) {
        if($reply_to != "") {
            $this->reply_to = $reply_to;
        }
    }

    // Return-Pathの設定
    function setReturnPath($return_path) {
        $this->return_path = $return_path;
    }

    // 件名の設定
    function setSubject($subject) {
        $this->subject = mb_encode_mimeheader($subject, "JIS", 'B', "\n");
        $this->subject = str_replace("\x0D\x0A", "\n", $this->subject);
        $this->subject = str_replace("\x0D", "\n", $this->subject);
        $this->subject = str_replace("\x0A", "\n", $this->subject);
    }

    // 本文の設定
    function setBody($body) {
        $this->body = mb_convert_encoding($body, "JIS", CHAR_CODE);
    }

    // SMTPサーバの設定
    function setHost($host) {
        $this->host = $host;
        $arrHost = array(
                'host' => $this->host,
                'port' => $this->port
        );
        //-- PEAR::Mailを使ってメール送信オブジェクト作成
        $this->objMail =& Mail::factory("smtp", $arrHost);

    }

    // SMTPポートの設定
    function setPort($port) {
        $this->port = $port;
        $arrHost = array(
                'host' => $this->host,
                'port' => $this->port
        );
        //-- PEAR::Mailを使ってメール送信オブジェクト作成
        $this->objMail =& Mail::factory("smtp", $arrHost);
    }

    // 名前<メールアドレス>の形式を生成
    function getNameAddress($name, $mail_address) {
            if($name != "") {
                // 制御文字を変換する。
                $_name = $name;
                $_name = ereg_replace("<","＜", $_name);
                $_name = ereg_replace(">","＞", $_name);
                if(OS_TYPE != 'WIN') {
                    // windowsでは文字化けするので使用しない。
                    // $_name = mb_convert_encoding($_name,"JIS",CHAR_CODE);
                }
                $_name = mb_encode_mimeheader($_name, "JIS", 'B', "\n");
                $name_address = "\"". $_name . "\"<" . $mail_address . ">";
            } else {
                $name_address = $mail_address;
            }
            return $name_address;
    }

    function setItem($to, $subject, $body, $fromaddress, $from_name, $reply_to="", $return_path="", $errors_to="", $bcc="", $cc ="") {
        $this->setBase($to, $subject, $body, $fromaddress, $from_name, $reply_to, $return_path, $errors_to, $bcc, $cc);
    }

    function setItemHtml($to, $subject, $body, $fromaddress, $from_name, $reply_to="", $return_path="", $errors_to="", $bcc="", $cc ="") {
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
    function setBase($to, $subject, $body, $fromaddress, $from_name, $reply_to="", $return_path="", $errors_to="", $bcc="", $cc ="") {
        // 宛先設定
        $this->setTo($to);
        // 件名設定
        $this->setSubject($subject);
        // 本文設定(iso-2022-jpだと特殊文字が？で送信されるのでJISを使用する)
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
        if($errors_to != "") {
            $this->return_path = $errors_to;
        } else if($return_path != "") {
            $this->return_path = $return_path;
        } else {
            $this->return_path = $fromaddress;
        }
    }

    // ヘッダーを返す
    function getBaseHeader() {
        //-- 送信するメールの内容と送信先
        $arrHeader['MIME-Version'] = '1.0';
        $arrHeader['To'] = $this->to;
        $arrHeader['Subject'] = $this->subject;
        $arrHeader['From'] = $this->from;
        $arrHeader['Return-Path'] = $this->return_path;
        if($this->reply_to != "") {
            $arrHeader['Reply-To'] = $this->reply_to;
        }
        if($this->cc != "") {
            $arrHeader['Cc'] = $this->cc;
        }
        if($this->bcc != "") {
            $arrHeader['Bcc'] = $this->bcc;
        }
        $arrHeader['Date'] = date("D, j M Y H:i:s O");
        return $arrHeader;
    }

    // ヘッダーを返す
    function getTEXTHeader() {
        $arrHeader = $this->getBaseHeader();
        $arrHeader['Content-Type'] = "text/plain; charset=\"ISO-2022-JP\"";
        $arrHeader['Content-Transfer-Encoding'] = "7bit";
        return $arrHeader;
    }

    // ヘッダーを返す
    function getHTMLHeader() {
        $arrHeader = $this->getBaseHeader();
        $arrHeader['Content-Type'] = "text/html; charset=\"ISO-2022-JP\"";
        $arrHeader['Content-Transfer-Encoding'] = "ISO-2022-JP";
        return $arrHeader;
    }
    
    // 送信先を返す
    function getRecip() {
        switch ($this->backend) {
        case "mail":
            return $this->to;
            break;
        case "sendmail":
        case "smtp":
        default:
            return $this->arrRecip;
            break;
        }
    }

    /**
     * TXTメール送信を実行する.
     *
     * 設定された情報を利用して, メールを送信する.
     * メールの宛先に Cc や Bcc が設定されていた場合は, 宛先ごとに複数回送信を行う.
     *
     * - getRecip() 関数の返り値が配列の場合は, 返り値のメールアドレスごとに,
     *   PEAR::Mail::send() 関数を実行する.
     * - getRecip() 関数の返り値が string の場合は, 返り値のメールアドレスを
     *   RCPT TO: に設定し, PEAR::Mail::send() 関数を実行する.
     *
     * @return void
     */
    function sendMail() {
        $header = $this->getTEXTHeader();
        $recip = $this->getRecip();

        // メール送信
        if (is_array($recip)) {
            foreach ($recip as $rcpt_to) {
                $results[] = $this->objMail->send($rcpt_to, $header, $this->body);
            }
        } else {
            $results[] = $this->objMail->send($recip, $header, $this->body);
        }

        $ret = true;
        foreach ($results as $result) {
            if (PEAR::isError($result)) {
                GC_Utils_Ex::gfPrintLog($result->getMessage());
                GC_Utils_Ex::gfDebugLog($header);
                $ret = false;
            }
        }
        return $ret;
    }

    // HTMLメール送信を実行する
    function sendHtmlMail() {
        $header = $this->getHTMLHeader();
        // メール送信
        $result = $this->objMail->send($this->getRecip(), $header, $this->body);
        if (PEAR::isError($result)) {
            GC_Utils_Ex::gfPrintLog($result->getMessage());
            GC_Utils_Ex::gfDebugLog($header);
            return false;
        }
        return true;
    }

    /**
     * メーラーバックエンドに応じたパラメータを返す.
     *
     * @param string $backend Pear::Mail のバックエンド
     * @return array メーラーバックエンドに応じたパラメータの配列
     */
    function getBackendParams($backend) {
        switch ($backend) {
        case "mail":
            $arrParams = array();
            break;
        case "sendmail":
            $arrParams = array('sendmail_path' => '/usr/bin/sendmail',
                               'sendmail_args' => '-i'
                               );
            break;
        case "smtp":
        default:
            $arrParams = array(
                               'host' => $this->host,
                               'port' => $this->port
                               );
            break;
        }
        return $arrParams;
    }
}
?>
