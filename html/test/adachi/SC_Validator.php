<?php
class SC_Validator {
    var $_error;
    var $_errorMessage;
    
    function SC_Validator(){
    }
    
    function factory($method, $args = null){
        $class = 'SC_Validator_' . $method;
        require_once('SC_Validator/' . $method . '.php');
        
        return new $class($args);
    }
    
    function validate($objParam){}
    
    function is_error(){
        return $this->_error;
    }
    
    function is_ok(){
        $bool = true;;
        if ($this->is_error()) { $bool = false; }
        
        return $bool;
    }
    
    function getErrorMessage(){}
}
?>
