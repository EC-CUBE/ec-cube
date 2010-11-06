<?php


class TestPlugin1 extends SC_Plugin_Ex {



    function enable(String $classname){
        return preg_match('/shopping|payment|products/',$classname)?
        !preg_match('/list/', $classname)
        :false
        ;
    }
    
    function init(){
      
    }


    function getVersion(){
        return "0.0.1";
    }

    function getName(){
      return "TestPlugin1";
    }

    function process(){
              
    }

    function mobileprocess(){

    }

    public function install(){
      
    }

    public function uninstall(){
      
    }

    /**
     *
     * @return String インストール用のSQL
     */
    protected function getInstallSQL(){

    }


    protected function getUninstallSQL(){

    }

}