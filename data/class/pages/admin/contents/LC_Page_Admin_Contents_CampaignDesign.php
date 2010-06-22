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
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * キャンペーンデザイン のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Contents_CampaignDesign extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'contents/campaign_design.tpl';
        $this->tpl_subnavi = 'contents/subnavi.tpl';
        $this->tpl_subno = "campaign";
        $this->tpl_mainno = 'contents';
        $this->header_row = 13;
        $this->contents_row = 13;
        $this->footer_row = 13;
        $this->tpl_subtitle = 'キャンペーンデザイン編集';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objView = new SC_AdminView();
        $objQuery = new SC_Query();

        // 認証可否の判定
        $objSess = new SC_Session();
        SC_Utils_Ex::sfIsSuccess($objSess);

        // キャンペーンデータを引き継ぎ
        if($_POST['mode'] != "") {
            $arrForm = $_POST;
        } else {
            $arrForm = $_GET;
        }

        // 正しく値が取得できない場合はキャンペーンTOPへ
        if($arrForm['campaign_id'] == "" || $arrForm['status'] == "") {
            $this->sendRedirect($this->getLocation(URL_CAMPAIGN_TOP));
            exit;
        }

        switch($arrForm['status']) {
        case 'active':
            $status = CAMPAIGN_TEMPLATE_ACTIVE;
            $this->tpl_campaign_title = "キャンペーン中デザイン編集";
            break;
        case 'end':
            $status = CAMPAIGN_TEMPLATE_END;
            $this->tpl_campaign_title = "キャンペーン終了デザイン編集";
            break;
        default:
            break;
        }

        // ディレクトリ名を取得名
        $directory_name = $objQuery->get("dtb_campaign", "directory_name", "campaign_id = ?", array($arrForm['campaign_id']));
        // キャンペーンテンプレート格納ディレクトリ
        $campaign_dir = CAMPAIGN_TEMPLATE_PATH . $directory_name . "/" .$status;

        switch($_POST['mode']) {
        case 'regist':
            // ファイルを更新
            SC_Utils_Ex::sfWriteFile($arrForm['header'], $campaign_dir."header.tpl", "w");
            SC_Utils_Ex::sfWriteFile($arrForm['contents'], $campaign_dir."contents.tpl", "w");
            SC_Utils_Ex::sfWriteFile($arrForm['footer'], $campaign_dir."footer.tpl", "w");
            // サイトフレーム作成
            $site_frame  = $arrForm['header']."\n";
            $site_frame .= '<script type="text/javascript" src="<!--{$TPL_DIR}-->js/site.js"></script>'."\n";
            $site_frame .= '<script type="text/javascript" src="<!--{$TPL_DIR}-->js/navi.js"></script>'."\n";
            $site_frame .= '<!--{include file=$tpl_mainpage}-->'."\n";
            $site_frame .= $arrForm['footer']."\n";
            SC_Utils_Ex::sfWriteFile($site_frame, $campaign_dir."site_frame.tpl", "w");

            // 完了メッセージ（プレビュー時は表示しない）
            $this->tpl_onload="alert('登録が完了しました。');";
            break;
        case 'preview':
            // プレビューを書き出し別窓で開く
            SC_Utils_Ex::sfWriteFile($arrForm['header'] . $arrForm['contents'] . $arrForm['footer'], $campaign_dir."preview.tpl", "w");
            $this->tpl_onload = "win02('./campaign_preview.php?status=". $arrForm['status'] ."&campaign_id=". $arrForm['campaign_id'] ."', 'preview', '600', '400');";
            $this->header_data = $arrForm['header'];
            $this->contents_data = $arrForm['contents'];
            $this->footer_data = $arrForm['footer'];
            break;
        case 'return':
            // 登録ページへ戻る
            $this->sendRedirect($this->getLocation(URL_CAMPAIGN_TOP));
            exit;
            break;
        default:
            break;
        }

        if ($arrForm['header_row'] != ''){
            $this->header_row = $arrForm['header_row'];
        }
        if ($arrForm['contents_row'] != ''){
            $this->contents_row = $arrForm['contents_row'];
        }
        if ($arrForm['footer_row'] != ''){
            $this->footer_row = $arrForm['footer_row'];
        }

        if($_POST['mode'] != 'preview') {
            // ヘッダーファイルの読み込み
            $this->header_data = file_get_contents($campaign_dir . "header.tpl");
            // コンテンツファイルの読み込み
            $this->contents_data = file_get_contents($campaign_dir . "contents.tpl");
            // フッターファイルの読み込み
            $this->footer_data = file_get_contents($campaign_dir . "footer.tpl");
        }

        // フォームの値を格納
        $this->arrForm = $arrForm;

        // 画面の表示
        $objView->assignobj($this);
        $objView->display(MAIN_FRAME);
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
