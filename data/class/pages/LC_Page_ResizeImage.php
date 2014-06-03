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
    public function init()
    {
        $this->skip_load_page_layout = true;
        parent::init();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        parent::process();
        $this->action();
    }

    /**
     * Page のAction.
     *
     * @return void
     */
    public function action()
    {
        $objFormParam = new SC_FormParam_Ex();
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_GET);
        $arrErr = $objFormParam->checkError();
        if (SC_Utils_Ex::isBlank($arrErr)) {

            $arrForm  = $objFormParam->getHashArray();

            // TODO: ファイル名を直接指定するような処理は避けるべき
            // NO_IMAGE_REALFILE以外のファイル名が直接渡された場合、ファイル名のチェックを行う
            if (strlen($arrForm['image']) >= 1 && $arrForm['image'] !== NO_IMAGE_REALFILE ) {
                if (!$this->lfCheckFileName($arrForm['image'])) {
                    GC_Utils_Ex::gfPrintLog('invalid access :resize_image.php image=' . $arrForm['image']);
                }
                $file = SC_Utils_Ex::getSaveImagePath($arrForm['image']);
            } else {
                // 商品画像を取得する
                $file = $this->lfGetProductImage($arrForm);
            }

            // リサイズ画像の出力
            $this->lfOutputImage($file, $arrForm['width'], $arrForm['height']);
        }
    }

    public function lfInitParam(&$objFormParam)
    {
        $objFormParam->addParam('商品ID', 'product_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('商品イメージキー', 'image_key', STEXT_LEN, '', array('GRAPH_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('画像ファイル名', 'image', STEXT_LEN, 'a', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('画像の幅', 'width', STEXT_LEN, 'n', array('NUM_CHECK'));
        $objFormParam->addParam('画像の高さ', 'height', STEXT_LEN, 'n', array('NUM_CHECK'));
    }

    /**
     * ファイル名の形式をチェック.
     *
     * @deprecated 2.13.0 商品IDを渡す事を推奨
     * @param $image
     * @return boolean 正常な形式:true 不正な形式:false
     */
    public function lfCheckFileName($image)
    {
        $file    = trim($image);
        if (!preg_match("/^[[:alnum:]_\.-]+$/i", $file)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 商品画像のパスを取得する
     *
     * @param $arrForm
     * @return string 指定された商品画像のパス
     */
    public function lfGetProductImage($arrForm)
    {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $table = 'dtb_products';
        $col = $arrForm['image_key'];
        $product_id = $arrForm['product_id'];
        //指定されたカラムが存在する場合にのみ商品テーブルからファイル名を取得
        if (SC_Helper_DB_Ex::sfColumnExists($table, $col, '', '', false)) {
            $product_image = $objQuery->get($col, $table, 'product_id = ?', array($product_id));
        } else {
            GC_Utils_Ex::gfPrintLog('invalid access :resize_image.php image_key=' . $col);
            $product_image = '';
        }
        // ファイル名が正しく、ファイルが存在する場合だけ、$fileを設定
        $file = SC_Utils_Ex::getSaveImagePath($product_image);

        return $file;
    }

    /**
     * 画像の出力
     *
     * @param string  $file   画像ファイル名
     * @param integer $width  画像の幅
     * @param integer $height 画像の高さ
     *
     * @return void
     */
    public function lfOutputImage($file, $width, $height)
    {
        $objThumb = new gdthumb();
        $objThumb->Main($file, $width, $height, '', true);
    }
}
