<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * 利用規約について のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Guide_Kiyaku extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
    }

    /**
     * モバイルページを初期化する.
     *
     * @return void
     */
    function mobileInit() {
        $this->tpl_mainpage = 'guide/kiyaku.tpl';	// メインテンプレート
        $this->tpl_title = 'ご利用規約';
    }

    /**
     * Page のプロセス(モバイル).
     *
     * @return void
     */
    function mobileProcess() {
        // レイアウトデザインを取得
        $objLayout = new SC_Helper_PageLayout_Ex();
        $objLayout->sfGetPageLayout($this, false, DEF_LAYOUT);

        // 利用規約を取得する。
        $this->lfGetKiyaku(intval(@$_GET['page']), $this);

        $objView = new SC_MobileView();
        $objView->assignobj($this);
        $objView->display(SITE_FRAME);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /**
     * 利用規約を取得し、ページオブジェクトに格納する。
     *
     * @param integer $index 規約のインデックス
     * @param object &$objPage ページオブジェクト
     * @return void
     */
    function lfGetKiyaku($index, &$objPage) {
        $objQuery = new SC_Query();
        $objQuery->setorder('rank DESC');
        $arrRet = $objQuery->select('kiyaku_title, kiyaku_text', 'dtb_kiyaku', 'del_flg <> 1');

        $number = count($arrRet);
        if ($number > 0) {
            $last = $number - 1;
        } else {
            $last = 0;
        }

        if ($index < 0) {
            $index = 0;
        } elseif ($index > $last) {
            $index = $last;
        }

        $objPage->tpl_kiyaku_title = @$arrRet[$index]['kiyaku_title'];
        $objPage->tpl_kiyaku_text = @$arrRet[$index]['kiyaku_text'];
        $objPage->tpl_kiyaku_index = $index;
        $objPage->tpl_kiyaku_last_index = $last;
        $objPage->tpl_kiyaku_is_first = $index <= 0;
        $objPage->tpl_kiyaku_is_last = $index >= $last;
    }
}
?>
