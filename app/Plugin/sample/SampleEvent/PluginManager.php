<?php

namespace Plugin\SampleEvent;
use Eccube\Plugin\AbstractPluginManager;

class PluginManager extends AbstractPluginManager {

    public function install($plugin,$app)
    {
        echo "<hr>";
        echo date('r');
        echo "<hr>";
    }

    public function uninstall($config,$app){}

    public function enable($config,$app){}

    public function disable($config,$app){}

    public function update($config,$app){}
}
