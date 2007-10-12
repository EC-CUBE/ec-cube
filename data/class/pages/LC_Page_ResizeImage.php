<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");
require_once(DATA_PATH . "module/gdthumb.php");

/**
 * リサイズイメージ のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_ResizeImage extends LC_Page {

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
        $objThumb = new gdthumb();

        $file = NO_IMAGE_DIR;

        // NO_IMAGE_DIR以外のファイル名が渡された場合、ファイル名のチェックを行う
        if ( isset($_GET['image']) && $_GET['image'] !== NO_IMAGE_DIR) {

            // ファイル名が正しい場合だけ、$fileを設定
            if ( $this->lfCheckFileName() === true ) {
                $file = IMAGE_SAVE_DIR . $_GET['image'];
            } else {
                GC_Utils_Ex::gfPrintLog('invalid access :resize_image.php $_GET["image"]=' . $_GET['image']);
            }
        }

        if(file_exists($file)){
            $objThumb->Main($file, $_GET["width"], $_GET["height"], "", true);
        }else{
            $objThumb->Main(NO_IMAGE_DIR, $_GET["width"], $_GET["height"], "", true);
        }
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    // ファイル名の形式をチェック
    function lfCheckFileName() {
        //$pattern = '|^[0-9]+_[0-9a-z]+\.[a-z]{3}$|';
        $pattern = '|\./|';
        $file    = trim($_GET["image"]);
        if ( preg_match_all($pattern, $file, $matches) ) {
            return false;
        } else {
            return true;
        }
    }
}
?>
