<?php

require_once('SC_Param.php');
require_once('SC_Validator.php');
/**
 * Form�ѥ�᡼���������饹
 */
class SC_FormParamsManager {
    var $_arrObjParams;
    var $_arrErrorMessage;
    var $_arrGropus;
    
    function SC_FormParamsManager($arrParams = array(), $arrParamsInfo = array()){
        $this->_arrObjParams    = array();
        $this->_arrErrorMessage = array();
        $this->_arrGropus       = array();
        
        $this->setParams($arrParams, $arrParamsInfo);
    }
    
    function setParams($arrParams, $arrParamsInfo, $useRawParams = false){
        foreach ($arrParamsInfo as $_key => $_value) {
            $arrParamsInfo[$_key]['value'] = $arrParams[$_key];
            $this->_arrObjParams[] = new SC_Param($arrParamsInfo[$_key]);
        }
        // $_POST��$_GET�ϸ�§���Ѷػ�(�̥᥽�åɤˤ��롩)
        if ($useRawParams === true) { return; }
        unset($_POST, $_GET);
    }
    
    function serGroups($groupName, $arrTargetName, $arrValidateMethod){
        foreach ($arrTargetName as $targetName) {
            $this->_arrObjParams['group'] = $groupName:
        );
        $this->_arrGroups[$groupName]['validateMethd'] = $arrValidateMethod
    }
    
    function validate(){
        $arrErrorMessage = array();  // ���顼��å�������Ǽ������
        $arrGroups = $this->_arrGroups;        // ʣ�����ܸ���������
        
        foreach ($this->_arrObjParams as $objParam) {
            $keyname = $objParam->getKeyName();
            
            // ʣ�����ܸ������������
            /*
            if ($objParam->has_group() === true) {
                $arrGroups[$objParam->getGroupName()][] = $objParam;
            }
            */
            
            $arrValidateMethod = $objParam->getValidateMethod();
            
            // ñ�ι��ܤθ���
            foreach ($arrValidateMethod as $method => $args) {
                $objValidator = SC_Validator::factory($method, $args);
                
                if ($objValidator->validate($objParam) === false) {
                    $arrErrorMessage[$keyname] = $objValidator->getErrorMessage();
                }
            }
        }
        
        // ʣ�����ܤθ���
        foreach ($arrGroups as $groupname => $objParam) {
            $keyname = $objParam->getKeyName();
            
            // ���˥��顼���������validation��Ԥ�ʤ�
            if (array_key_exists($keyname, $arrErrorMessage)) {
                continue;
            }
            
            $arrValidateMethod = $objParam->getGroupValidateMethod();
            $objValidator = SC_Validate::factory('GROUP');
            if ($objValidator->validate($arrGroups[$group]) === true) {
                $this->arrErr[$group] = $objValidator->getErrorMessage();
            }
        }
        
        return $arrErrorMessage;
    }
    
    function convert(){
        foreach ($this->_arrObjParams as &$objParams) {
            $objParams->convert();
        }
    }
    function is_error(){
        if (count($this->_arrErrorMessage) > 0) {
            return true;
        } else {
            return false;
        }
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
     *  �ѥ�᡼�����������
     *
     *  @access  public
     *  @param   boolean $escape  true:���������פ��� false:���������פ��ʤ�
     *  @param   array   $arrNonEscape ���������פ��ʤ��ͤ�����ǻ���
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
    function _addDBData(){
        
    }
    
    function _getEscapeParams($string){
        
    }
}
?>
