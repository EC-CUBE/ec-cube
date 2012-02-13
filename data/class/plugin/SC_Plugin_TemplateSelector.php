<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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
 *
 */

/**
* SmartyテンプレートをDOMを使用して変形させる際に使用するセレクタクラス
*
* @package Plugin
* @author LOCKON CO.,LTD.
* @version $Id: $
*/
class SC_Plugin_TemplateSelector {

    var $objTransformer;
    var $arrElements;
    var $current_plugin;

    /**
     * コンストラクタ
     *
     * @param SC_Plugin_TemplateTransformList $objTransformer SC_Plugin_TemplateTransformListオブジェクト
     * @param string $current_plugin プラグイン名
     * @return void
     */
    function __construct(SC_Plugin_TemplateTransformList $objTransformer, $current_plugin) {
        $this->objTransformer = $objTransformer;
        $this->current_plugin = $current_plugin;
        $this->arrElements = array();
    }


    /**
     * 見つかった要素をプロパティに登録
     *
     * @param integer $elementNo  エレメントのインデックス
     * @param array   $arrElement インデックスとDOMオブジェクトをペアとした配列
     * @return void
     */
    function addElement($elementNo, array &$arrElement) {
        if (!array_key_exists($arrElement[0], $this->arrElements)) {
            $this->arrElements[$arrElement[0]] = array($elementNo, &$arrElement[1]);

        }
    }

    /**
     * jQueryライクなセレクタを用いてエレメントを検索する
     *
     * @param string  $selector      セレクタ
     * @param string  $index         インデックス（指定がある場合）
     * @param boolean $require       エレメントが見つからなかった場合、エラーとするか
     * @param string  $err_msg       エラーメッセージ
     * @param SC_Plugin_TemplateSelector $objSelector セレクタオブジェクト
     * @param string  $parent_index  セレクタ検索時の親要素の位置（子孫要素検索のため）
     * @return SC_Plugin_TemplateSelector SC_Plugin_TemplateSelectorオブジェクト
     */
    function find($selector, $index = NULL, $require = true, $err_msg = NULL, SC_Plugin_TemplateSelector $objSelector = NULL, $parent_index = NULL) {
        $objSelectorChild = new SC_Plugin_TemplateSelector($this->objTransformer, $this->current_plugin);
        foreach ($this->arrElements as $key => &$objElement) {
            $this->objTransformer->find($selector, $index, false, NULL, $objSelectorChild, $objElement[0]);
        }
        if ($require && $objSelectorChild->getFoundCount() == 0) {
            $this->objTransformer->setError(
                $this->current_plugin,
                $selector,
                SC_Plugin_TemplateTransformList::ERR_TARGET_ELEMENT_NOT_FOUND,
                $err_msg
            );
        }
        return $objSelectorChild;
    }


    /**
     * 要素の前にHTMLを挿入
     *
     * @param string $html_snip 挿入するHTMLの断片
     * @return SC_Plugin_TemplateSelector SC_Plugin_TemplateSelectorオブジェクト
     */
    function insertBefore($html_snip) {
        foreach ($this->arrElements as $key => $objElement) {
            $this->objTransformer->setTransform('insertBefore', $objElement[0], $html_snip);
        }
        return $this;
    }
    

    /**
     * 要素の後にHTMLを挿入
     *
     * @param string $html_snip 挿入するHTMLの断片
     * @return Plugin_Template_Selector Plugin_Template_Selectorオブジェクト
     */
    function insertAfter($html_snip) {
        foreach ($this->arrElements as $key => $objElement) {
            $this->objTransformer->setTransform('insertAfter', $objElement[0], $html_snip);
        }
        return $this;
    }

    /**
     * 要素の末尾にHTMLを挿入
     *
     * @param string $html_snip 挿入するHTMLの断片
     * @return SC_Plugin_TemplateSelector SC_Plugin_TemplateSelectorオブジェクト
     */
    function appendChild($html_snip) {
        foreach ($this->arrElements as $key => $objElement) {
            $this->objTransformer->setTransform('appendChild', $objElement[0], $html_snip);
        }
        return $this;
    }
    
    
    /**
     * 要素を指定したHTMLに置換
     *
     * @param string $html_snip 置換後のHTMLの断片
     * @return SC_Plugin_TemplateSelector SC_Plugin_TemplateSelectorオブジェクト
     */
    function replaceChild($html_snip) {
        foreach ($this->arrElements as $key => &$objElement) {
            $this->objTransformer->setTransform('replaceChild', $objElement[0], $html_snip);
        }
        return $this;
    }
    
    
    /**
     * findで見つかったエレメントの数を返す
     *
     * @return integer findで見つかったエレメントの数
     */
    function getFoundCount() {
        return count($this->arrElements);
    }
    


}

?>
