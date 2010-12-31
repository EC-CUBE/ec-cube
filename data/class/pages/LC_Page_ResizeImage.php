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
require_once(CLASS_FILE_PATH . "pages/LC_Page.php");
require_once(DATA_FILE_PATH . "module/gdthumb.php");

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
        if (strlen($_GET['image']) >= 1 && $_GET['image'] !== NO_IMAGE_DIR) {

            // ファイル名が正しく、ファイルが存在する場合だけ、$fileを設定
            if (!$this->lfCheckFileName()) {
                GC_Utils_Ex::gfPrintLog('invalid access :resize_image.php $_GET["image"]=' . $_GET['image']);
            }
            else if (file_exists(IMAGE_SAVE_FILE_PATH . $_GET['image'])) {
                $file = IMAGE_SAVE_FILE_PATH . $_GET['image'];
            }
        }

        $objThumb->Main($file, $_GET["width"], $_GET["height"], "", true);
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
