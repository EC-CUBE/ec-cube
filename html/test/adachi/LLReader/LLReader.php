<?php
require_once('LLReader/Util.php');
require_once('LLReader/Feed.php');
//require_once('LLReader/Constant.php');

class LLReader {
    private $config;
    private $plugins;
    private $feeds;

    public function __construct($config){
        $this->config = $config;
    }

    public function run () {
        $this->load_plugins();

        $phases = array(
            'Subscription',
            'Filter',
            'Publish'
        );
        
        foreach ( $phases as $phase ) {
            $plugins = $this->get_plugins($phase);
            
            foreach ( $plugins as $plugin ) {
                $plugin->execute($this);
            }
        }
    }

    private function load_plugins () {
        foreach ($this->config['plugins'] as $name => $config) {
            $class   = 'LLReader_Plugin_' . $name;
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
                $this->log('[ERR] ' . $class . " not found");
                $err++;
            }
        }

        if ($err) {
            $this->log('[Die] ' . 'function load_plugins()');
            exit();
        }
    }

    public function log ($msg) {
        LLReader_Util::log($msg);
    }

    public function p ($var) {
        LLReader_Util::p($var);
    }

    private function get_plugins ($phase) {
        if ( empty($this->plugins[$phase]) ) {
        	return array();
        }
        return $this->plugins[$phase];
    }

    public function get_feeds () {
        return $this->feeds;
    }
}
?>