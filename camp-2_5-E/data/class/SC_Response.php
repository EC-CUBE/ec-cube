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

    function setContentType(String $contentType){

    }

    function setResponseCode(int $code){

    }

    function setResposeBody(String $body){

    }
 
    function addDateHdeader(String $name, $date){
        
    }
    function addHeader(String $name, $value){
      $this->header[$name] = $value;    
    }
    
    function containsHeader(String $name){
        return isset($this->header[$name]);
    }

}