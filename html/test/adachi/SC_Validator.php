<?php
/**
 *  validation���饹
 */
class SC_Validator {
    var $_error;
    var $_errorMessage;
    
    /**
     *  
     *  
     *  @param string $method �»ܤ���validation̾
     *  @param mixed  $args   validation��ɬ�פʰ���
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
