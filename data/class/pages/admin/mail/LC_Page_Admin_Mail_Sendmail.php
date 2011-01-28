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

// {{{ requires
require_once(CLASS_REALDIR . "pages/admin/LC_Page_Admin.php");

/**
 * メール配信履歴 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Mail_Sendmail extends LC_Page_Admin {

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
        if (file_exists(MODULE_REALDIR . "mdl_speedmail/SC_SpeedMail.php")) {
            require_once(MODULE_REALDIR . "mdl_speedmail/SC_SpeedMail.php");
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
        $objQuery = new SC_Query();

        $objDb = new SC_Helper_DB_Ex();
        $objSite = $objDb->sfGetBasisData();

        if (MELMAGA_SEND != true) {
            exit;
        }

        $where = 'del_flg = 0';
        $sqlval = array();
        // リアルタイム配信モードがオンのとき
        switch ($this->getMode()) {
        case 'now':
            // 指定データを取得する
            $where .= ' AND send_id = ?';
            $sqlval[] = $_GET['send_id'];
            if ($_GET['retry'] != 'yes') {
                $where .= ' AND complete_count = 0 AND end_date IS NULL';
            }
            break;
        default:
            $where .= ' AND end_date IS NULL';
            $dbFactory = SC_DB_DBFactory::getInstance();
            $where .= $dbFactory->getSendHistoryWhereStartdateSql();
            // 30分毎にCronが送信時間データ確認
            break;
        }

        $objQuery->setOrder('send_id');
        $arrMailList = $objQuery->select('*', 'dtb_send_history', $where, $sqlval);
        $objQuery->setOrder('');

        // 未送信メルマガがあれば送信処理を続ける。なければ中断する。
        if (empty($arrMailList)) {
            echo "not found\n";
            exit;
        }

        echo "start sending\n";

        // メール生成と送信
        foreach ($arrMailList as $arrMail) {
            $sendFlag = null;

            // 送信先リストの取得
            $arrDestinationList = $objQuery->select(
                '*',
                'dtb_send_customer',
                'send_id = ? AND (send_flag = 2 OR send_flag IS NULL)',
                array($arrMail["send_id"])
            );

            foreach ($arrDestinationList as $arrDestination) {

                // 顧客名の変換
                $name = trim($arrDestination["name"]);

                if ($name == "") {
                    $name = "お客";
                }

                $customerName = htmlspecialchars($name);
                $subjectBody = ereg_replace("{name}", $customerName, $arrMail["subject"]);
                $mailBody = ereg_replace("{name}", $customerName, $arrMail["body"]);

                $this->objMail->setItem(
                    $arrDestination["email"],
                    $subjectBody,
                    $mailBody,
                    $objSite->data["email03"],      // 送信元メールアドレス
                    $objSite->data["shop_name"],    // 送信元名
                    $objSite->data["email03"],      // reply_to
                    $objSite->data["email04"],      // return_path
                    $objSite->data["email04"]       // errors_to
                );

                // テキストメール配信の場合
                if ($arrMail["mail_method"] == 2) {
                    $sendResut = $this->objMail->sendMail();
                // HTMLメール配信の場合
                } else {
                    $sendResut = $this->objMail->sendHtmlMail();
                }

                // 送信完了なら1、失敗なら2をメール送信結果フラグとしてDBに挿入
                if (!$sendResut) {
                    $sendFlag = '2';
                } else {
                    $sendFlag = '1';

                    // 完了を 1 増やす
                    $sql = "UPDATE dtb_send_history SET complete_count = complete_count + 1 WHERE send_id = ?";
                    $objQuery->query($sql, array($arrMail["send_id"]));
                }

                // 送信結果フラグ
                $sql ="UPDATE dtb_send_customer SET send_flag = ? WHERE send_id = ? AND customer_id = ?";
                $objQuery->query($sql, array($sendFlag, $arrMail["send_id"], $arrDestination["customer_id"]));
            }

            // メール全件送信完了後の処理
            $completeSql = "UPDATE dtb_send_history SET end_date = now() WHERE send_id = ?";
            $objQuery->query($completeSql, array($arrMail["send_id"]));

            // 送信完了　報告メール
            $compSubject = date("Y年m月d日H時i分") . "  下記メールの配信が完了しました。";
            // 管理者宛に変更
            $this->objMail->setTo($objSite->data["email03"]);
            $this->objMail->setSubject($compSubject);

            // テキストメール配信の場合
            if ($arrMail["mail_method"] == 2 ) {
                $sendResut = $this->objMail->sendMail();
            // HTMLメール配信の場合
            } else {
                $sendResut = $this->objMail->sendHtmlMail();
            }
        }
        //TODO 要リファクタリング(MODE if利用)
        if ($this->getMode() == 'now') {
            SC_Response_Ex::sendRedirectFromUrlPath(ADMIN_DIR . 'mail/history.php');
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
