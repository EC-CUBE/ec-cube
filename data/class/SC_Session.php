<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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

    /** ログインユーザ名 */
    var $login_id;

    /** ユーザ権限 */
    var $authority;

    /** 認証文字列(認証成功の判定に使用) */
    var $cert;

    /** セッションID */
    var $sid;

    /** ログインユーザの主キー */
    var $member_id;

    /** ページ遷移の正当性チェックに使用 */
    var $uniqid;

    /* コンストラクタ */
    function __construct() {
        // セッション情報の保存
        if (isset($_SESSION['cert'])) {
            $this->sid = session_id();
            $this->cert = $_SESSION['cert'];
            $this->login_id  = $_SESSION['login_id'];
            // 管理者:0, 店舗オーナー:1, 閲覧:2, 販売担当:3 (XXX 現状 0, 1 を暫定実装。2, 3 は未実装。)
            $this->authority = $_SESSION['authority'];
            $this->member_id = $_SESSION['member_id'];
            if (isset($_SESSION['uniq_id'])) {
                $this->uniqid    = $_SESSION['uniq_id'];
            }

            // ログに記録する
            GC_Utils_Ex::gfPrintLog('access : user='.$this->login_id.' auth='.$this->authority.' sid='.$this->sid);
        } else {
            // ログに記録する
            GC_Utils_Ex::gfPrintLog('access error.');
        }
    }
    /* 認証成功の判定 */
    function IsSuccess() {
        if ($this->cert == CERT_STRING) {
            $masterData = new SC_DB_MasterData_Ex();
            $admin_path = preg_replace('/\/+/', '/', $_SERVER['SCRIPT_NAME']);
            $arrPERMISSION = $masterData->getMasterData('mtb_permission');
            if (isset($arrPERMISSION[$admin_path])) {
                // 数値が自分の権限以上のものでないとアクセスできない。
                if ($arrPERMISSION[$admin_path] < $this->authority) {
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
        if (empty($_SESSION['uniqid'])) {
            $this->setUniqId();
        }
        return $this->GetSession('uniqid');
    }

    /** ユニークIDのセット **/
    function setUniqId() {
        // 予測されないようにランダム文字列を付与する。
        $this->SetSession('uniqid', SC_Utils_Ex::sfGetUniqRandomId());
    }

    // 関連セッションのみ破棄する。
    function logout() {
        unset($_SESSION['cert']);
        unset($_SESSION['login_id']);
        unset($_SESSION['authority']);
        unset($_SESSION['member_id']);
        unset($_SESSION['uniqid']);
        // トランザクショントークンを破棄
        SC_Helper_Session_Ex::destroyToken();
        // ログに記録する
        GC_Utils_Ex::gfPrintLog('logout : user='.$this->login_id.' auth='.$this->authority.' sid='.$this->sid);
    }
}
