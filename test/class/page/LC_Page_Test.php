<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");
require_once("PHPUnit/TestCase.php");

/**
 * LC_Page のテストケース.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Test extends PHPUnit_TestCase {

    // }}}
    // {{{ functions

    /**
     * LC_Page::sendRedirect() のテストケース(エラー).
     */
    function testSendRedirect() {
        $objPage = new LC_Page();
        $result = $objPage->sendRedirect(SITE_URL);

        $this->assertEquals(true, empty($result));
    }

    /**
     * LC_Page::sendRedirect() のテストケース(エラー).
     */
    function testSendRedirectIsFailed() {
        $objPage = new LC_Page();
        $result = $objPage->sendRedirect("http://www.example.org");

        $this->assertEquals(false, $result);
    }

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
}
?>
