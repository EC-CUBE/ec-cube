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
 */

/**
 * テンプレートをDOM変形するためのヘルパークラス
 *
 * @package Helper
 * @version $Id$
 */
class SC_Helper_Transform {
    protected $objDOM;
    protected $arrSmartyTagsOrg;
    protected $arrSmartyTagsSub;
    protected $smarty_tags_idx;
    protected $arrErr;
    protected $arrElementTree;
    protected $arrSelectElements;
    protected $html_source;
    protected $header_source;
    protected $footer_source;
    protected $search_depth;

    const ERR_TARGET_ELEMENT_NOT_FOUND = 1;

    /**
     * SmartyのHTMLソースをDOMに変換しておく
     *
     * @param string $source 変形対象のテンプレート
     * @return void
     */
    public function __construct($source) {
        $this->objDOM = new DOMDocument();
        $this->objDOM->strictErrorChecking = false;
        $this->snip_count      = 0;
        $this->smarty_tags_idx = 0;
        $this->arrErr          = array();
        $this->arrElementTree  = array();
        $this->arrSelectElements = array();
        $this->html_source = $source;
        $this->header_source = NULL;
        $this->footer_source = NULL;
        $this->search_depth = 0;

        $encoding = mb_detect_encoding($source);
        if (!in_array($encoding, array('ASCII', 'UTF-8'))) {
            if ($encoding === false) {
                $encoding = '検出不能';
            }
            $msg = 'テンプレートの文字コードが「' . $encoding . '」です。「UTF-8」のみ利用できます。';
            SC_Utils_Ex::sfDispSiteError(FREE_ERROR_MSG, '', true, $msg);
        }

        // Smartyのコメントを削除
        $source = preg_replace(
            '/<\!--{\*.+?\*\}-->/s',
            '',
            $source
        );

        // headタグの内側を退避
        $source = preg_replace_callback(
            '/(<head[^>]*>)(.+)(<\/head>)/is',
            array($this, 'lfCaptureHeadTags2Comment'),
            $source
        );

        // JavaScript内にSmartyのタグが存在するものを、コメント形式に置換
        $source = preg_replace_callback(
            '/<script.+?\/script>/s',
            array($this, 'lfCaptureSmartyTags2Comment'),
            $source
        );

        // HTMLタグ内にSmartyのタグが存在するものを、まず置換する
        $source = preg_replace_callback(
            '/<(?:[^<>]*?(?:(<\!--\{.+?\}-->)|(?R))[^<>]*?)*?>/s',
            array($this, 'lfCaptureSmartyTagsInTag'),
            $source
        );

        // 通常のノードに属する部分を、コメント形式に置換
        $source = preg_replace_callback(
            '/<\!--{.+?\}-->/s',
            array($this, 'lfCaptureSmartyTags2Comment'),
            $source
        );

        // HTMLタグの有無、BODYタグの有無で動作を切り替える
        if (preg_match('/^(.*?)(<html[^>]*>.+<\/html>)(.*?)$/is', $source, $arrMatches)) {
            $this->header_source = $arrMatches[1];
            $source = $arrMatches[2];
            $this->footer_source = $arrMatches[3];
        }
        elseif (preg_match('/^.*?<body[^>]*>.+<\/body>.*$/is', $source)) {
            $source = '<meta http-equiv="content-type" content="text/html; charset=UTF-8" /><html><!--TemplateTransformer start-->'.$source.'<!--TemplateTransformer end--></html>';
        }
        else {
            $source = '<meta http-equiv="content-type" content="text/html; charset=UTF-8" /><html><body><!--TemplateTransformer start-->'.$source.'<!--TemplateTransformer end--></body></html>';
        }

        @$this->objDOM->loadHTML($source);
        $this->lfScanChild($this->objDOM);
    }


