<?php
// TODO GPLのあれ
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
    * const('ADMIN',8);
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
    function hoge($page, $is_admin = false){
        if(!$this->deviceSeted || !is_null($this->view)){
            $device = ($is_admin) ? 8 : $this->detectDevice();
            $this->setDevice($device);
        }
        $this->assignobj($page);
        $this->response->setResposeBody($this->view->getResponse($page->getTemplate()));
    }
    
    function redirect($location){
        $this->response->sendRedirect($location);
    }
    
    function reload($queryString = array(), $removeQueryString = false){
        $this->response->reload($queryString, $removeQueryString);
    }
    
    function noAction(){    
        return;
    }
    
    function addHeader($name, $value){
        $this->response->addHeader($name, $value);
    }

    /**
     * デバイス毎の出力方法を自動で変更する、ファサード
     * Enter description here ...
     */
    function setDevice($device=4){
        
        switch ($device){
            case 1:
                $this->response->setContentType("text/html");
                $this->setView(new SC_MobileView());
                break;
            case 2:
                //                $this->view = new
                break;
            case 4:
                $this->setView(new SC_SiteView());
                break;
            case 8:
                $this->setView(new SC_AdminView());
        }
        $this->deviceSeted = true;
    }
    
    function setView($view){
        
        $this->view = $view;
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
        $su = new SC_SmartphoneUserAgent();
        $retDevice = 0;
        if($nu->isMobile()){
            $retDevice = 1;
        }elseif ($su->isSmartphone()){
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
