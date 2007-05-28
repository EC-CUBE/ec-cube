<?php

require_once('SC_Param.php');
require_once('SC_Validate.php')
/**
 * Formパラメータ管理クラス
 */

class SC_FormParamsManager {
    var $_objQuery;
    var $_objValidate;
    var $_arrParamsInfo;
    var $_arrInputDBData;
    var $_arrErr;
    
    function SC_FormParamsManager($arrParams = array(), $arrParamsInfo = array()){
        $this->_objQuery    = new SC_Query();
        $this->_objValidate = new SC_Validate();
        $this->_arrParamsInfo  = array();
        $this->_arrInputDBData = array();
        
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
    
    function validate(){
        $arrGroups = array();
        $arrParentAndChild = array();
        
        // Single Validation
        foreach ($this->$_arrParamsInfo as $_key => $objParam) {
            $arrGroups[$objParam->getGroup()][$_key] = $objParam;
            $arrParentAndChild
            
            $arrValidateType = $objParam->getValidateType();
            
            foreach ($arrValidateType as $method => $args) {
                $objValidator = SC_Validate::factory($method, $args);
                
                if ($objValidator->validate($objParam->getValue)->is_error()) {
                    $this->arrErr[$_key] = $objValidate->getErrorMessage();
                }
            }
        }
        
        // Group Validation
        foreach ($arrGroups as $group => $_value) {
            foreach ($_value as $_key => $objParam) {
                $objValidator = SC_Validate::factory('GROUP', $objParam);
                if ($objValidator->validate()->is_error()) {
                    $this->arrErr[$group] = 
                }
    }
    function getEM(){
        return $this->getErrorMessage()
    }
    
    function getErrorMessage(){
        $arrErr = array();
        foreach ($this->_arrParamsInfo as $_key => $objParam) {
            if ($onjParam->isRelation === true) {
                $arrErr['$_key'] = $objParam->getRelateErrorMessage();
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
            if (isset($arrNonEscape['$_key']) && $arrNonEscape['$_key'] == $_key) {
                $arrRet[$key] = $objParam->getValue();
            }
            else {
                $arrRet[$key] = $objParam->getEscapeValue();
                
            }
        }
            
        return $arrParams;
    }
    
    function getParamByKeyName($keyName, $escape = true){
        if ($escape === true) 
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
    function _addDBData(){
        
    }
    
    function _getEscapeParams($string){
        
    }
?>