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
 * テンプレートトランスフォーマークラス
 *
 * @package Plugin
 * @author LOCKON CO.,LTD.
 * @version $Id: $
 */
class SC_Plugin_Template_Transformer {

    var $objDOM;
    var $arrDomHash;
    var $current_plugin;
    var $arrSmartyTagsOrg;
    var $arrSmartyTagsSub;
    var $smarty_tags_idx;
    var $arrErr;
    var $arrElementTree;

    const ERR_TARGET_ELEMENT_NOT_FOUND = 1;


    /**
     * コンストラクタ
     *
     * @param string $tmpl 変形前テンプレートファイルのフルパス
     * @return void
     */
    function SC_Plugin_Template_Transformer($tmpl) {
        $this->objDOM = new DOMDocument();
        $this->objDOM->strictErrorChecking = false;
        $this->snip_count = 0;
        $this->smarty_tags_idx = 0;
        $this->arrErr = array();
        $this->arrElementTree = array();

        // ファイルの内容を全て文字列に読み込む
        $html = file_get_contents(SMARTY_TEMPLATES_REALDIR . $tmpl);
        $err_msg = null;

        // 対象のパスが存在するかを検証する,
        if ($html === false) {
            $err_msg = SMARTY_TEMPLATES_REALDIR . $tmpl. "は存在しないか、読み取れません";
        } elseif (!in_array(mb_detect_encoding($html), array('ASCII', 'UTF-8'))) {
            $err_msg = $tmpl. "の文字コードがUTF-8ではありません";
        }

        if (!is_null($err_msg)) {
            // TODO エラー処理
        }

        // JavaScript内にSmartyのタグが存在するものを、コメント形式に置換
        $html = preg_replace_callback(
            '/<script.+?\/script>/s',
            array($this, 'captureSmartyTags2Comment'),
            $html
        );

        // HTMLタグ内にSmartyのタグが存在するものを、いったんダミーのタグに置換する
        $html = preg_replace_callback(
            '/<(?:[^<>]*?(?:(<\!--\{.+?\}-->)|(?R))[^<>]*?)*?>/s',
            array($this, 'captureSmartyTagsInTag'),
            $html
        );

        // 通常のノードに属する部分を、コメント形式に置換
        $html = preg_replace_callback(
            '/<\!--{.+?\}-->/s',
            array($this, 'captureSmartyTags2Comment'),
            $html
        );

        $html = '<meta http-equiv="content-type" content="text/html; charset=UTF-8" /><html><body><!--TemplateTransformer start-->'.$html.'<!--TemplateTransformer end--></body></html>';
        // TODO エラー処理
        @$this->objDOM->loadHTML($html);
        $this->arrDomHash = array('name' => array());

        $this->scanChild($this->objDOM);

    }

    /**
     * これから処理を行おうとするプラグインの名前をセットする
     *
     * @param string $plugin_name  プラグイン名
     * @return void
     */
    function setCurrentPlugin($plugin_name) {
        $this->current_plugin = $plugin_name;
    }

    /**
     * DOMの処理の邪魔になるSmartyのタグを代理文字に置換する preg_replace_callback のコールバック関数
     *
     * コメント形式への置換
     *
     * @param array $arrMatches マッチしたタグの情報
     * @return string 代わりの文字列
     */
    function captureSmartyTags2Comment(array $arrMatches) {
        $substitute_tag = sprintf('<!--###%08d###-->', $this->smarty_tags_idx);
        $this->arrSmartyTagsOrg[$this->smarty_tags_idx] = $arrMatches[0];
        $this->arrSmartyTagsSub[$this->smarty_tags_idx] = $substitute_tag;
        $this->smarty_tags_idx++;
        return $substitute_tag;
    }

