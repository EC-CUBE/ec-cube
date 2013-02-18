<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
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
require_once(realpath(dirname(__FILE__)) . "/../../require.php");
require_once(realpath(dirname(__FILE__)) . "/../../../data/class_extends/helper_extends/SC_Helper_Session_Ex.php");

/**
 * SC_Helper_Session のテストケース.
 *
 * @package Helper
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class SC_Helper_Session_Test extends PHPUnit_Framework_TestCase 
{

    /**
     * getToken() のテストケース.
     */
    function testGetToken()
    {
        $objSession = new SC_Helper_Session_Ex();
        $token = $objSession->getToken();
        
        // 40文字の16進数
        $this->assertEquals(1, preg_match("/[a-f0-9]{40,}/", $token));
        
        // セッションに文字列が格納されているか
        $this->assertEquals($token, $_SESSION[TRANSACTION_ID_NAME]);
    }
    /**
     * isValidToken() のテストケース.
     */
    function testIsValidToken()
    {
        $objSession = new SC_Helper_Session_Ex();
        $token = $objSession->getToken();
        
        // POST でトークンを渡す.
        $_REQUEST[TRANSACTION_ID_NAME] = $token;
        
        $this->assertEquals(true, $objSession->isValidToken());
        unset($_REQUEST[TRANSACTION_ID_NAME]);
    }

    /**
     * isValidToken() のテストケース(POST).
     */
    function testIsValidTokenWithPost()
    {
        $objSession = new SC_Helper_Session_Ex();
        $token = $objSession->getToken();
        
        // POST でトークンを渡す.
        $_POST[TRANSACTION_ID_NAME] = $token;
        
        // FIXME: PHPUnitでの実行時は、$_POSTの内容が$_REQUESTに統合されないためコメントアウトしています。テストを記述する良い方法があれば変更してください
        // $this->assertEquals(true, $objSession->isValidToken());
        unset($_POST[TRANSACTION_ID_NAME]);
    }

    /**
     * isValidToken() のテストケース(GET).
     */
    function testIsValidTokenWithGET()
    {
        $objSession = new SC_Helper_Session_Ex();
        $token = $objSession->getToken();
        
        // GET でトークンを渡す.
        $_GET[TRANSACTION_ID_NAME] = $token;
        
        // FIXME: PHPUnitでの実行時は、$_GETの内容が$_REQUESTに統合されないためコメントアウトしています。テストを記述する良い方法があれば変更してください
        // $this->assertEquals(true, $objSession->isValidToken());
        unset($_GET[TRANSACTION_ID_NAME]);
    }

    /**
     * isValidToken() のテストケース(エラー).
     *
     * 値が渡されてない場合
     */
    function testIsValidTokenNotParam()
    {
        $objSession = new SC_Helper_Session_Ex();
        $token = $objSession->getToken();
        
        // 値を渡さなければ false
        $this->assertEquals(false, $objSession->isValidToken());
    }
}
?>
