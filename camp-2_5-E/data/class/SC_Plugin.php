<?php

class SC_Plugin
{

    /**
     *
     * plugin_path
     * @var String
     */
    var $path;

    function SC_Plugin(){
        $this->__construct();
    }

    function __construct(){
        if(!defined(PLUGIN_PATH)){
            define("PLUGIN_PATH",HTML_PATH."/user_data/plugins/");
        }
        $this->init();
    }

    function init(){


    }

    function enable($classname){

    }

    function getVersion(){

    }

    function getName(){

    }

    function process(){

    }

    function mobileprocess(){

    }

    public function install(){

    }

    public function uninstall(){

    }

    function disablePlugin(){
      $objQuery = new SC_Query();
      $name = $this->getName();
      
      
    }

    function enablePlugin(){
    
    }


    /**
     *
     * @return String インストール用のSQL
     */
    function getInstallSQL(){

    }


    function getUninstallSQL(){

    }


}