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

/**
 * HttpResponse を扱うクラス.
 *
 * @author Ryuichi Tokugami
 * @version $Id$
 */
class SC_Response{

    /**
     * コンテンツタイプ
     * Enter description here ...
     * @var unknown_type
     */
    var $contentType;
    var $body;
    var $statuscode;
    var $header = array();

    var $statusTexts = array(
            '100' => 'Continue',
            '101' => 'Switching Protocols',
            '200' => 'OK',
            '201' => 'Created',
            '202' => 'Accepted',
            '203' => 'Non-Authoritative Information',
            '204' => 'No Content',
            '205' => 'Reset Content',
            '206' => 'Partial Content',
            '300' => 'Multiple Choices',
            '301' => 'Moved Permanently',
            '302' => 'Found',
            '303' => 'See Other',
            '304' => 'Not Modified',
            '305' => 'Use Proxy',
            '306' => '(Unused)',
            '307' => 'Temporary Redirect',
            '400' => 'Bad Request',
            '401' => 'Unauthorized',
            '402' => 'Payment Required',
            '403' => 'Forbidden',
            '404' => 'Not Found',
            '405' => 'Method Not Allowed',
            '406' => 'Not Acceptable',
            '407' => 'Proxy Authentication Required',
            '408' => 'Request Timeout',
            '409' => 'Conflict',
            '410' => 'Gone',
            '411' => 'Length Required',
            '412' => 'Precondition Failed',
            '413' => 'Request Entity Too Large',
            '414' => 'Request-URI Too Long',
            '415' => 'Unsupported Media Type',
            '416' => 'Requested Range Not Satisfiable',
            '417' => 'Expectation Failed',
            '500' => 'Internal Server Error',
            '501' => 'Not Implemented',
            '502' => 'Bad Gateway',
            '503' => 'Service Unavailable',
            '504' => 'Gateway Timeout',
            '505' => 'HTTP Version Not Supported',
        );


    /**
     *
     * Enter description here ...
     */
    var $encoding;

    function SC_Response() {
    }

    /**
     * レスポンス出力を書き込む.
     */
    function write() {
        $this->sendHeader();
        echo $this->body;
    }

    function sendHeader() {
        // HTTPのヘッダ
        //        header('HTTP/1.1 '.$this->statuscode.' '.$this->statusTexts[$this->statuscode]);
        foreach ($this->header as $name => $head){
            header($name.': '.$head);
        }
    }


    function setContentType($contentType) {
        $this->header['Content-Type'] = $contentType;
    }

    function setResposeBody($body){
        $this->body = $body;
    }

    function addHeader($name, $value) {
        $this->header[$name] = $value;
    }

    function containsHeader($name) {
        return isset($this->header[$name]);
    }

    function sendError($errorcode) {
        header('HTTP/1.1 '.$errorcode.' '.$this->statusTexts[$errorcode]);
    }

    function sendRedirect($location) {
        if (preg_match("/(" . preg_quote(SITE_URL, '/')
                          . "|" . preg_quote(SSL_URL, '/') . ")/", $location)) {

            $netURL = new Net_URL($location);
            $arrQueryString = $netURL->querystring;

            if (!empty($_SERVER['QUERY_STRING'])) {
                $netURL->addRawQueryString($_SERVER['QUERY_STRING']);
            }

            foreach ($arrQueryString as $key => $val) {
                $netURL->addQueryString($key, $val);
            }

            $session = SC_SessionFactory::getInstance();
            if (SC_MobileUserAgent::isMobile() || $session->useCookie() == false) {
                $netURL->addQueryString(session_name(), session_id());
            }

            $netURL->addQueryString(TRANSACTION_ID_NAME, SC_Helper_Session_Ex::getToken());
            header("Location: " . $netURL->getURL());
            exit;
        }
        return false;
    }

    function reload($queryString = array(), $removeQueryString = false) {
        // 現在の URL を取得
        $netURL = new Net_URL($_SERVER['REQUEST_URI']);

        if ($removeQueryString) {
            $netURL->querystring = array();
            $_SERVER['QUERY_STRING'] = ''; // sendRedirect() での処理用らしい
        }

        // QueryString を付与
        if (!empty($queryString)) {
            foreach ($queryString as $key => $val) {
                $netURL->addQueryString($key, $val);
            }
        }

        $this->sendRedirect($netURL->getURL());
    }

    function setHeader($headers) {
        $this->header = $headers;
    }

    function setStatus($sc = 202) {
        $this->statuscode = $sc;
    }

}
