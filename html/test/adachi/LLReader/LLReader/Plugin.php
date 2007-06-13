<?php

abstract class LLReader_Plugin {
    protected $config;
    
    public function __construct ($llr, $config) {
        $this->config = $config;
    }
    
    abstract public function execute ($llr);
    
    protected function get_config () {
        return $this->config;
    }
    
}

?>