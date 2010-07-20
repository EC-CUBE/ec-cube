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
require_once("../html/require.php");
require_once("../data/class/pages/LC_Page.php");
require_once("PHPUnit/Framework.php");

/**
 * LC_Page のテストケース.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_Test.php 15116 2007-07-23 11:32:53Z nanasess $
 */
class LC_Page_Test extends PHPUnit_Framework_TestCase {

    // }}}
    // {{{ functions

    /*
     * FIXME LC_Page::sendRedirect() は, リダイレクトしてしまうため,
     *       PHPUnit3 ではテストできない...
     */

    /**
     * LC_Page::sendRedirect() のテストケース(エラー).
     */
    /*
    function testSendRedirect() {
        $objPage = new LC_Page();
        $result = $objPage->sendRedirect(SITE_URL);

        $this->assertEquals(true, empty($result));
    }
    */
    /**
     * LC_Page::sendRedirect() のテストケース(エラー).
     */
    /*
    function testSendRedirectIsFailed() {
        $objPage = new LC_Page();
        $result = $objPage->sendRedirect("http://www.example.org");

        $this->assertEquals(false, $result);
    }
    */

    /**
     * LC_Page::getToken() のテストケース.
     */
    function testGetToken() {
        $objPage = new LC_Page();

        $token = $objPage->getToken();

        // 40文字の16進数
        $this->assertEquals(1, preg_match("/[a-f0-9]{40,}/", $token));

        // セッションに文字列が格納されているか
        $this->assertEquals($token, $_SESSION[TRANSACTION_ID_NAME]);
    }

    /**
     * LC_Page::isValidToken() のテストケース(POST).
     */
    function testIsValidToken() {
        $objPage = new LC_Page();

        $token = $objPage->getToken();

        // POST でトークンを渡す.
        $_POST[TRANSACTION_ID_NAME] = $token;

        $this->assertEquals(true, $objPage->isValidToken());
        unset($_POST[TRANSACTION_ID_NAME]);
    }

    /**
     * LC_Page::isValidToken() のテストケース(GET).
     */
    function testIsValidTokenWithGET() {
        $objPage = new LC_Page();

        $token = $objPage->getToken();

        // GET でトークンを渡す.
        $_GET[TRANSACTION_ID_NAME] = $token;

        $this->assertEquals(true, $objPage->isValidToken());
        unset($_GET[TRANSACTION_ID_NAME]);
    }


    /**
     * LC_Page::isValidToken() のテストケース(エラー).
     *
     * 値が渡されてない場合
     */
    function testIsValidTokenNotParam() {

        $objPage = new LC_Page();

        $token = $objPage->getToken();

        // 値を渡さなければ falsel
        $this->assertEquals(false, $objPage->isValidToken());
    }

    /**
     * LC_Page::createToken() のテストケース.
     */
    function testCreateToken() {
        // 40文字の16進数
        $this->assertEquals(1, preg_match("/[a-f0-9]{40,}/", LC_Page::createToken()));
    }

    /**
     * LC_Page::getLocation() のテストケース.
     */
    function testGetLocation() {
        $objPage = new LC_Page();
        $_SERVER['DOCUMENT_ROOT'] = realpath("../html");
        $url = $objPage->getLocation("../html/abouts/index.php");

        $this->assertEquals(SITE_URL . "abouts/index.php", $url);
        unset($_SERVER['DOCUMENT_ROOT']);
    }

    /**
     * LC_Page::getLocation() のテストケース.
     *
     * 絶対パス
     */
    function testGetLocationWithFullPath() {
        $objPage = new LC_Page();
        $_SERVER['DOCUMENT_ROOT'] = realpath("../html");
        $url = $objPage->getLocation(URL_DIR . 'abouts/index.php');

        $this->assertEquals(SITE_URL . "abouts/index.php", $url);
        unset($_SERVER['DOCUMENT_ROOT']);
    }

    /**
     * LC_Page::getLocation() のテストケース.
     *
     * QueryString 付与
     */
    function testGetLocationWithQueryString() {
        $objPage = new LC_Page();
        $_SERVER['DOCUMENT_ROOT'] = realpath("../html");

        $queryString = array("mode" => "update", "type" => "text");
        $url = $objPage->getLocation("../html/abouts/index.php", $queryString);

        $this->assertEquals(SITE_URL . "abouts/index.php?mode=update&type=text", $url);
        unset($_SERVER['DOCUMENT_ROOT']);
    }

    /**
     * LC_Page::getLocation() のテストケース.
     *
     * SSL_URL 使用
     */
    function testGetLocationUseSSL() {
        $objPage = new LC_Page();
        $_SERVER['DOCUMENT_ROOT'] = realpath("../html");

        $queryString = array("mode" => "update", "type" => "text");
        $url = $objPage->getLocation("../html/abouts/index.php", $queryString, true);

        $this->assertEquals(SSL_URL . "abouts/index.php?mode=update&type=text", $url);
        unset($_SERVER['DOCUMENT_ROOT']);
    }

    /**
     * LC_Page::getLocation() のテストケース.
     *
     * DocumentRoot 指定
     */
    function testGetLocationWithDocumentRoot() {
        $objPage = new LC_Page();
        $documentRoot = realpath("../html");

        $queryString = array("mode" => "update", "type" => "text");
        $url = $objPage->getLocation("../html/abouts/index.php", array(),
                                     false, $documentRoot);

        $this->assertEquals(SITE_URL . "abouts/index.php", $url);
    }
}
?>
