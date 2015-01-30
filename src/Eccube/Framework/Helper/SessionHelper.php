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
use Eccube\Framework\Session;
use Eccube\Framework\Util\GcUtils;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Util\Utils;

/**
 * セッション関連のヘルパークラス.
 *
 * @package Helper
 * @author LOCKON CO.,LTD.
 */
class SessionHelper
{
    /**
     * トランザクショントークンを生成し, 取得する.
     *
     * 悪意のある不正な画面遷移を防止するため, 予測困難な文字列を生成して返す.
     * 同時に, この文字列をセッションに保存する.
     *
     * この関数を使用するためには, 生成した文字列を次画面へ渡すパラメーターとして
     * 出力する必要がある.
     *
     * 例)
     * <input type='hidden' name='transactionid' value="この関数の返り値" />
     *
     * 遷移先のページで, LC_Page::isValidToken() の返り値をチェックすることにより,
     * 画面遷移の妥当性が確認できる.
     *
     * @access protected
     * @return string トランザクショントークンの文字列
     */
    public static function getToken()
    {
        if (empty($_SESSION[TRANSACTION_ID_NAME])) {
            $_SESSION[TRANSACTION_ID_NAME] = static::createToken();
        }

        return $_SESSION[TRANSACTION_ID_NAME];
    }

    /**
     * トランザクショントークン用の予測困難な文字列を生成して返す.
     *
     * @access private
     * @return string トランザクショントークン用の文字列
     */
    public static function createToken()
    {
        return sha1(uniqid(rand(), true));
    }

    /**
     * トランザクショントークンの妥当性をチェックする.
     *
     * 生成されたトランザクショントークンの妥当性をチェックする.
     * この関数を使用するためには, 前画面のページクラスで LC_Page::getToken()
     * を呼んでおく必要がある.
     *
     * トランザクショントークンは, SessionHelper::getToken() が呼ばれた際に
     * 生成される.
     * 引数 $is_unset が false の場合は, トークンの妥当性検証が不正な場合か,
     * セッションが破棄されるまで, トークンを保持する.
     * 引数 $is_unset が true の場合は, 妥当性検証後に破棄される.
     *
     * @access protected
     * @param boolean $is_unset 妥当性検証後, トークンを unset する場合 true;
     *                          デフォルト値は false
     * @return boolean トランザクショントークンが有効な場合 true
     */
    public function isValidToken($is_unset = false)
    {
        // token の妥当性チェック
        $ret = $_REQUEST[TRANSACTION_ID_NAME] === $_SESSION[TRANSACTION_ID_NAME];

        if ($is_unset || $ret === false) {
            static::destroyToken();
        }

        return $ret;
    }

    /**
     * トランザクショントークンを破棄する.
     *
     * @return void
     */
    public static function destroyToken()
    {
        unset($_SESSION[TRANSACTION_ID_NAME]);
    }

    /**
     * 管理画面の認証を行う.
     *
     * mtb_auth_excludes へ登録されたページは, 認証を除外する.
     *
     * @return void
     */
    public static function adminAuthorization()
    {
        $arrScriptPath = explode('/', str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']));
        if (!GcUtils::isAdminFunction()) {
            return;
        } else {
            $masterData = Application::alias('eccube.db.master_data');
            $arrExcludes = $masterData->getMasterData('mtb_auth_excludes');
            foreach ($arrExcludes as $exclude) {
                $arrExcludesPath = explode('/', str_replace('\\', '/', realpath(HTML_REALDIR . ADMIN_DIR . $exclude)));
                $arrDiff = array_diff_assoc($arrExcludesPath, $arrScriptPath);
                if (count($arrDiff) === 0) {
                    return;
                }
            }
        }
        Utils::sfIsSuccess(new Session());
    }

    /**
     * セッションIDを新しいIDに書き換える
     *
     * @return bool
     */
    public static function regenerateSID()
    {
        return session_regenerate_id(true);
    }
}
