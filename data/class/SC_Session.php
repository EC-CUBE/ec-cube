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

/* セッション管理クラス */
class SC_Session {
    var $login_id;		// ログインユーザ名
    var $authority;		// ユーザ権限
    var $cert;			// 認証文字列(認証成功の判定に使用)
    var $sid;			// セッションID
    var $member_id;		// ログインユーザの主キー
    var $uniqid;         // ページ遷移の正当性チェックに使用

    /* コンストラクタ */
    function SC_Session() {
        // セッション開始
        SC_Utils_Ex::sfDomainSessionStart();

        // セッション情報の保存
        if(isset($_SESSION['cert'])) {
            $this->sid = session_id();
            $this->cert = $_SESSION['cert'];
            $this->login_id  = $_SESSION['login_id'];
            $this->authority = $_SESSION['authority'];	// 管理者:0, 一般:1, 閲覧:2
            $this->member_id = $_SESSION['member_id'];
            if (isset($_SESSION['uniq_id'])) {
                $this->uniqid    = $_SESSION['uniq_id'];
            }

            // ログに記録する
            GC_Utils_Ex::gfPrintLog("access : user=".$this->login_id." auth=".$this->authority." sid=".$this->sid);
        } else {
            // ログに記録する
            GC_Utils_Ex::gfPrintLog("access error.");
        }
    }
    /* 認証成功の判定 */
    function IsSuccess() {
        global $arrPERMISSION;
        if($this->cert == CERT_STRING) {
            if(isset($arrPERMISSION[$_SERVER['PHP_SELF']])) {
                // 数値が自分の権限以上のものでないとアクセスできない。
                if($arrPERMISSION[$_SERVER['PHP_SELF']] < $this->authority) {
                    return AUTH_ERROR;
                }
            }
            return SUCCESS;
        }

        return ACCESS_ERROR;
    }

    /* セッションの書き込み */
    function SetSession($key, $val) {
        $_SESSION[$key] = $val;
    }

    /* セッションの読み込み */
    function GetSession($key) {
        return $_SESSION[$key];
    }

    /* セッションIDの取得 */
    function GetSID() {
        return $this->sid;
    }

    /** ユニークIDの取得 **/
    function getUniqId() {
        // ユニークIDがセットされていない場合はセットする。
        if( empty($_SESSION['uniqid']) ) {
            $this->setUniqId();
        }
        return $this->GetSession('uniqid');
    }

    /** ユニークIDのセット **/
    function setUniqId() {
        // 予測されないようにランダム文字列を付与する。
        $this->SetSession('uniqid', SC_Utils_Ex::sfGetUniqRandomId());
    }

    /* セッションの破棄 */
    function EndSession() {
        // デフォルトは、「PHPSESSID」
        $sname = session_name();
        // セッション変数を全て解除する
        $_SESSION = array();
        // セッションを切断するにはセッションクッキーも削除する。
        // Note: セッション情報だけでなくセッションを破壊する。
        if (isset($_COOKIE[$sname])) {
            setcookie($sname, '', time()-42000, '/');
        }
        // 最終的に、セッションを破壊する
        session_destroy();
        // ログに記録する
        GC_Utils_Ex::gfPrintLog("logout : user=".$this->login_id." auth=".$this->authority." sid=".$this->sid);
    }

    // 関連セッションのみ破棄する。
    function logout() {
        unset($_SESSION['cert']);
        unset($_SESSION['login_id']);
        unset($_SESSION['authority']);
        unset($_SESSION['member_id']);
        unset($_SESSION['uniqid']);
        // ログに記録する
        GC_Utils_Ex::gfPrintLog("logout : user=".$this->login_id." auth=".$this->authority." sid=".$this->sid);
    }
}
?>
