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
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Util\Utils;
use Eccube\Framework\Util\GcUtils;

/* セッション管理クラス */
class Session
{
    /** ログインユーザ名 */
    public $login_id;

    /** ユーザ権限 */
    public $authority;

    /** 認証文字列(認証成功の判定に使用) */
    public $cert;

    /** セッションID */
    public $sid;

    /** ログインユーザの主キー */
    public $member_id;

    /** ページ遷移の正当性チェックに使用 */
    public $uniqid;

    /* コンストラクタ */
    public function __construct()
    {
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
            GcUtils::gfPrintLog('access : user='.$this->login_id.' auth='.$this->authority.' sid='.$this->sid);
        } else {
            // ログに記録する
            GcUtils::gfPrintLog('access error.');
        }
    }
    /* 認証成功の判定 */
    public function IsSuccess()
    {
        if ($this->cert == CERT_STRING) {
            $masterData = Application::alias('eccube.db.master_data');
            $admin_path = strtolower(preg_replace('/\/+/', '/', $_SERVER['SCRIPT_NAME']));            
            $arrPERMISSION = array_change_key_case($masterData->getMasterData('mtb_permission'));
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

    /**
     * @param string $key
     */
    public function SetSession($key, $val)
    {
        $_SESSION[$key] = $val;
    }

    /* セッションの読み込み */

    /**
     * @param string $key
     */
    public function GetSession($key)
    {
        return $_SESSION[$key];
    }

    /* セッションIDの取得 */
    public function GetSID()
    {
        return $this->sid;
    }

    /** ユニークIDの取得 **/
    public function getUniqId()
    {
        // ユニークIDがセットされていない場合はセットする。
        if (empty($_SESSION['uniqid'])) {
            $this->setUniqId();
        }

        return $this->GetSession('uniqid');
    }

    /** ユニークIDのセット **/
    public function setUniqId()
    {
        // 予測されないようにランダム文字列を付与する。
        $this->SetSession('uniqid', Utils::sfGetUniqRandomId());
    }

    // 関連セッションのみ破棄する。
    public function logout()
    {
        unset($_SESSION['cert']);
        unset($_SESSION['login_id']);
        unset($_SESSION['authority']);
        unset($_SESSION['member_id']);
        unset($_SESSION['uniqid']);
        // トランザクショントークンを破棄
        \Eccube\Helper\Session::destroyToken();
        // ログに記録する
        GcUtils::gfPrintLog('logout : user='.$this->login_id.' auth='.$this->authority.' sid='.$this->sid);
    }
}
