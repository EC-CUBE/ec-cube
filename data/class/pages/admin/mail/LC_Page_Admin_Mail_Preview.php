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
 * メルマガプレビュー のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Mail_Preview extends LC_Page_Admin {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'mail/preview.tpl';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        $objQuery = new SC_Query();
        $objSess = new SC_Session();
        $objDate = new SC_Date();

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

        if (!isset($_POST['body'])) $_POST['body'] = "";
        if (!isset($_REQUEST['method'])) $_REQUEST['method'] = "";
        if (!isset($_REQUEST['id'])) $_REQUEST['id'] = "";
        if (!isset($_GET['send_id'])) $_GET['send_id'] = "";

        if ( $_POST['body'] ){
            $this->body = $_POST['body'];

            // HTMLメールテンプレートのプレビュー
        } elseif ($_REQUEST["method"] == "template"
                  && SC_Utils_Ex::sfCheckNumLength($_REQUEST['id'])) {

            $sql = "SELECT * FROM dtb_mailmaga_template WHERE template_id = ?";
            $result = $objQuery->getAll($sql, array($_REQUEST["id"]));
            $this->list_data = $result[0];

            // メイン商品の情報取得
            // FIXME SC_Product クラスを使用した実装
            $sql = "SELECT name, main_image, point_rate, deliv_fee, price01_min, price01_max, price02_min, price02_max FROM vw_products_allclass AS allcls WHERE product_id = ?";
            $main = $objQuery->getAll($sql, array($this->list_data["main_product_id"]));
            $this->list_data["main"] = $main[0];

            // サブ商品の情報取得
            // FIXME SC_Product クラスを使用した実装
            $sql = "SELECT product_id, name, main_list_image, price01_min, price01_max, price02_min, price02_max FROM vw_products_allclass WHERE product_id = ?";
            $k = 0;
            $l = 0;
            for ($i = 1; $i <= 12; $i ++) {
                if ($l == 4) {
                    $l = 0;
                    $k ++;
                }
                $result = "";
                $j = sprintf("%02d", $i);
                if ($i > 0 && $i < 5 ) $k = 0;
                if ($i > 4 && $i < 9 ) $k = 1;
                if ($i > 8 && $i < 13 ) $k = 2;

                if (is_numeric($this->list_data["sub_product_id" .$j])) {
                    $result = $objQuery->getAll($sql, array($this->list_data["sub_product_id" .$j]));
                    $this->list_data["sub"][$k][$l] = $result[0];
                    $this->list_data["sub"][$k]["data_exists"] = "OK";	//当該段にデータが１つ以上存在するフラグ
                }
                $l ++;
            }
            $this->tpl_mainpage = 'mail/html_template.tpl';

        } elseif (SC_Utils_Ex::sfCheckNumLength($_GET['send_id'])
                   || SC_Utils_Ex::sfCheckNumLength($_GET['id'])){
            if (is_numeric($_GET["send_id"])) {
                $id = $_GET["send_id"];
                $sql = "SELECT body, mail_method FROM dtb_send_history WHERE send_id = ?";
            } else {
                $sql = "SELECT body, mail_method FROM dtb_mailmaga_template WHERE template_id = ?";
                $id = $_GET['id'];
            }
            $result = $objQuery->getAll($sql, array($id));

            if ( $result ){
                if ( $result[0]["mail_method"] == 2 ){
                    // テキスト形式の時はタグ文字をエスケープ
                    $this->escape_flag = 1;
                }
                $this->body = $result[0]["body"];
            }
        }
        $this->setTemplate($this->tpl_mainpage);
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
