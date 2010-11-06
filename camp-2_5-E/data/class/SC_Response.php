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
    var $statuscode;
    var $header = array();

    var $statusTexts = array(
    '100' => 'Continue',
    '101' => 'Switching Protocols',
    '200' => 'OK',
    '201' => 'Created',
    '202' => 'Accepted',
    '203' => 'Non-Authoritative Information',
    '204' => 'No Content',
    '205' => 'Reset Content',
    '206' => 'Partial Content',
    '300' => 'Multiple Choices',
    '301' => 'Moved Permanently',
    '302' => 'Found',
    '303' => 'See Other',
    '304' => 'Not Modified',
    '305' => 'Use Proxy',
    '306' => '(Unused)',
    '307' => 'Temporary Redirect',
    '400' => 'Bad Request',
    '401' => 'Unauthorized',
    '402' => 'Payment Required',
    '403' => 'Forbidden',
    '404' => 'Not Found',
    '405' => 'Method Not Allowed',
    '406' => 'Not Acceptable',
    '407' => 'Proxy Authentication Required',
    '408' => 'Request Timeout',
    '409' => 'Conflict',
    '410' => 'Gone',
    '411' => 'Length Required',
    '412' => 'Precondition Failed',
    '413' => 'Request Entity Too Large',
    '414' => 'Request-URI Too Long',
    '415' => 'Unsupported Media Type',
    '416' => 'Requested Range Not Satisfiable',
    '417' => 'Expectation Failed',
    '500' => 'Internal Server Error',
    '501' => 'Not Implemented',
    '502' => 'Bad Gateway',
    '503' => 'Service Unavailable',
    '504' => 'Gateway Timeout',
    '505' => 'HTTP Version Not Supported',
    );


    /**
     *
     * Enter description here ...
     */
    var $encoding;

    function SC_Response(){
        $this->http = new HTTP_Response();
    }

    function response(){
        $this->sendHeader();
    }

    function sendHeader(){
        // HTTPのヘッダ
        header('HTTP/1.1 '.$this->statuscode.' '.$this->statusTexts[$this->statuscode]);
        foreach ($this->header as $name => $head){
            header($name.': '.$head);
        }

    }


    function setContentLength(int $length){

    }


    function setContentType(String $contentType){
        $this->header['Content-Type'] = $contentType;
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

    function sendError(int $errorcode){

    }

    function sendRedirect(String $location){

    }

    function setHeader(Array $headers){
        $this->header = $headers;
    }

    function setStatus(int $sc = 202){
        $this->statuscode = $sc;
    }

}