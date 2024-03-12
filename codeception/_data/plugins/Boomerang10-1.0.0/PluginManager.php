<?php

namespace Plugin\Boomerang10;

use Doctrine\ORM\EntityManager;
use Eccube\Plugin\AbstractPluginManager;
use Plugin\Boomerang\Entity\Bar;
use Psr\Container\ContainerInterface;

class PluginManager extends AbstractPluginManager
{
    public function install(array $config, ContainerInterface $container) {
        echo "*******************************************".PHP_EOL;
        echo "Install Boomerang10 1.0.0".PHP_EOL;
        echo "*******************************************".PHP_EOL;
        /** @var EntityManager $entityManager */
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $bar = new Bar();
        $bar->id = 2;
        $bar->name = 'Boomerang10 1.0.0';
        $bar->mail = 'bar@example.com';
        $entityManager->persist($bar);
        $entityManager->flush($bar);
    }

    public function enable(array $config, ContainerInterface $container) {
        echo "*******************************************".PHP_EOL;
        echo "Enable Boomerang10 1.0.0".PHP_EOL;
        echo "*******************************************".PHP_EOL;
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $entityManager->find(Bar::class, 2);
    }

    public function disable(array $config, ContainerInterface $container) {
        echo "*******************************************".PHP_EOL;
        echo "Disable Boomerang10 1.0.0".PHP_EOL;
        echo "*******************************************".PHP_EOL;
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $entityManager->find(Bar::class, 2);
    }

    public function update(array $config, ContainerInterface $container) {
        echo "*******************************************".PHP_EOL;
        echo "Update Boomerang10 1.0.0".PHP_EOL;
        echo "*******************************************".PHP_EOL;
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $entityManager->find(Bar::class, 2);
    }

    public function uninstall(array $config, ContainerInterface $container) {
        echo "*******************************************".PHP_EOL;
        echo "Uninstall Boomerang10 1.0.0".PHP_EOL;
        echo "*******************************************".PHP_EOL;
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $entityManager->find(Bar::class, 2);
    }
}
