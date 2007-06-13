<?php
/**
 *  validationクラス
 */
class SC_Validator {
    var $_error;
    var $_errorMessage;
    
    /**
     *  
     *  
     *  @param string $method 実施するvalidation名
     *  @param mixed  $args   validationに必要な引数
     *  
     *  @return object SC_Validator_$method
     *  @example $objValidator = SC_Validator::factory('MAX', 20);
     */
    function factory($method, $args = null){
        $class = 'SC_Validator_' . $method;
        require_once('SC_Validator/' . $method . '.php');
        
        return new $class($args);
    }
    
    /**
     *  
     *  
     *  @param object SC_Param
     *  
     *  @return void
     */
    function validate($objParam){}
    
    function is_error(){
        return $this->_error;
    }
    
    function is_ok(){
        $bool = true;
        if ($this->is_error()) { $bool = false; }
        
        return $bool;
    }
    
    
    function getErrorMessage(){}
}
?>
