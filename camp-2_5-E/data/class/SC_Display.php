<?php
class SC_Display{

    var $response;

    var $device;

    var $autoSet;

    // TODO php4を捨てたときに ここのコメントアウトを外してね。
    /*
    * const('MOBILE',1);
    * const('SMARTPHONE',2);
    * const('PC',4);
    */

    function SC_Display($autoGenerateHttpHeaders = true){
        require_once(CLASS_EX_PATH."/SC_Response_Ex.php");
        $this->response = new SC_Response_Ex();
        $this->autoSet = $autoGenerateHttpHeaders;
    }


    // TODO このメソッドは、レスポンスを返すためのメソッドです。名前を絶対に変えましょう。
    /**
    *
    * @param $page LC_Page
    */
    function hoge(LC_Page $page){
        $this->assign($page);

    }

    /**
     * デバイス毎の出力方法を自動で変更する、ファサード
     * Enter description here ...
     */
    function setDevice(int $device = 4){

    }

    /**
     * 機種を判別する。
     * SC_Display::MOBILE = ガラケー = 1
     * SC_Display::SMARTPHONE = スマホ = 2
     * SC_Display::PC = PC = 4
     * ※PHP4の為にconstは使っていません。 1がガラケーで、2がスマホで4がPCです。
     * @return
     */
    function detectDevice(){
        $nu = new Net_UserAgent_Mobile();
        $retDevice = 0;
        if($nu->isMobile()){
            $retDevice = 1;
        }elseif ($this->isSmartphone()){
        }else{
        if($this->autoSet){
            $this->setDevice($nu);
        }
        return
    }

    function isSmartphone(){
        $useragents = array(
            'iPhone',         // Apple iPhone
    'iPod',           // Apple iPod touch
    'Android',        // 1.5+ Android
    'dream',          // Pre 1.5 Android
    'CUPCAKE',        // 1.5+ Android
    'blackberry9500', // Storm
    'blackberry9530', // Storm
    'blackberry9520', // Storm v2
    'blackberry9550', // Storm v2
    'blackberry9800', // Torch
    'webOS',          // Palm Pre Experimental
    'incognito',      // Other iPhone browser
    'webmate'         // Other iPhone browser
        );

        $pattern = implode("|", $useragents);
        return preg_match('/['.$pattern.']/', $_SERVER['HTTP_USER_AGENT']);

    }

}

function assign(LC_Page $page){

}


}