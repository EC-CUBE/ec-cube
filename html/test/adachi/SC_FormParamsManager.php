<?php

require_once('SC_Param.php');
require_once('SC_Validator.php');
/**
 * Formパラメータ管理クラス
 */

class SC_FormParamsManager {
    var $_arrParamsInfo;
    var $_arrErr;
    
    function SC_FormParamsManager($arrParams, $arrParamsInfo){
        $this->_arrParamsInfo = array();
        $this->_arrErr = array();
        
        if (count($arrParams) > 0 && count($arrParamsInfo) > 0) {
            $this->setParams($arrParams, $arrParamsInfo);
        }
    }
    
    function setParams($arrParams, $arrParamsInfo, $usePOST = false){
        foreach ($arrParamsInfo as $_key => $_value) {
            $arrParamsInfo[$_key]['value'] = $arrParams[$_key];
            $this->_arrParamsInfo[$_key] = new SC_Param($arrParamsInfo[$_key]);
        }
        // $_POSTは原則使用禁止
        if ($usePOST === true) { return; }
        unset($_POST);
    }
    
    function setGroups($arrGroup){
        $this->_groups = $arrGroup;
    }
    
    function getGroups(){
        return $this->_groups;
    }
    
    
    function validate(){
        $arrGroups = array();    // 複数項目チェック用
        $arrParent = array();    // 上位項目チェック用
        
        
        foreach ($this->_arrParamsInfo as $_key => $objParam) {
            // 複数項目チェック用配列を構築
            if ($objParam->has_group())  { $arrGroups[$objParam->getGroup()][$_key] = $objParam; }
            
            // 上位項目チェック
            if ($objParam->has_parent()) {
                $has_parent = true;
                while ($has_parent) {
                    if ($this->_arrParamsInfo[$objParam->getParent()]->has_parent()) {
                        
                    }
            }
            
            $arrValidateType = $objParam->getValidateType();
            
            // Single Validation
            foreach ($arrValidateType as $method => $args) {
                $objValidator = SC_Validate::factory($method, $args);
                $objValidator->validate($objParam);
                
                if ($objValidator->is_error()) {
                    $this->arrErr[$_key] = $objValidator->getErrorMessage();
                }
            }
        }
        
        // Group Validation
        foreach ($arrGroups as $group => $_value) {
            // 既にエラーがある場合はvalidationを行わない
            if (array_key_exists(array_keys($_value), $this->arrErr)) {
                continue;
            }
            
            $objValidator = SC_Validate::factory('GROUP');
            if ($objValidator->validate($arrGroups[$group]) === true) {
                $this->arrErr[$group] = $objValidator->getErrorMessage();
            }
        }
        
        // 親子 validation
        
        return $this;
    }
    
    function getEM(){
        return $this->getErrorMessage();
    }
    
    function getErrorMessage(){
        $arrErr = array();
        foreach ($this->_arrParamsInfo as $_key => $objParam) {
            if ($onjParam->isRelation === true) {
                $arrErr[$_key] = $objParam->getRelateErrorMessage();
            } else {
                $arrErr[$_key] = $objParam->getErrorMessage();
            }
        }
        return $arrErr;
    }
    /**
     *  static method
     *  SC_Form::getMode();
     */
    function getMode(){
        $mode = '';
        
        if (isset($_POST['mode'])) {
            $mode = $_POST['mode'];
        }
        elseif (isset($_GET['mode'])) {
            $mode = $_GET['mode'];
        }
        
        return $mode;
    }
    
    function &getObjQuery(){
        return $this->_objQuery;
    }
    
    function &initObjQuery(){
        $this->_objQuery = new SC_Query();
        return $this->_objQuery;
    }
    
    function valiadte(){
        return $this->objValidate->validate($this->_arrParamsInfo);
    }
    
    function insert($table, $arrAddInsertData = array()){
        $this->_setDBData($this->arrParams);
        $this->_objQuery->insert($table, $this->_arrDBData);
    }
    
    function update($table, $arrAddUpdateData = array()) {
        
    }
    

    
    /**
     *  パラメータを取得する
     *
     *  @access  public
     *  @param   boolean $escape  true:エスケープする false:エスケープしない
     *  @param   array   $arrNonEscape エスケープしない値を配列で指定
     *  
     *  @return  array | string
     */
    function getParams($arrNonEscape = array()){
        $arrParams = array();
        if (!is_array($arrNonEscape)) { $arrNonEscape = (array)$arrNonEscape; }
        
        foreach ($this->_arrParams as $_key => $_value) {
            if (isset($arrNonEscape[$_key]) && $arrNonEscape[$_key] == $_key) {
                $arrRet[$key] = $objParam->getValue();
            }
            else {
                $arrRet[$key] = $objParam->getEscapeValue();
                
            }
        }
            
        return $arrParams;
    }
    
    function getParamByKeyName($keyName, $escape = true){
        if ($escape === true) {
            return $this->_arrParams[$keyName]->getValue();
        }
        else {
            return $this->_arrParams[$keyName]->getEscapedValue();
        }
    }
    
    function _setDBData($arrData = array()){
        foreach ($arrData as $key => $value) {
            if (is_array($value['value'])) {
                $count = 1;
                foreach ($value['value'] as $val) {
                    $this->_arrDBData[$key . $count] = $val;
                    $count++;
                }
            }
            else {
                $this->_arrDBData[$key] = $value['value'];
            }
        }
    }
}
?>
