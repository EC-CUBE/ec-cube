<?php



abstract class SC_Plugin
{

    
    
    /**
     *
     * plugin_path
     * @var String
     */
    private $path;

    public function __construct(){
        if(!defined(PLUGIN_PATH)){
            define("PLUGIN_PATH",HTML_PATH."/user_data/plugins/");
        }
        $this->init();
    }
    
    public abstract function init();
    
    public abstract function enable();
    
    public abstract function getVersion();
    
    public abstract function getName();
    
    
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