<?php
/**
 * 個々のパラメータを管理するクラス
 */
class SC_Param {
    var $_keyname;
    var $_dispname;
    var $_value;
    var $_group;
    var $_parent;
    var $_file;
    var $_convert;
    var $_validate;
    var $_javascript;
    
    function SC_Param($arrParamInfo){
        $this->_init($arrParamInfo);
    }
    
    function _init($arrParamInfo){
        $arrProperties = array_keys(get_object_vars($this));
        
        foreach ($arrProperties as $property) {
            $this->$property = isset($arrParamInfo[$property])
                ? $arrParamInfo[preg_replace('/^_/', '', $property)]
                : null;
        }
    }
    
    function getEscapeValue($CHAR_CODE = CHAR_CODE){
        return htmlspecialchars($this->_value, ENT_QUOTES, $CHAR_CODE);
    }
    
    function getValue(){
        return $this->_value;
    }
    
    function convert(){
        $this->_value = mb_convert_kana($this->_value, $this->$_convert);
    }
    
    function getKeyName(){
        return $this->_keyname;
    }
    
    function getDispName(){
        return $this->_dispname;
    }
    
    function is_file(){
        return $this->_file == true ? true : false;
    }
    
}
?>