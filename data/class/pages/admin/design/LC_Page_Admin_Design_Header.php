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
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * ヘッダ, フッタ編集 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Design_Header extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'design/header.tpl';
        $this->tpl_subnavi  = 'design/subnavi.tpl';
        $this->header_row = 13;
        $this->footer_row = 13;
        $this->tpl_subno = 'header';
        $this->tpl_mainno = 'design';
        $this->tpl_subtitle = 'ヘッダー/フッター設定';
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrDeviceType = $masterData->getMasterData('mtb_device_type');
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
     * FIXME テンプレートの取得方法を要修正
     *
     * @return void
     */
    function action() {
        // 端末種別IDを取得
        if (isset($_REQUEST['device_type_id'])
            && is_numeric($_REQUEST['device_type_id'])) {
            $device_type_id = $_REQUEST['device_type_id'];
        } else {
            $device_type_id = DEVICE_TYPE_PC;
        }
        $this->device_type_id = $device_type_id;

        //サブタイトルの追加
        $this->tpl_subtitle .= ' - ' . $this->arrDeviceType[$device_type_id];

        // テンプレートのパス
        $template_path = $this->lfGetTemplatePath($device_type_id);

        // データ更新処理
        if (isset($_POST['division']) && $_POST['division'] != '') {
            $division = $_POST['division'];
            $content = $_POST[$division]; // TODO no checked?

            switch ($this->getMode()) {
            case 'regist':
                // 正規のテンプレートに書き込む
                $template = $template_path . '/' . $division . '.tpl';
                $this->lfUpdateTemplate($template, $content);
                $this->tpl_onload="alert('登録が完了しました。');";
                break;
            default:
                // なにもしない
                break;
            }
        }

        // テキストエリアに表示
        $this->header_data = file_get_contents($template_path . '/header.tpl');
        $this->footer_data = file_get_contents($template_path . '/footer.tpl');

        // ブラウザタイプ
        $this->browser_type = isset($_POST['browser_type']) ? $_POST['browser_type'] : "";
    }

    function lfUpdateTemplate($template, $content) {
        $fp = fopen($template,'w');
        fwrite($fp, $content);
        fclose($fp);
    }

    function lfGetTemplatePath($device_type_id) {
        $objLayout = new SC_Helper_PageLayout_Ex();
        return $objLayout->getTemplatePath($device_type_id);
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
