<?php
/*
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/**
 * セッション関連のヘルパークラス.
 *
 * @package Helper
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class SC_Helper_Session {

    var $objDb;

    // }}}
    // {{{ constructor

    /**
     * デフォルトコンストラクタ.
     *
     * 各関数をセッションハンドラに保存する
     */
    function __construct() {
        $this->objDb = new SC_Helper_DB_Ex();
        session_set_save_handler(array(&$this, 'sfSessOpen'),
                                 array(&$this, 'sfSessClose'),
                                 array(&$this, 'sfSessRead'),
                                 array(&$this, 'sfSessWrite'),
                                 array(&$this, 'sfSessDestroy'),
                                 array(&$this, 'sfSessGc'));

        // 通常よりも早い段階(オブジェクトが書きされる前)でセッションデータを書き込んでセッションを終了する
        // XXX APC による MDB2 の破棄タイミングによる不具合を回避する目的
        register_shutdown_function('session_write_close');
    }

    // }}}
    // {{{ functions

    /**
     * セッションを開始する.
     *
     * @param string $save_path セッションを保存するパス(使用しない)
     * @param string $session_name セッション名(使用しない)
     * @return bool セッションが正常に開始された場合 true
     */
    function sfSessOpen($save_path, $session_name) {
        return true;
    }

    /**
     * セッションを閉じる.
     *
     * @return bool セッションが正常に終了した場合 true
     */
    function sfSessClose() {
        return true;
    }

    /**
     * セッションのデータをDBから読み込む.
     *
     * @param string $id セッションID
     * @return string セッションデータの値
     */
    function sfSessRead($id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $arrRet = $objQuery->select('sess_data', 'dtb_session', 'sess_id = ?', array($id));
        if (empty($arrRet)) {
            return '';
        } else {
            return $arrRet[0]['sess_data'];
        }
    }

    /**
     * セッションのデータをDBに書き込む.
     *
     * @param string $id セッションID
     * @param string $sess_data セッションデータの値
     * @return bool セッションの書き込みに成功した場合 true
     */
    function sfSessWrite($id, $sess_data) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $exists = $objQuery->exists('dtb_session', 'sess_id = ?', array($id));
        $sqlval = array();
        if ($exists) {
            // レコード更新
            $sqlval['sess_data'] = $sess_data;
            $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
            $objQuery->update('dtb_session', $sqlval, 'sess_id = ?', array($id));
        } else {
            // セッションデータがある場合は、レコード作成
            if (strlen($sess_data) > 0) {
                $sqlval['sess_id'] = $id;
                $sqlval['sess_data'] = $sess_data;
                $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
                $sqlval['create_date'] = 'CURRENT_TIMESTAMP';
                $objQuery->insert('dtb_session', $sqlval);
            }
        }
        return true;
    }

    // セッション破棄

    /**
     * セッションを破棄する.
     *
     * @param string $id セッションID
     * @return bool セッションを正常に破棄した場合 true
     */
    function sfSessDestroy($id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->delete('dtb_session', 'sess_id = ?', array($id));
        return true;
    }

    /**
     * ガーベジコレクションを実行する.
     *
     * 引数 $maxlifetime の代りに 定数 MAX_LIFETIME を使用する.
     *
     * @param integer $maxlifetime セッションの有効期限(使用しない)
     */
    function sfSessGc($maxlifetime) {
        // MAX_LIFETIME以上更新されていないセッションを削除する。
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $where = "update_date < current_timestamp + '-". MAX_LIFETIME . " secs'";
        $objQuery->delete('dtb_session', $where);
        return true;
    }

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
    function getToken() {
        if (empty($_SESSION[TRANSACTION_ID_NAME])) {
            $_SESSION[TRANSACTION_ID_NAME] = SC_Helper_Session_Ex::createToken();
        }
        return $_SESSION[TRANSACTION_ID_NAME];
    }

    /**
     * トランザクショントークン用の予測困難な文字列を生成して返す.
     *
     * @access private
     * @return string トランザクショントークン用の文字列
     */
    function createToken() {
        return sha1(uniqid(rand(), true));
    }

    /**
     * トランザクショントークンの妥当性をチェックする.
     *
     * 生成されたトランザクショントークンの妥当性をチェックする.
     * この関数を使用するためには, 前画面のページクラスで LC_Page::getToken()
     * を呼んでおく必要がある.
     *
     * トランザクショントークンは, SC_Helper_Session::getToken() が呼ばれた際に
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
    function isValidToken($is_unset = false) {
        // token の妥当性チェック
        $ret = $_REQUEST[TRANSACTION_ID_NAME] === $_SESSION[TRANSACTION_ID_NAME];

        if ($is_unset || $ret === false) {
            SC_Helper_Session_Ex::destroyToken();
        }
        return $ret;
    }

    /**
     * トランザクショントークンを破棄する.
     *
     * @return void
     */
    function destroyToken() {
        unset($_SESSION[TRANSACTION_ID_NAME]);
    }

    /**
     * 管理画面の認証を行う.
     *
     * mtb_auth_excludes へ登録されたページは, 認証を除外する.
     *
     * @return void
     */
    function adminAuthorization() {
        $masterData = new SC_DB_MasterData_Ex();
        $arrExcludes = $masterData->getMasterData('mtb_auth_excludes');
        if (preg_match('|^' . ROOT_URLPATH . ADMIN_DIR . '|', $_SERVER['SCRIPT_NAME'])) {
            $is_auth = true;

            foreach ($arrExcludes as $exclude) {
                if (preg_match('|^' . ROOT_URLPATH . ADMIN_DIR . $exclude . '|', $_SERVER['SCRIPT_NAME'])) {
                    $is_auth = false;
                    break;
                }
            }
            if ($is_auth) {
                SC_Utils_Ex::sfIsSuccess(new SC_Session_Ex());
            }
        }
    }
}