    /**
     * jQueryライクなセレクタを用いてエレメントを選択する
     *
     * @param string  $selector      セレクタ
     * @param integer $index         インデックス（指定がある場合）
     * @param boolean $require       エレメントが見つからなかった場合、エラーとするか
     * @param string  $err_msg       エラーメッセージ
     * @return SC_Helper_Transformオブジェクト
     */
    public function select($selector, $index = NULL, $require = true, $err_msg = NULL) {
        $this->arrSelectElements = array();
        $this->search_depth = 0;

        $regex = $this->lfSelector2Regex($selector);    // セレクタをツリー検索用正規表現に変換

        $cur_idx = 0;
        // ツリーを初めから全検索する
        for ($iLoop=0; $iLoop < count($this->arrElementTree); $iLoop++) {
            if (preg_match($regex, $this->arrElementTree[$iLoop][0])) {
                // インデックスが指定されていない(見つけたエレメント全て)、もしくは指定されたインデックスなら選択する
                if (is_null($index) || $cur_idx == $index) {
                    $this->lfAddElement($iLoop, $this->arrElementTree[$iLoop]);
                }
                $cur_idx++;
            }
        }

        // 見つからなかった場合エラーとするならエラーを記録する
        if ($require && $cur_idx == 0) {
            $this->lfSetError(
                $selector,
                self::ERR_TARGET_ELEMENT_NOT_FOUND,
                $err_msg
            );
        }

        return $this;
    }


    /**
     * jQueryライクなセレクタを用いて、選択したエレメント内をさらに絞り込む
     *
     * @param string  $selector      セレクタ
     * @param integer $index         インデックス（指定がある場合）
     * @param boolean $require       エレメントが見つからなかった場合、エラーとするか
     * @param string  $err_msg       エラーメッセージ
     * @return SC_Helper_Transformオブジェクト
     */
    public function find($selector, $index = NULL, $require = true, $err_msg = NULL) {
        $arrParentElements = $this->arrSelectElements[$this->search_depth];
        $this->search_depth++;
        $this->arrSelectElements[$this->search_depth] = array();

        foreach ($arrParentElements as $key => &$objElement) {
            $regex = $this->lfSelector2Regex($selector, $objElement[0]);    // セレクタをツリー検索用正規表現に変換(親要素のセレクタを頭に付ける)

            $cur_idx = 0;
            // 親エレメント位置からツリーを検索する
            for ($iLoop=$objElement[0]; $iLoop < count($this->arrElementTree); $iLoop++) {
                if (preg_match($regex, $this->arrElementTree[$iLoop][0])) {
                    // インデックスが指定されていない(見つけたエレメント全て)、もしくは指定されたインデックスなら選択する
                    if (is_null($index) || $cur_idx == $index) {
                        $this->lfAddElement($iLoop, $this->arrElementTree[$iLoop]);
                    }
                    $cur_idx++;
                }
            }
        }

        // 見つからなかった場合エラーとするならエラーを記録する
        if ($require && count($this->arrSelectElements[$this->search_depth]) == 0) {
            $this->lfSetError(
                $selector,
                self::ERR_TARGET_ELEMENT_NOT_FOUND,
                $err_msg
            );
        }

        return $this;
    }


    /**
     * 選択状態を指定数戻す
     *
     * @param int $back_num 選択状態を戻す数
     * @return SC_Helper_Transformオブジェクト
     */
    public function end($back_num = 1) {
        if ($this->search_depth >= $back_num) {
            $this->search_depth -= $back_num;
        } else {
            $this->search_depth = 0;
        }

        return $this;
    }


    /**
     * 要素の前にHTMLを挿入
     *
     * @param string $html_snip 挿入するHTMLの断片
     * @return SC_Helper_Transformオブジェクト
     */
    public function insertBefore($html_snip) {
        foreach ($this->arrSelectElements[$this->search_depth] as $key => $objElement) {
            $this->lfSetTransform('insertBefore', $objElement[0], $html_snip);
        }
        return $this;
    }


