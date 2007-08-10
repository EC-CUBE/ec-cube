<?php
 /*
  * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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

     // }}}
     // {{{ constructor

     /**
      * デフォルトコンストラクタ.
      *
      * 各関数をセッションハンドラに保存する
      */
     function SC_Heler_Session() {
         $objDb = new SC_Helper_DB_Ex();
         if($objDb->sfTabaleExists("dtb_session")) {
             session_set_save_handler(array(&$this, "sfSessOpen"),
                                      array(&$this, "sfSessClose"),
                                      array(&$this, "sfSessRead"),
                                      array(&$this, "sfSessWrite"),
                                      array(&$this, "sfSessDestroy"),
                                      array(&$this, "sfSessGc"));
         }
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
             return null;
         } else {
             return($arrRet[0]['sess_data']);
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
}
?>
