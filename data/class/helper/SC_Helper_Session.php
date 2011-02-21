<?php
 /*
  * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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
     function SC_Helper_Session() {
         $this->objDb = new SC_Helper_DB_Ex();
         session_set_save_handler(array(&$this, "sfSessOpen"),
                                  array(&$this, "sfSessClose"),
                                  array(&$this, "sfSessRead"),
                                  array(&$this, "sfSessWrite"),
                                  array(&$this, "sfSessDestroy"),
                                  array(&$this, "sfSessGc"));
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
      * セッションのデータををDBから読み込む.
      *
      * @param string $id セッションID
      * @return string セッションデータの値
      */
     function sfSessRead($id) {
         $objQuery = new SC_Query();
         $arrRet = $objQuery->select("sess_data", "dtb_session", "sess_id = ?", array($id));
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
     function sfSessWrite($id, $sess_data)
     {
         $objQuery = new SC_Query();
         $count = $objQuery->count("dtb_session", "sess_id = ?", array($id));
         $sqlval = array();
         if($count > 0) {
             // レコード更新
             $sqlval['sess_data'] = $sess_data;
             $sqlval['update_date'] = 'Now()';
             $objQuery->update("dtb_session", $sqlval, "sess_id = ?", array($id));
         } else {
             // セッションデータがある場合は、レコード作成
             if(strlen($sess_data) > 0) {
                 $sqlval['sess_id'] = $id;
                 $sqlval['sess_data'] = $sess_data;
                 $sqlval['update_date'] = 'Now()';
                 $sqlval['create_date'] = 'Now()';
                 $objQuery->insert("dtb_session", $sqlval);
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
         $objQuery = new SC_Query();
         $objQuery->delete("dtb_session", "sess_id = ?", array($id));
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
         $objQuery = new SC_Query();
         $where = "update_date < current_timestamp + '-". MAX_LIFETIME . " secs'";
         $objQuery->delete("dtb_session", $where);
         return true;
    }

    /**
     * トランザクショントークンを生成し, 取得する.
     *
     * 悪意のある不正な画面遷移を防止するため, 予測困難な文字列を生成して返す.
     * 同時に, この文字列をセッションに保存する.
     *
     * この関数を使用するためには, 生成した文字列を次画面へ渡すパラメータとして
     * 出力する必要がある.
     *
     * 例)
     * <input type="hidden" name="transactionid" value="この関数の返り値" />
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
     * 前画面で生成されたトランザクショントークンの妥当性をチェックする.
     * この関数を使用するためには, 前画面のページクラスで LC_Page::getToken()
     * を呼んでおく必要がある.
     *
     * @access protected
     * @param boolean $is_unset TODO: nanasessさんが作り変えているらしいですが暫定対応で
     * @return boolean トランザクショントークンが有効な場合 true
     */
    function isValidToken($is_unset = true) {

        $checkToken = "";

        // $_POST の値を優先する
        if (isset($_POST[TRANSACTION_ID_NAME])) {

            $checkToken = $_POST[TRANSACTION_ID_NAME];
        } elseif (isset($_GET[TRANSACTION_ID_NAME])) {

            $checkToken = $_GET[TRANSACTION_ID_NAME];
        }

        $ret = false;
        // token の妥当性チェック
        if ($checkToken === $_SESSION[TRANSACTION_ID_NAME]) {

            $ret = true;
        }

        if ($is_unset) {
            unset($_SESSION[TRANSACTION_ID_NAME]);
        }
        return $ret;
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
        if (preg_match('|^' . ROOT_URLPATH . ADMIN_DIR . '|',
                       $_SERVER['PHP_SELF'])) {
            $is_auth = true;

            foreach ($arrExcludes as $exclude) {
                if (preg_match('|^' . ROOT_URLPATH . ADMIN_DIR . $exclude . '|',
                               $_SERVER['PHP_SELF'])) {
                    $is_auth = false;
                    break;
                }
            }
            if ($is_auth) {
                SC_Utils_Ex::sfIsSuccess(new SC_Session());
            }
        }
    }
}
?>
