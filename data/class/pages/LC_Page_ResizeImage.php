<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
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
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';
require_once DATA_REALDIR . 'module/gdthumb.php';

/**
 * リサイズイメージ のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_ResizeImage extends LC_Page_Ex {

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

        $file = NO_IMAGE_REALFILE;

        // NO_IMAGE_REALFILE以外のファイル名が渡された場合、ファイル名のチェックを行う
        if (strlen($_GET['image']) >= 1 && $_GET['image'] !== NO_IMAGE_REALFILE) {

            // ファイル名が正しく、ファイルが存在する場合だけ、$fileを設定
            if (!$this->lfCheckFileName()) {
                GC_Utils_Ex::gfPrintLog('invalid access :resize_image.php $_GET[\'image\']=' . $_GET['image']);
            }
            else if (file_exists(IMAGE_SAVE_REALDIR . $_GET['image'])) {
                $file = IMAGE_SAVE_REALDIR . $_GET['image'];
            }
        }

        $objThumb->Main($file, $_GET['width'], $_GET['height'], "", true);
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
     * ファイル名の形式をチェック.
     *
     * @return boolean 正常な形式:true 不正な形式:false
     */
    function lfCheckFileName() {
        //$pattern = '|^[0-9]+_[0-9a-z]+\.[a-z]{3}$|';
        $pattern = '|\./|';
        $file    = trim($_GET['image']);
        if (preg_match_all($pattern, $file, $matches)) {
            return false;
        } else {
            return true;
        }
    }
}
