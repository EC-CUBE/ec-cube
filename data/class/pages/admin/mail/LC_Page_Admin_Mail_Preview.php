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

        if (SC_Utils_Ex::sfIsInt($_GET['send_id'])
                   || SC_Utils_Ex::sfIsInt($_GET['id'])){
                       
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
