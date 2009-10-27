<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * メール配信履歴 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Mail_Sendmail extends LC_Page {
    
	var $objMail;
    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
    	 // SC_SendMailの拡張
	    if(file_exists(MODULE_PATH . "mdl_speedmail/SC_SpeedMail.php")) {
	        require_once(MODULE_PATH . "mdl_speedmail/SC_SpeedMail.php");
	        // SpeedMail対応
	        $this->objMail = new SC_SpeedMail();
	    } else {
	        $this->objMail = new SC_SendMail_Ex();
	    }
    	
        parent::init();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $conn = new SC_DbConn();
        $objSite = new SC_SiteInfo($conn);

        if(MELMAGA_SEND != true) {
            exit;
        }

        //リアルタイム配信モードがオンのとき
        if($_GET['mode'] == 'now') {
            //----　未送信データを取得する
            $time_data = $conn->getAll( "SELECT send_id FROM dtb_send_history  WHERE complete_count = 0 AND del_flg = 0 AND end_date IS NULL ORDER BY send_id ASC, start_date ASC" );
        } else {
            // postgresql と mysql とでSQLをわける
            if (DB_TYPE == "pgsql") {
                $sql = "SELECT send_id FROM dtb_send_history  ";
                $sql.= "WHERE start_date  BETWEEN current_timestamp + '- 5 minutes' AND current_timestamp + '5 minutes' AND del_flg = 0  AND end_date IS NULL ORDER BY send_id ASC, start_date ASC";
            }else if (DB_TYPE == "mysql") {
                $sql = "SELECT send_id FROM dtb_send_history  ";
                $sql.= "WHERE start_date  BETWEEN date_add(now(),INTERVAL -5 minute) AND date_add(now(),INTERVAL 5 minute) AND del_flg = 0  AND end_date IS NULL ORDER BY send_id ASC, start_date ASC";
            }
            //----　30分毎にCronが送信時間データ確認
            $time_data = $conn->getAll($sql);
        }

        //未送信メルマガの数
        $count = count($time_data);

        //未送信メルマガがあれば送信処理を続ける。なければ中断する。
        if( $count > 0 ){
            print("start sending <br />\n");
        } else {
            print("not found <br />\n");
            exit;
        }

        //---- メール送信準備
        for( $i = 0; $i < $count; $i++ ) {
            // 送信先リストの取得
            $sql = "SELECT * FROM dtb_send_customer WHERE send_id = ? AND (send_flag = 2 OR send_flag IS NULL)";
            $list_data[] = $conn->getAll( $sql, array( $time_data[$i]["send_id"] ) );
            // 送信先データの取得
            $sql = "SELECT * FROM dtb_send_history WHERE send_id = ?";
            $mail_data[] = $conn->getAll( $sql, array( $time_data[$i]["send_id"] ) );
        }

        //---- 送信結果フラグ用SQL
        $sql_flag ="UPDATE dtb_send_customer SET send_flag = ? WHERE send_id = ? AND customer_id = ?";

        //----　メール生成と送信
        for( $i = 0; $i < $count; $i++ ) {
            for( $j = 0; $j < count( $list_data[$i] ); $j ++ ) {
                $customerName = "";
                $mailBody = "";
                $sendFlag = "";

                //-- 顧客名の変換
                $name = trim($list_data[$i][$j]["name"]);

                if ($name == "") {
                    $name = "お客";
                }

                $customerName = htmlspecialchars($name);
                $subjectBody = ereg_replace( "{name}", $customerName , $mail_data[$i][0]["subject"] );
                $mailBody = ereg_replace( "{name}", $customerName ,  $mail_data[$i][0]["body"] );

                $this->objMail->setItem(
                                                $list_data[$i][$j]["email"]
                                               ,$subjectBody
                                               ,$mailBody
                                               ,$objSite->data["email03"]                  //　送信元メールアドレス
                                               ,$objSite->data["company_name"]             //　送信元名
                                               ,$objSite->data["email03"]                  //　reply_to
                                               ,$objSite->data["email04"]                  //　return_path
                                               ,$objSite->data["email04"]                  //　errors_to
                                    );

                //-- テキストメール配信の場合
                if( $mail_data[$i][0]["mail_method"] == 2 ) {
                    $sendResut = $this->objMail->sendMail();
                //--  HTMLメール配信の場合
                } else {
                    $sendResut = $this->objMail->sendHtmlMail();
                }

                //-- 送信完了なら1、失敗なら-1をメール送信結果フラグとしてDBに挿入
                if( ! $sendResut ){
                    $sendFlag = "-1";
                } else {
                    $sendFlag = "1";

                    // 完了を 1 増やす
                    $sql = "UPDATE dtb_send_history SET complete_count = complete_count + 1 WHERE send_id = ?";
                    $conn->query( $sql, array($mail_data[$i][0]["send_id"]));
                }
                $conn->query( $sql_flag, array( $sendFlag, $mail_data[$i][0]["send_id"], $list_data[$i][$j]["customer_id"] ) );
            }

            //--- メール全件送信完了後の処理
            $completeSql = "UPDATE dtb_send_history SET end_date = now() WHERE send_id = ?";
            $conn->query( $completeSql, array( $time_data[$i]["send_id"] ) );

            //---　送信完了　報告メール
            $compSubject =  date("Y年m月d日H時i分" . "  下記メールの配信が完了しました。" );
            // 管理者宛に変更
            $this->objMail->setTo($objSite->data["email03"]);
            $this->objMail->setSubject($compSubject);

            //-- テキストメール配信の場合
            if( $mail_data[$i][0]["mail_method"] == 2 ) {
                $sendResut = $this->objMail->sendMail();
            //--  HTMLメール配信の場合
            } else {
                $sendResut = $this->objMail->sendHtmlMail();
            }
        }
        if ($_GET['mode'] = "now") {
            header("Location: " . URL_DIR . "admin/mail/history.php");
        }
        echo "complete\n";
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }
}
?>
