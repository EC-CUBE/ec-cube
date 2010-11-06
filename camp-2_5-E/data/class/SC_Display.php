<?php
class SC_Display{

    var $response;

    var $device;

    var $autoSet;

    /**
     *
     * @var SC_View
     */
    var $view;

        
    var $deviceSeted = false;
    
    // TODO php4を捨てたときに ここのコメントアウトを外してね。
    /*
    * const('MOBILE',1);
    * const('SMARTPHONE',2);
    * const('PC',4);
    */
    function SC_Display($setPrevURL=true,$autoGenerateHttpHeaders = true){
        require_once(CLASS_EX_PATH."/SC_Response_Ex.php");
        $this->response = new SC_Response_Ex();
        $this->autoSet = $autoGenerateHttpHeaders;
        if ($setPrevURL) {
            $this->setPrevURL();
        }
    }
    
    function setPrevURL(){
        $objCartSess = new SC_CartSession();
        $objCartSess->setPrevURL($_SERVER['REQUEST_URI']);

    }


    // TODO このメソッドは、レスポンスを返すためのメソッドです。名前を絶対に変えましょう。
    /**
    *
    * @param $page LC_Page
    */
    function hoge(LC_Page $page){
        $this->assign($page);
        if(!$this->deviceSeted){
            $device = $this->detectDevice();
            $this->setDevice($device);
        }
        $this->response->setResposeBody($this->view->fetch(SITE_FRAME));
    }

    /**
     * デバイス毎の出力方法を自動で変更する、ファサード
     * Enter description here ...
     */
    function setDevice(int $device = 4){
        switch ($device){
            case 1:
                $this->response->setContentType("text/html");
                $this->view = new SC_MobileView();
                break;
            case 2:
                //                $this->view = new
                break;
            case 4:
                $this->view = new SC_SiteView();
                break;
        }
        $this->deviceSeted = true;
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

    function assign($val1,$val2){
        $this->view->assign($val1, $val2);
    }

    function assignobj($obj){
        $this->view->assignobj($obj);
    }

    function assignarray($array){
        $this->view->assignarray($array);
    }


}