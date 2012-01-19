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
 * テンプレートの transform に関する情報のコンテナとなるクラス
 *
 * @package Plugin
 * @author LOCKON CO.,LTD.
 * @version $Id: $
 */
class SC_Plugin_Template_Transform_List {

    // トランスフォームするテンプレートの配列
    var $arrConfsByTemplates;
    var $arrTemplatesByPlugin;
    var $arrBlocsByPlugin;

    /**
     * SC_Plugin_Template_Transform_List オブジェクトを返す（Singletonパターン）
     *
     * @return object SC_Plugin_Template_Transform_List
     */
    function getSingletonInstance() {
        if (!isset($GLOBALS['_SC_Plugin_Template_Transform_List_instance']) || is_null($GLOBALS['_SC_Plugin_Template_Transform_List_instance'])) {
            $GLOBALS['_SC_Plugin_Template_Transform_List_instance'] =& new SC_Plugin_Template_Transform_List();
        }
        return $GLOBALS['_SC_Plugin_Template_Transform_List_instance'];
    }

    /**
     * 初期化
     *
     * @return void
     */
    function init() {
        $this->arrConfsByTemplates = array();
        // プラグインが介入するテンプレートの配列
        $this->arrTemplatesByPlugin = array();
        // HeadNaviに追加するブロックの配列
        $this->arrHeadNaviBlocsByPlugin = array();
    }

    /**
     * 設定対象のテンプレートをセットする
     *
     * @param streing $tmpl 設定対象のテンプレートパス
     * @param SC_Plugin_Base $objPlugin プラグインオブジェクト
     * @return SC_Plugin_Template_Transformer 指定したテンプレートを transform するための SC_Plugin_Template_Transformer オブジェクト
     */
    function setTemplate($tmpl, SC_Plugin_Base $objPlugin) {
        $this->arrTemplatesByPlugin[$objPlugin->arrSelfInfo['class_name']][$tmpl] = 1;

        if (!is_array($this->arrConfsByTemplates)) $this->arrConfsByTemplates = array();
        if (!array_key_exists($tmpl, $this->arrConfsByTemplates)) {
            // テンプレートパスをキーにトランスフォーマのインスタンスをセット.
            $this->arrConfsByTemplates[$tmpl] = new SC_Plugin_Template_Transformer($tmpl);
        }
        // 処理を行うプラグイン名をセットする.
        $this->arrConfsByTemplates[$tmpl]->setCurrentPlugin($objPlugin->arrSelfInfo['class_name']);
        return $this->arrConfsByTemplates[$tmpl];
    }

    /**
     * 設定対象の管理者用テンプレートをセットする
     *
     * @param streing $tmpl 設定対象のテンプレートのパス（adminディレクトリからの相対パス）
     * @param SC_Plugin_Base $objPlugin プラグインオブジェクト
     * @return SC_Plugin_Template_Transformer SC_Plugin_Template_Transformer オブジェクト
     */
    function setTemplateAdmin($tmpl, SC_Plugin_Base $objPlugin) {
        return $this->setTemplate('admin/'.$tmpl, $objPlugin);
    }

    /**
     * 設定対象のPC用テンプレートをセットする
     *
     * @param streing $tmpl 設定対象のテンプレートのパス（PCディレクトリからの相対パス）
     * @param SC_Plugin_Base $objPlugin プラグインオブジェクト
     * @return SC_Plugin_Template_Transformer SC_Plugin_Template_Transformer オブジェクト
     */
    function setTemplatePC($tmpl, SC_Plugin_Base $objPlugin) {
        return $this->setTemplate(TEMPLATE_NAME.'/'.$tmpl, $objPlugin);
    }

    /**
     * 設定対象の携帯用テンプレートをセットする
     *
     * @param streing $tmpl 設定対象のテンプレートのパス（携帯ディレクトリからの相対パス）
     * @param SC_Plugin_Base $objPlugin プラグインオブジェクト
     * @return SC_Plugin_Template_Transformer SC_Plugin_Template_Transformer オブジェクト
     */
    function setTemplateMobile($tmpl, SC_Plugin_Base $objPlugin) {
        return $this->setTemplate(MOBILE_TEMPLATE_NAME.'/'.$tmpl, $objPlugin);
    }

    /**
     * 設定対象のスマホ用テンプレートをセットする
     *
     * @param streing         $tmpl      設定対象のテンプレートのパス（スマホディレクトリからの相対パス）
     * @param SC_Plugin_Base $objPlugin プラグインオブジェクト
     * @return SC_Plugin_Template_Transformer SC_Plugin_Template_Transformer オブジェクト
     */
    function setTemplateSphone($tmpl, SC_Plugin_Base $objPlugin) {
        return $this->setTemplate(SMARTPHONE_TEMPLATE_NAME.'/'.$tmpl, $objPlugin);
    }

    /**
     * 指定したテンプレートの transform を実行する
     *
     * @param string $group_name transformするテンプレート
     * @return void
     */
    function transform($tmpl) {
        $this->arrConfsByTemplates[$tmpl]->saveHTMLFile($tmpl);
    }

    /**
     * 全てのテンプレートの transform を実行する
     *
     * @param boolean $test_mode
     * @return void
     */
    function transformAll($test_mode = false) {
        foreach ($this->arrConfsByTemplates as $tmpl => $objTransformaer) {
            $objTransformaer->saveHTMLFile($tmpl, $test_mode);
        }
    }

    /**
     * テンプレートのヘッダに追加するPHPのURLをセットする
     *
     * @param string $url PHPファイルのURL
     * @return void
     */
    function setHeadNavi($url){
        $this->arrHeadNaviBlocsByPlugin[$url] = TARGET_ID_HEAD;
    }

    /**
     * PHPのURLをテンプレートのヘッダに追加する
     *
     * @param array|null $arrBlocs  配置情報を含めたブロックの配列
     * @return void
     */
    function setHeadNaviBlocs(&$arrBlocs){
        foreach($this->arrHeadNaviBlocsByPlugin as $key => $value){
            $arrBlocs[] = array(
                'target_id' =>$value,
                'php_path' => $key
            );
        }
    }

}

?>