    /**
     * DOMの処理の邪魔になるSmartyのタグを代理文字に置換する preg_replace_callback のコールバック関数
     *
     * HTMLエレメント内部の処理
     *
     * @param array $arrMatches マッチしたタグの情報
     * @return string 代わりの文字列
     */
    function captureSmartyTagsInTag(array $arrMatches) {
        // Smartyタグ内のクォートを処理しやすいよう、いったんダミーのタグに
        $html = preg_replace_callback('/<\!--{.+?\}-->/s', array($this, 'captureSmartyTags2Temptag'), $arrMatches[0]);
        $html = preg_replace_callback('/\"[^"]*?\"/s', array($this, 'captureSmartyTagsInQuote'), $html);
        $html = preg_replace_callback('/###TEMP(\d{8})###/s', array($this, 'captureSmartyTags2Attr'), $html);
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
    function captureSmartyTags2Temptag(array $arrMatches) {
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
    function captureSmartyTagsInQuote(array $arrMatches) {
        $html = preg_replace_callback('/###TEMP(\d{8})###/s', array($this, 'captureSmartyTags2Value'), $arrMatches[0]);
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
    function captureSmartyTags2Value(array $arrMatches) {
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
    function captureSmartyTags2Attr(array $arrMatches) {
        $tag_idx = (int)$arrMatches[1];
        $substitute_tag = sprintf('rel%08d="######"', $tag_idx);
        $this->arrSmartyTagsSub[$tag_idx] = $substitute_tag;
        return ' '.$substitute_tag.' '; // 属性はパース時にスペースが詰まるので、こちらにはスペースを入れておく
    }

    /**
     * DOM Element / Document を走査し、name、class別に分類する
     *
     * @param  DOMNode $objDOMElement DOMNodeオブジェクト
     * @param string $parent_selector 親セレクタ
     * @return void
     */
    function scanChild(DOMNode $objDOMElement, $parent_selector = '') {
        $objNodeList = $objDOMElement->childNodes;

        if (is_null($objNodeList)) return;
        foreach ($objNodeList as $element) {

            $arrAttr = array();
            // エレメントの場合、tag名を配列に入れる.
            if ($element instanceof DOMElement) {
                $arrAttr[] = $element->tagName;
            }

            // getAttributeメソッドを持つかを検証
            if (method_exists($element, 'getAttribute')) {
                // id属性を持つ場合.
                if ($element->hasAttribute('id')) {
                    // idの値を配列に格納(ex: [0] => #hoge)
                    $arrAttr[] = '#'.$element->getAttribute('id');
                }
                // class属性を持つ場合.
                if ($element->hasAttribute('class')) {
                    // class名毎に配列に格納(ex: [0] => .hoge [1] => .huga)
                    $arrClasses = preg_split('/\s+/', $element->getAttribute('class'));
                    foreach ($arrClasses as $classname) $arrAttr[] = '.'.$classname;
                }
                // name属性を持つ場合.
                if ($element->hasAttribute('name')) {
                    $this->arrDomHash['name'][$element->getAttribute('name')][] = $element;
                }
            }
            // tag名と属性値を結合してセレクター名とする (ex: body #hoge.huga)
            $this_selector = $parent_selector.' '.implode('', $arrAttr);
            // セレクター名をキーにエレメントを格納.
            $this->arrElementTree[] = array($this_selector, $element);
            // エレメントがDOMNode場合は更に子要素を取り出す.
            if ($element instanceof DOMNode) {
                $this->scanChild($element, $this_selector);
            }
        }
    }

    /**
     * jQueryライクなセレクタを用いてエレメントを検索する
     *
     * @param string  $selector      セレクタ
     * @param integer $index         インデックス（指定がある場合）
     * @param boolean $require       エレメントが見つからなかった場合、エラーとするか
     * @param string  $err_msg       エラーメッセージ
     * @param SC_Plugin_Template_Selector $objSelector セレクタオブジェクト
     * @param string  $parent_index セレクタ検索時の親要素の位置（子孫要素検索のため）
     * @return SC_Plugin_Template_Selector
     */
    function find($selector, $index = NULL, $require = true, $err_msg = NULL, SC_Plugin_Template_Selector $objSelector = NULL, $parent_index = NULL) {

        if (is_null($objSelector)) $objSelector = new SC_Plugin_Template_Selector($this, $this->current_plugin);

        // jQueryライクなセレクタを正規表現に
        $selector = preg_replace('/ *> */', ' >', $selector);

        $regex = '/';
        if (!is_null($parent_index)) $regex .= preg_quote($this->arrElementTree[$parent_index][0], '/');
        // セレクターを配列にします.
        $arrSelectors = explode(' ', $selector);        

        // セレクタから正規表現を生成.
        foreach ($arrSelectors as $sub_selector) {
            if (preg_match('/^(>?)([\w\-]+)?(#[\w\-]+)?(\.[\w\-]+)*$/', $sub_selector, $arrMatch)) {
                if (isset($arrMatch[1]) && $arrMatch[1]) $regex .= ' ';
                else $regex .= '.* ';
                if (isset($arrMatch[2]) && $arrMatch[2]) $regex .= preg_quote($arrMatch[2], '/');
                else $regex .= '([\w\-]+)?';
                if (isset($arrMatch[3]) && $arrMatch[3]) $regex .= preg_quote($arrMatch[3], '/');
                else $regex .= '(#(\w|\-|#{3}[0-9]{8}#{3})+)?';
                if (isset($arrMatch[4]) && $arrMatch[4]) $regex .= '(\.(\w|\-|#{3}[0-9]{8}#{3})+)*'.preg_quote($arrMatch[4], '/').'(\.(\w|\-|#{3}[0-9]{8}#{3})+)*'; // class指定の時は前後にもclassが付いているかもしれない
                else $regex .= '(\.(\w|\-|#{3}[0-9]{8}#{3})+)*';
            }
        }
        $regex .= '$/i';

        $cur_idx = 0;
        // 絞込み検索のときは、前回見つけた位置から検索を開始する
        $startIndex = is_null($parent_index) ? 0 : $parent_index;

        // エレメントツリーのセレクタを先ほど作成した正規表現で順に検索.
        for ($iLoop=$startIndex; $iLoop < count($this->arrElementTree); $iLoop++) {

            if (preg_match($regex, $this->arrElementTree[$iLoop][0])) {
                if (is_null($index) || $cur_idx == $index) {
                    // 検索にかかったエレメントをセレクターのメンバ変数の配列に入れる
                    $objSelector->addElement($iLoop, $this->arrElementTree[$iLoop]);
                }
                $cur_idx++;
            }
        }

        if ($require && $cur_idx == 0) {
            $this->setError(
                $this->current_plugin,
                $selector,
                SC_Plugin_Template_Transformer::ERR_TARGET_ELEMENT_NOT_FOUND,
                $err_msg
            );
        }

        return $objSelector;
    }

    /**
     * DOMを用いた変形を実行する
     *
     * @param string $mode       実行するメソッドの種類
     * @param string $target_key 変形対象のエレメントの完全なセレクタ
     * @param string $html_snip  HTMLコード
     * @return boolean
     */
    function setTransform($mode, $target_key, $html_snip) {

        $substitute_tag = sprintf('<!--###%08d###-->', $this->smarty_tags_idx);

        $this->arrSmartyTagsOrg[$this->smarty_tags_idx] = $html_snip;
        $this->arrSmartyTagsSub[$this->smarty_tags_idx] = $substitute_tag;
        $this->smarty_tags_idx++;

        $objSnip = $this->objDOM->createDocumentFragment();
        $objSnip->appendXML($substitute_tag);

        $objElement = false;
        if (isset($this->arrElementTree[$target_key]) && $this->arrElementTree[$target_key][0]) {
            $objElement = &$this->arrElementTree[$target_key][1];
        }

        if (!$objElement) return false;

        try {
            if ($mode == 'appendChild') {
                $objElement->appendChild($objSnip);
            } elseif ($mode == 'insertBefore') {
                if (!is_object($objElement->parentNode)) return false;
                $objElement->parentNode->insertBefore($objSnip, $objElement);
            } elseif ($mode == 'insertAfter') {
                if ($objElement->nextSibling) {
                     $objElement->parentNode->insertBefore($objSnip, $objElement->nextSibling);
                } else {
                     $objElement->parentNode->appendChild($objSnip);
                }
            } elseif ($mode == 'replaceChild') {
                if (!is_object($objElement->parentNode)) return false;
                $objElement->parentNode->replaceChild($objSnip, $objElement);
            }
            $this->snip_count++;
        } catch (Exception $e) {
            // TODO エラー処理
        }
        return true;
    }

    /**
     * セレクタエラーを記録する
     *
     * @param string  $plugin_name プラグイン名
     * @param string  $selector    セレクタ
     * @param integer $type        エラーの種類
     * @param string  $err_msg     エラーメッセージ
     * @return void
     */
    function setError($plugin_name, $selector, $type, $err_msg = NULL) {
        $this->arrErr[] = array(
            'plugin_name' => $plugin_name,
            'selector'    => $selector,
            'type'        => $type,
            'err_msg'     => $err_msg
        );
    }

    /**
     * HTMLに戻して、Transform用に付けたマーカーを削除し、Smartyのタグを復元する
     *
     * @return mixed トランスフォーム済みHTML。まったくトランスフォームが行われなかった場合はfalse。
     */
    function getHTML() {
        if (count($this->arrErr)) {
            // エラーメッセージ組み立て
            $err_msg = "";
            foreach ($this->arrErr as $arrErr) {
                if ($arrErr['err_msg']) {
                    $err_msg .= "<br />".$arrErr['err_msg'];
                } else {
                    if ($arrErr['type'] == SC_Plugin_Template_Transformer::ERR_TARGET_ELEMENT_NOT_FOUND) {
                        $err_msg .= "<br />${arrErr['selector']} が存在しません";
                    } else {
                        $err_msg .= "<br />".print_r($arrErr, true);
                    }
                }
            }
            // TODO エラー処理
            // ECC_Plugin_Engine::dispError(FREE_ERROR_MSG, "テンプレートの操作に失敗しました。".$err_msg);

        } elseif ($this->snip_count) {
            $html = $this->objDOM->saveHTML();
            // 置換　$htmlの$this->arrSmartyTagsSubを$this->arrSmartyTagsOrgに置換
            $html = str_replace($this->arrSmartyTagsSub, $this->arrSmartyTagsOrg, $html);
            $html = preg_replace('/^.*<\!--TemplateTransformer start-->/s', '', $html);
            $html = preg_replace('/<\!--TemplateTransformer end-->.*$/s', '', $html);
            return $html;

        } else {
            return false;
        }
    }


    /**
     * 変形済みファイルを書き出す
     *
     * @param boolean $test_mode 
     * @return mixed  書き出しに成功した場合は,書き出したファイルのパス. 失敗した場合は false.
     */
    function saveHTMLFile($filename, $test_mode = false) {
        $html = $this->getHTML();
        if ($html && $test_mode == false) {
            // 成功し、かつ test_mode でなければファイルに書き出す
            $filepath = PLUGIN_TMPL_CACHE_REALDIR . $filename;
            $dir = dirname($filepath);

            if (!file_exists($dir)) mkdir($dir, PLUGIN_DIR_PERMISSION, true);
            if (!file_put_contents($filepath, $html)) return false;
            return $filepath;
        } else {
            return false;
        }
    }

}
