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

    
    
    
    
    public function install(){

    }

    public function uninstall(){

    }

    protected function getInstallSQL(){
        
    }

    protected function getUninstallSQL(){

    }


}