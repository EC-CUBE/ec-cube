<?php
class SC_Display{

    var $response;
    
    var $device;

    
    // TODO php4を捨てたときに ここのコメントアウトを外してね。
    /*
     * const('MOBILE',1);
     * const('SMARTPHONE',2);
     * const('PC',4);
     */
    
    function SC_Display(){
        require_once(CLASS_EX_PATH."/SC_Response_Ex.php");
        $this->response = new SC_Response_Ex();
    }


    // TODO このメソッドは、レスポンスを返すためのメソッドです。名前を絶対に変えましょう。
    /**
    *
    * @param $page LC_Page
    */
    function hoge(LC_Page $page){
        $this->assign($page);
        
    }
    
    function setDevice(){
        
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
        $nu = Net_UserAgent_Mobile::factory();

        
    }

    function assign(LC_Page $page){

    }


}