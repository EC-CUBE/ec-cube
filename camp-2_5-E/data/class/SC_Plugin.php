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
    
    public function __construct(){
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

    /**
     * 
     * @return String インストール用のSQL
     */
    protected function getInstallSQL(){
        
    }

    
    protected function getUninstallSQL(){

    }


}