    /**
     * 要素の後にHTMLを挿入
     *
     * @param string $html_snip 挿入するHTMLの断片
     * @return SC_Helper_Transformオブジェクト
     */
    public function insertAfter($html_snip) {
        foreach ($this->arrSelectElements[$this->search_depth] as $key => $objElement) {
            $this->lfSetTransform('insertAfter', $objElement[0], $html_snip);
        }
        return $this;
    }


    /**
     * 要素の先頭にHTMLを挿入
     *
     * @param string $html_snip 挿入するHTMLの断片
     * @return SC_Helper_Transformオブジェクト
     */
    public function appendFirst($html_snip) {
        foreach ($this->arrSelectElements[$this->search_depth] as $key => $objElement) {
            $this->lfSetTransform('appendFirst', $objElement[0], $html_snip);
        }
        return $this;
    }


    /**
     * 要素の末尾にHTMLを挿入
     *
     * @param string $html_snip 挿入するHTMLの断片
     * @return SC_Helper_Transformオブジェクト
     */
    public function appendChild($html_snip) {
        foreach ($this->arrSelectElements[$this->search_depth] as $key => $objElement) {
            $this->lfSetTransform('appendChild', $objElement[0], $html_snip);
        }
        return $this;
    }


    /**
     * 要素を指定したHTMLに置換
     *
     * @param string $html_snip 置換後のHTMLの断片
     * @return SC_Helper_Transformオブジェクト
     */
    public function replaceElement($html_snip) {
        foreach ($this->arrSelectElements[$this->search_depth] as $key => &$objElement) {
            $this->lfSetTransform('replaceElement', $objElement[0], $html_snip);
        }
        return $this;
    }


    /**
     * 要素を削除する
     *
     * @return SC_Helper_Transformオブジェクト
     */
    public function removeElement() {
        foreach ($this->arrSelectElements[$this->search_depth] as $key => &$objElement) {
            $this->lfSetTransform('replaceElement', $objElement[0], '');
        }
        return $this;
    }


    /**
     * HTMLに戻して、Transform用に付けたマーカーを削除し、Smartyのタグを復元する
     *
     * @return string トランスフォーム済みHTML。まったくトランスフォームが行われなかった場合は元のHTMLを返す。。
     */
    public function getHTML() {
        if (count($this->arrErr)) {
            // エラーメッセージ組み立て
            $err_msg = '';
            foreach ($this->arrErr as $arrErr) {
                if ($arrErr['err_msg']) {
                    $err_msg .= '<br />'.$arrErr['err_msg'];
                } else {
                    if ($arrErr['type'] == self::ERR_TARGET_ELEMENT_NOT_FOUND) {
                        $err_msg .= "<br />${arrErr['selector']} が存在しません";
                    } else {
                        $err_msg .= '<br />'.print_r($arrErr, true);
                    }
                }
            }
            // エラー画面表示
            SC_Utils_Ex::sfDispSiteError(FREE_ERROR_MSG, '', true, 'テンプレートの操作に失敗しました。' . $err_msg);
        } elseif ($this->snip_count) {
            $html = $this->objDOM->saveHTML();
            $html = preg_replace('/^.*(<html[^>]*>)/s', '$1', $html);
            $html = preg_replace('/(<\/html>).*$/s', '$1', $html);
            $html = preg_replace('/^.*<\!--TemplateTransformer start-->/s', '', $html);
            $html = preg_replace('/<\!--TemplateTransformer end-->.*$/s', '', $html);
            $html = preg_replace(
                '/<\!--TemplateTransformerSnip start-->.*?<\!--TemplateTransformerSnip end-->/s',
                '',
                $html
            );
            $html = $this->header_source.$html.$this->footer_source;
            $html = str_replace($this->arrSmartyTagsSub, $this->arrSmartyTagsOrg, $html);
            return $html;
        } else {
            return $this->html_source;
        }
    }




