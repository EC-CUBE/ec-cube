<?php
// TODO GPLのあれ
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
    }

    function response(){
        $this->sendHeader();
                
        echo $this->body;
    }

    function sendHeader(){
        // HTTPのヘッダ
        //        header('HTTP/1.1 '.$this->statuscode.' '.$this->statusTexts[$this->statuscode]);
        foreach ($this->header as $name => $head){
            header($name.': '.$head);
        }
    }


    function setContentType(String $contentType){
        $this->header['Content-Type'] = $contentType;
    }

    function setResposeBody(String $body){
        $this->body = $body;
    }

    /* function addDateHdeader(String $name, $date){
     *
     * }
     */

    function addHeader(String $name, $value){
        $this->header[$name] = $value;
    }

    function containsHeader(String $name){
        return isset($this->header[$name]);
    }

    function sendError( $errorcode){
        header('HTTP/1.1 '.$errorcode.' '.$this->statusTexts[$errorcode]);
    }

    function sendRedirect(String $location){
        if (preg_match("/(" . preg_quote(SITE_URL, '/')
                          . "|" . preg_quote(SSL_URL, '/') . ")/", $location)) {

            $netURL = new Net_URL($location);
            if (!empty($_SERVER['QUERY_STRING'])) {
                $netURL->addRawQueryString($_SERVER['QUERY_STRING']);
            }

            $session = SC_SessionFactory::getInstance();
            if (SC_MobileUserAgent::isMobile() || $session->useCookie() == false) {
                $netURL->addQueryString(session_name(), session_id());
            }

            $netURL->addQueryString(TRANSACTION_ID_NAME, SC_Helper_Session_Ex::getToken());
            header("Location: " . $netURL->getURL());
            exit;
        }
        return false;
    }

    function reload(Array $queryString = array(), $removeQueryString = false) {
        // 現在の URL を取得
        $netURL = new Net_URL($_SERVER['REQUEST_URI']);

        if ($removeQueryString) {
            $netURL->querystring = array();
            $_SERVER['QUERY_STRING'] = ''; // sendRedirect() での処理用らしい
        }

        // QueryString を付与
        if (!empty($queryString)) {
            foreach ($queryString as $key => $val) {
                $netURL->addQueryString($key, $val);
            }
        }

        $this->sendRedirect($netURL->getURL());
    }

    function setHeader(Array $headers){
        $this->header = $headers;
    }

    function setStatus( $sc = 202){
        $this->statuscode = $sc;
    }

}
