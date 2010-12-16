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
require_once CLASS_PATH . 'SC_Query.php';
require_once CLASS_EX_PATH . 'db_extends/SC_DB_MasterData_Ex.php';

/**
 * モジュールデータ管理クラス.
 * 各モジュールに固有のデータへのアクセスを担当する.
 *
 * @example
 * $module = new SC_Module('mdl_gmopg', 'GMOPG決済モジュール');
 * $arrSubData = $module->getSubData();
 * $module->registerSubData($arrData);
 * $arrPaymethod = $module->getMasterData('paymethod); // data/cache/mtb_mdl_gmopg_paymethod.php
 * $module->log('order error!', $debugValue); // data/logs/mdl_gmopg.log
 *
 * @package module
 * @author LOCKON CO.,LTD.
 * @version $Id$
 *
 * TODO モジュールコード周りの改修 命名がばらばらなのを吸収できるように
 * TODO テーブル拡張, マスターデータ登録, 初期ファイルコピー処理の追加
 */
class SC_Module {

    /** モジュール名 */
    var $moduleName;

    /** モジュールコード */
    var $moduleCode;

    /** サブデータを保持する */
    var $subData;

    /** パス定義 */
    var $classPath    = 'class/';
    var $templatePath = 'template/';
    var $installPath  = 'install/';

    /**
     * コンストラクタ
     *
     * @param string $code
     * @param string $name
     * @return SC_Module
     */
    function SC_Module($code, $name='') {
        $this->setCode($code);
        $this->setName($name);
    }

    function setName($name) {
        $this->moduleName = strtolower($name);
    }

    function setCode($code) {
        $this->moduleCode = $code;
    }

    /**
     * モジュール名を返す.
     *
     * @return string
     */
    function getName() {
        if (empty($this->moduleName)) {
            $moduleName = $this->_getName();
            $this->setName($moduleName);
            return $moduleName;
        }
        return $this->moduleName;
    }

    /**
     * DBからモジュール名を取得する
     *
     * @return string
     */
    function _getName() {
        $objQuery = new SC_Query;
        return $objQuery->get('module_name', 'dtb_module', 'module_code = ?', $this->getCode());
    }

    /**
     * モジュールコードを返す.
     *
     * @param boolean $toUpper 大文字に変換するかどうか.デフォルトはfalse
     * @return string
     */
    function getCode($toUpper = false) {
        $moduleCode = $this->moduleCode;
        return $toUpper ? strtoupper($moduleCode) : $moduleCode;
    }

    /**
     * モジュールのベースパスを返す
     *
     * @return string
     */
    function getBasePath() {
        return MODULE_PATH . $this->getCode() . '/';
    }

    /**
     * テンプレートパスを返す
     *
     * @return string
     */
    function getTemplatePath() {
        return $this->getBasePath() . $this->templatePath;
    }

    /**
     * クラスパスを返す.
     *
     * @return string
     */
    function getClassPath() {
        return $this->getBasePath() . $this->classPath;
    }

    /**
     * dtb_moduleのsub_dataを取得する.
     *
     * @access private
     * @param booean $force
     * @return mixed|null
     */
    function _getSubData($force = false) {
        if (isset($this->subData)) return $this->subData;

        $moduleCode = $this->getCode();
        $objQuery = new SC_Query;
        $ret = $objQuery->get(
            'sub_data', 'dtb_module', 'module_code = ?', array($moduleCode)
        );

        if (isset($ret)) {
            $this->subData = unserialize($ret);
            return $this->subData;
        }
        return null;
    }

    /**
     * dtb_moduleのsub_dataを取得する.
     *
     * @param string $key
     * @param boolean $force
     * @return mixed|null
     */
    function getSubData($key = null, $force = false) {
        $subData = $this->_getSubData($force);
        $returnData = null;

        if (is_null($key)) {
            $returnData = $subData;
        } else {
            $returnData = isset($subData[$key])
                ? $subData[$key]
                : null;
        }

        return $returnData;
    }

    /**
     * サブデータを登録する.
     *
     * @param mixed $data
     * @param string
     */
    function registerSubData($data, $key = null) {
        $subData = $this->getSubData();

        if (is_null($key)) {
            $subData = $data;
        } else {
            if (is_array($subData)) {
                $subData[$key] = $data;
            } else {
                $subData = array($key => $data);
            }
        }

        $arrUpdate = array('sub_data' => serialize($subData));

        $objQuery = new SC_Query;
        $objQuery->update('dtb_module', $arrUpdate, 'module_code = ?', array($this->getCode()));

        $this->subData = $subData;
    }

    /**
     * マスターデータを登録.
     * 作りかけ...
     *
     * @param unknown_type $key
     * @param unknown_type $value
     */
    function registerMasterData($key, $value) {
        $masterData = new SC_DB_MasterData;
    }

    /**
     * マスターデータを取得する.
     *
     * キャッシュはmtb_mdl_[name]_[***].phpで保存されるが、
     * 取得する場合はは$keyに***を指定する.
     * 例えば、mtb_mdl_gmopg_paymethod.phpにアクセスしたい場合は、
     * $arrPaymethod = $masterData->getMasterData('paymethod');
     * で取得できる.
     *
     * @param string $key
     * @return array
     */
    function getMasterData($key) {
        $key = 'mtb_' . $this->getCode() . "_$key";
        $masterData = new SC_DB_MasterData_Ex;
        return $masterData->getMasterData($key);
    }

    /**
     * ログを出力.
     *
     * @param string $msg
     * @param mixed $data Dumpしたいデータ.デバッグ用.
     * @param string $suffix
     */
    function log($msg, $data = null, $suffix = '') {
        $path = DATA_PATH . 'logs/' . $this->getCode() . "$suffix.log";
        GC_Utils::gfPrintLog($msg, $path);

        if (!is_null($data)) {
            GC_Utils::gfPrintLog(print_r($data, true), $path);
        }
    }
}
/*
 * Local variables:
 * coding: utf-8
 * End:
 */
?>