    /**
     * DOMの処理の邪魔になるSmartyのタグを代理文字に置換する preg_replace_callback のコールバック関数
     *
     * コメント形式への置換
     *
     * @param array $arrMatches マッチしたタグの情報
     * @return string 代わりの文字列
     */
    protected function lfCaptureSmartyTags2Comment(array $arrMatches) {
        $substitute_tag = sprintf('<!--###%08d###-->', $this->smarty_tags_idx);
        $this->arrSmartyTagsOrg[$this->smarty_tags_idx] = $arrMatches[0];
        $this->arrSmartyTagsSub[$this->smarty_tags_idx] = $substitute_tag;
        $this->smarty_tags_idx++;
        return $substitute_tag;
    }


    /**
     * DOMの処理の邪魔になるSmartyのタグを代理文字に置換する preg_replace_callback のコールバック関数
     *
     * コメント形式への置換
     *
     * @param array $arrMatches マッチしたタグの情報
     * @return string 代わりの文字列
     */
    protected function lfCaptureHeadTags2Comment(array $arrMatches) {
        $substitute_tag = sprintf('<!--###%08d###-->', $this->smarty_tags_idx);
        $this->arrSmartyTagsOrg[$this->smarty_tags_idx] = $arrMatches[2];
        $this->arrSmartyTagsSub[$this->smarty_tags_idx] = $substitute_tag;
        $this->smarty_tags_idx++;

        // 文字化け防止用のMETAを入れておく
        $content_type_tag = '<!--TemplateTransformerSnip start-->';
        $content_type_tag .= '<meta http-equiv="content-type" content="text/html; charset=UTF-8" />'; 
        $content_type_tag .= '<!--TemplateTransformerSnip end-->';

        return $arrMatches[1].$content_type_tag.$substitute_tag.$arrMatches[3];
    }


    /**
     * DOMの処理の邪魔になるSmartyのタグを代理文字に置換する preg_replace_callback のコールバック関数
     *
     * HTMLエレメント内部の処理
     *
     * @param array $arrMatches マッチしたタグの情報
     * @return string 代わりの文字列
     */
    protected function lfCaptureSmartyTagsInTag(array $arrMatches) {
        // Smartyタグ内のクォートを処理しやすいよう、いったんダミーのタグに
        $html = preg_replace_callback('/<\!--{.+?\}-->/s', array($this, 'lfCaptureSmartyTags2Temptag'), $arrMatches[0]);
        $html = preg_replace_callback('/\"[^"]*?\"/s', array($this, 'lfCaptureSmartyTagsInQuote'), $html);
        $html = preg_replace_callback('/###TEMP(\d{8})###/s', array($this, 'lfCaptureSmartyTags2Attr'), $html);
        return $html;
    }


    /**
     * DOMの処理の邪魔になるSmartyのタグを代理文字に置換する preg_replace_callback のコールバック関数
     *
     * ダミーへの置換実行
     *
     * @param array $arrMatches マッチしたタグの情報
     * @return string 代わりの文字列
     */
    protected function lfCaptureSmartyTags2Temptag(array $arrMatches) {
        $substitute_tag = sprintf('###TEMP%08d###', $this->smarty_tags_idx);
        $this->arrSmartyTagsOrg[$this->smarty_tags_idx] = $arrMatches[0];
        $this->arrSmartyTagsSub[$this->smarty_tags_idx] = $substitute_tag;
        $this->smarty_tags_idx++;
        return $substitute_tag;
    }


    /**
     * DOMの処理の邪魔になるSmartyのタグを代理文字に置換する preg_replace_callback のコールバック関数
     *
     * クォート内（＝属性値）内にあるSmartyタグ（ダミーに置換済み）を、テキストに置換
     *
     * @param array $arrMatches マッチしたタグの情報
     * @return string 代わりの文字列
     */
    protected function lfCaptureSmartyTagsInQuote(array $arrMatches) {
        $html = preg_replace_callback(
            '/###TEMP(\d{8})###/s',
            array($this, 'lfCaptureSmartyTags2Value'),
            $arrMatches[0]
        );
        return $html;
    }


