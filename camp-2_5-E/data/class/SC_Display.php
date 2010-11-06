<?php
class SC_Display{

    var $response;

    var $device;

    var $autoSet;
    
    var $view;

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
        switch ($device){
            case 1:
                $this->response->setContentType("text/html");
                
                break;
            case 2:

                break;
            case 4:
                
                break;
        }
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
        }elseif ($nu->isSmartphone()){
            $retDevice = 2;
        }else{
            $retDevice = 4;
        }

        if($this->autoSet){
            $this->setDevice($retDevice);
        }
        return $retDevice;
    }

    function assign(LC_Page $page){
      
    }


}