<?php


namespace Plugin\SampleEntity;
use Eccube\Plugin\AbstractPluginManager;
use Doctrine\DBAL\Migrations\Migration;
use Doctrine\DBAL\Migrations\Configuration\Configuration;

class PluginManager extends AbstractPluginManager {

    public function install($plugin,$app)
    {
        echo "<hr>";
        echo "installed". date('r');
        echo "<hr>";

        $this->migrationSchema($app, __DIR__ . '/migration' ,$plugin['code']);

    }

    public function uninstall($plugin,$app){
      
        echo "<hr>";
        echo "uninstalled". date('r');
        echo "<hr>";

        $this->migrationSchema($app, __DIR__ . '/migration' ,$plugin['code'],0); // 0にするとプラグインをインストールする前に戻す

    }

    public function enable($plugin,$app){}

    public function disable($plugin,$app){}

    public function update($plugin,$app){


    }
}