    /**
     * DOMの処理の邪魔になるSmartyのタグを代理文字に置換する preg_replace_callback のコールバック関数
     *
     * テキストへの置換実行
     *
     * @param array $arrMatches マッチしたタグの情報
     * @return string 代わりの文字列
     */
    protected function lfCaptureSmartyTags2Value(array $arrMatches) {
        $tag_idx = (int)$arrMatches[1];
        $substitute_tag = sprintf('###%08d###', $tag_idx);
        $this->arrSmartyTagsSub[$tag_idx] = $substitute_tag;
        return $substitute_tag;
    }


    /**
     * DOMの処理の邪魔になるSmartyのタグを代理文字に置換する preg_replace_callback のコールバック関数
     *
     * エレメント内部にあって、属性値ではないものを、ダミーの属性として置換
     *
     * @param array $arrMatches マッチしたタグの情報
     * @return string 代わりの文字列
     */
    protected function lfCaptureSmartyTags2Attr(array $arrMatches) {
        $tag_idx = (int)$arrMatches[1];
        $substitute_tag = sprintf('rel%08d="######"', $tag_idx);
        $this->arrSmartyTagsSub[$tag_idx] = $substitute_tag;
        return ' '.$substitute_tag.' '; // 属性はパース時にスペースが詰まるので、こちらにはスペースを入れておく
    }


    /**
     * DOM Element / Document を走査し、name、class別に分類する
     *
     * @param  DOMNode $objDOMElement DOMNodeオブジェクト
     * @return void
     */
    protected function lfScanChild(DOMNode $objDOMElement, $parent_selector = '') {
        $objNodeList = $objDOMElement->childNodes;
        if (is_null($objNodeList)) return;

        foreach ($objNodeList as $element) {
            // DOMElementのみ取り出す
            if ($element instanceof DOMElement) {
                $arrAttr = array();
                $arrAttr[] = $element->tagName;
                if (method_exists($element, 'getAttribute')) {
                    // idを持っていればidを付加する
                    if ($element->hasAttribute('id'))
                        $arrAttr[] = '#'.$element->getAttribute('id');
                    // classを持っていればclassを付加する(複数の場合は複数付加する)
                    if ($element->hasAttribute('class')) {
                        $arrClasses = preg_split('/\s+/', $element->getAttribute('class'));
                        foreach ($arrClasses as $classname) $arrAttr[] = '.'.$classname;
                    }
                }
                // 親要素のセレクタを付けてツリーへ登録する
                $this_selector = $parent_selector.' '.implode('', $arrAttr);
                $this->arrElementTree[] = array($this_selector, $element);
                // エレメントが子孫要素を持っていればさらに調べる
                if ($element->hasChildNodes()) $this->lfScanChild($element, $this_selector);
            }
        }
    }


    /**
     * セレクタ文字列をツリー検索用の正規表現に変換する
     *
     * @param string $selector      セレクタ
     * @param string $parent_index  セレクタ検索時の親要素の位置（子孫要素検索のため）
     * @return string 正規表現文字列
     */
    protected function lfSelector2Regex($selector, $parent_index = NULL){
        // jQueryライクなセレクタを正規表現に
        $selector = preg_replace('/ *> */', ' >', $selector);   // 子セレクタをツリー検索用に 「A >B」の記法にする
        $regex = '/';
        if (!is_null($parent_index)) $regex .= preg_quote($this->arrElementTree[$parent_index][0], '/');    // (親要素の指定(絞り込み時)があれば頭に付加する(特殊文字はエスケープ)
        $arrSelectors = explode(' ', $selector);
        foreach ($arrSelectors as $sub_selector) {
            if (preg_match('/^(>?)([\w\-]+)?(#[\w\-]+)?(\.[\w\-]+)*$/', $sub_selector, $arrMatch)) {
                // 子セレクタ
                if (isset($arrMatch[1]) && $arrMatch[1]) $regex .= ' ';
                else $regex .= '.* ';
                // タグ名
                if (isset($arrMatch[2]) && $arrMatch[2]) $regex .= preg_quote($arrMatch[2], '/');
                else $regex .= '([\w\-]+)?';
                // id
                if (isset($arrMatch[3]) && $arrMatch[3]) $regex .= preg_quote($arrMatch[3], '/');
                else $regex .= '(#(\w|\-|#{3}[0-9]{8}#{3})+)?';
                // class
                if (isset($arrMatch[4]) && $arrMatch[4]) $regex .= '(\.(\w|\-|#{3}[0-9]{8}#{3})+)*'.preg_quote($arrMatch[4], '/').'(\.(\w|\-|#{3}[0-9]{8}#{3})+)*'; // class指定の時は前後にもclassが付いているかもしれない
                else $regex .= '(\.(\w|\-|#{3}[0-9]{8}#{3})+)*';
            }
        }
        $regex .= '$/i';

        return $regex;
    }


