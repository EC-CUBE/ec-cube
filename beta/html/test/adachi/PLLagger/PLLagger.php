<?php
require_once('PLLagger/Util.php');
//require_once('PLLgger/Constant.php');

class PLLagger {
    private $config;
    private $plugins;
    private $feeds;
    private $update_feeds;
    private $corrent_phase;
    
    public function __construct($config){
        $this->config  = $config;
        $this->plugins = array();
        $this->feeds   = array();
        $this->update_feeds  = array();
        $this->corrent_phase = 'Init';
    }
    
    public function run () {
        $this->load_plugins();
        
        $phases = array(
            'Subscription',
            'Filter',
            'Publish'
        );
        
        foreach ( $phases as $phase ) {
            $this->corrent_phase = $phase;
            $plugins = $this->get_plugins($phase);
            
            foreach ( $plugins as $plugin ) {
                $plugin->execute($this);
            }
        }
    }
    
    private function load_plugins () {
        foreach ($this->config['plugins'] as $name => $config) {
            $class   = 'PLLagger_Plugin_' . $name;
            $include = preg_replace('/_/', '/', $class) . '.php';
            $ret     = include_once($include);
            
            $err = 0;
            
            if ($ret) {
                if ( preg_match("/^(.+?)_/", $name, $matches) ) {
                    $phase = $matches[1];
                    $this->plugins[$phase][] = new $class($this, $config);
                    $this->log('[OK] ' . $class . ' loaded');
                }
                else {
                    $this->log('[ERR] ' . 'class name is invalid: ' . $class);
                    $err++;
                }
            }
            else {
                $this->log('[ERR] ' . $class . ' not found');
                $err++;
            }
        }
        
        if ($err) {
            $this->_die('function load_plugins()');
        }
    }
    
    public function log ($msg) {
        PLLagger_Util::log($this->corrent_phase, $msg);
    }
    
    public function p ($var) {
        PLLagger_Util::p($var);
    }
    
    public function _die ($msg) {
        $this->log('[DIE] ' . $msg);
        exit();
    }
    
    private function get_plugins ($phase) {
        if ( empty($this->plugins[$phase]) ) {
            return array();
        }
        return $this->plugins[$phase];
    }
    
    public function get_feeds () {
        if ( count($this->update_feeds) > 0 ) {
            return $this->update_feeds;
        }
        return $this->feeds;
    }
    
    public function add_feed ($feed) {
        $this->feeds[] = $feed;
    }
    
    public function update_feed ($feed) {
    
    }
}
?>