<?php
/**
 * 個々のパラメータを管理するクラス
 */
class SC_Param {
    var $_name;
    var $_value;
    var $_group;
    var $_parent;
    var $_file;
    var $_convertType;
    var $_validateType;
    var $_error;
    
    function SC_Param($arrParamInfo){
        $this->init($arrParamInfo);
    }
    
    function init($arrParamInfo){
        $arrProperties = array(
            'name', 'value', 'group', 'child', 'parent',
            'file', 'convertType', 'validateType'
        );
        
        foreach ($arrProperties as $property) {
            $this->$property
                = isset($arrParamInfo[$property]) ? $arrParamInfo[$property] : null;
        }
    }
    
    function getEscapeValue(){
        return htmlspecialchars($this->_value, ENT_QUOTES, CHAR_CODE));
    }
    
    function getValue(){
        return $this->_value;
    }
    
    function convert(){
        $this->_value = mb_convert_kana($this->_value, $this->$_convertType);
    }
    
    function getName(){
        return $this->_Name;
    }
    
    function is_file(){
        return $this->_file == true ? true : false;
    }
    
    }
}
?>