    /**
     * 見つかった要素をプロパティに登録
     *
     * @param integer $elementNo  エレメントのインデックス
     * @param array   $arrElement インデックスとDOMオブジェクトをペアとした配列
     * @return void
     */
    protected function lfAddElement($elementNo, array &$arrElement) {
        if (!array_key_exists($arrElement[0], $this->arrSelectElements[$this->search_depth])) {
            $this->arrSelectElements[$this->search_depth][$arrElement[0]] = array($elementNo, &$arrElement[1]);
        }
    }


    /**
     * DOMを用いた変形を実行する
     *
     * @param string $mode       実行するメソッドの種類
     * @param string $target_key 対象のエレメントの完全なセレクタ
     * @param string $html_snip  HTMLコード
     * @return boolean
     */
    protected function lfSetTransform($mode, $target_key, $html_snip) {

        $substitute_tag = sprintf('<!--###%08d###-->', $this->smarty_tags_idx);
        $this->arrSmartyTagsOrg[$this->smarty_tags_idx] = $html_snip;
        $this->arrSmartyTagsSub[$this->smarty_tags_idx] = $substitute_tag;
        $this->smarty_tags_idx++;

        $this->objDOM->createDocumentFragment();
        $objSnip = $this->objDOM->createDocumentFragment();
        $objSnip->appendXML($substitute_tag);

        $objElement = false;
        if (isset($this->arrElementTree[$target_key]) && $this->arrElementTree[$target_key][0]) {
            $objElement = &$this->arrElementTree[$target_key][1];
        }

        if (!$objElement) return false;

        try {
            switch ($mode) {
                case 'appendFirst':
                    if ($objElement->hasChildNodes()) {
                        $objElement->insertBefore($objSnip, $objElement->firstChild);
                    } else {
                        $objElement->appendChild($objSnip);
                    }
                    break;
                case 'appendChild':
                    $objElement->appendChild($objSnip);
                    break;
                case 'insertBefore':
                    if (!is_object($objElement->parentNode)) return false;
                    $objElement->parentNode->insertBefore($objSnip, $objElement);
                    break;
                case 'insertAfter':
                    if ($objElement->nextSibling) {
                         $objElement->parentNode->insertBefore($objSnip, $objElement->nextSibling);
                    } else {
                         $objElement->parentNode->appendChild($objSnip);
                    }
                    break;
                case 'replaceElement':
                    if (!is_object($objElement->parentNode)) return false;
                    $objElement->parentNode->replaceChild($objSnip, $objElement);
                    break;
                default:
                    break;
            }
            $this->snip_count++;
        }
        catch (Exception $e) {
            SC_Utils_Ex::sfDispSiteError(FREE_ERROR_MSG, '', true, 'テンプレートの操作に失敗しました。');
        }

        return true;
    }


    /**
     * セレクタエラーを記録する
     *
     * @param string  $selector    セレクタ
     * @param integer $type        エラーの種類
     * @param string  $err_msg     エラーメッセージ
     * @return void
     */
    protected function lfSetError($selector, $type, $err_msg = NULL) {
        $this->arrErr[] = array(
            'selector'    => $selector,
            'type'        => $type,
            'err_msg'     => $err_msg
        );
    }
}
