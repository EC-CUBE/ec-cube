<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
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

require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * リサイズイメージ のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_ResizeImage extends LC_Page_Ex
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init()
    {
        parent::init();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process()
    {
        parent::process();
        $this->action();
    }

    /**
     * Page のAction.
     *
     * @return void
     */
    function action()
    {
        $objFormParam = new SC_FormParam_Ex();
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_GET);
        $arrForm  = $objFormParam->getHashArray();

        $file = NO_IMAGE_REALFILE;

        // NO_IMAGE_REALFILE以外のファイル名が渡された場合、ファイル名のチェックを行う
        if (strlen($arrForm['image']) >= 1
            && $arrForm['image'] !== NO_IMAGE_REALFILE) {
            // ファイル名が正しく、ファイルが存在する場合だけ、$fileを設定
            if (!$this->lfCheckFileName()) {
                GC_Utils_Ex::gfPrintLog('invalid access :resize_image.php image=' . $arrForm['image']);
            } elseif (file_exists(IMAGE_SAVE_REALDIR . $arrForm['image'])) {
                $file = IMAGE_SAVE_REALDIR . $arrForm['image'];
            }
        }

        // リサイズ画像の出力
        $this->lfOutputImage($file, $arrForm['width'], $arrForm['height']);
    }

    function lfInitParam(&$objFormParam)
    {
        $objFormParam->addParam('画像ファイル名', 'image', STEXT_LEN, 'a',  array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('画像の幅', 'width', STEXT_LEN, 'n',  array('NUM_CHECK'));
        $objFormParam->addParam('画像の高さ', 'height', STEXT_LEN, 'n',  array('NUM_CHECK'));
    }

    /**
     * ファイル名の形式をチェック.
     *
     * @return boolean 正常な形式:true 不正な形式:false
     */
    function lfCheckFileName()
    {
        $file    = trim($_GET['image']);
        if (!preg_match("/^[[:alnum:]_\.-]+$/i", $file)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 画像の出力
     *
     * @param string $file 画像ファイル名
     * @param integer $width 画像の幅
     * @param integer $height 画像の高さ
     *
     * @return void
     */
    function lfOutputImage($file, $width, $height)
    {
        $objThumb = new gdthumb();
        $objThumb->Main($file, $width, $height, '', true);
    }
}
