<?php
class SC_Response{
    
    /**
     * 
     * @var HTTP_Response
     */
    var $http;
    /**
     * コンテンツタイプ
     * Enter description here ...
     * @var unknown_type
     */
    var $contentType;
    var $header;
    var $body;
    var $header = array();
    
    /**
     * 
     * Enter description here ...
     */
    var $encoding;
    
    function SC_Response(){
        $this->http = new HTTP_Response();
    }
    
    function response(){
                          
    }
    
    function setContentType(String $contentType)
    {
        
    }
    
    function setResponseCode()
    {
        
    }
    
    function setResposeBody(String $body)
    {
        
    }
    
    
      
}