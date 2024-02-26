<?php

namespace Plugin\Boomerang;

use Eccube\Plugin\AbstractPluginManager;
use Psr\Container\ContainerInterface;

class PluginManager extends AbstractPluginManager
{
    public function install(array $config, ContainerInterface $container) {
        echo "*******************************************".PHP_EOL;
        echo "Install Boomerang 1.0.0".PHP_EOL;
        echo "*******************************************".PHP_EOL;
    }

    public function enable(array $config, ContainerInterface $container) {
        echo "*******************************************".PHP_EOL;
        echo "Enable Boomerang 1.0.0".PHP_EOL;
        echo "*******************************************".PHP_EOL;
    }

    public function disable(array $config, ContainerInterface $container) {
        echo "*******************************************".PHP_EOL;
        echo "Disable Boomerang 1.0.0".PHP_EOL;
        echo "*******************************************".PHP_EOL;
    }

    public function update(array $config, ContainerInterface $container) {
        echo "*******************************************".PHP_EOL;
        echo "Update Boomerang 1.0.0".PHP_EOL;
        echo "*******************************************".PHP_EOL;
    }

    public function uninstall(array $config, ContainerInterface $container) {
        echo "*******************************************".PHP_EOL;
        echo "Uninstall Boomerang 1.0.0".PHP_EOL;
        echo "*******************************************".PHP_EOL;
    }
}
