<?php
class SC_Display{

    var $response;

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

    /**
     * 機種を判別する。
     * SC_Display::MOBILE = ガラケー = 1
     * SC_Display::SMARTPOHNE = スマホ = 2
     * SC_Display::PC = PC = 4
     * ※PHP4の為にconstは使っていません。 1がガラケーで、2がスマホで4がPCです。
     * @return
     */
    function detectAgent(){
        $nu = new Net_UserAgent_Mobile();
        
        $_SERVER['User-Agent']


    }

    function assign(LC_Page $page){

    }


}