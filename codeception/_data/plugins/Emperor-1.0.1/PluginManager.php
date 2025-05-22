<?php

namespace Plugin\Emperor;

use Doctrine\ORM\EntityManager;
use Eccube\Plugin\AbstractPluginManager;
use Plugin\Emperor\Entity\Bar;
use Psr\Container\ContainerInterface;

class PluginManager extends AbstractPluginManager
{
    public function install(array $config, ContainerInterface $container) {
        echo "*******************************************".PHP_EOL;
        echo "Install Emperor 1.0.1".PHP_EOL;
        echo "*******************************************".PHP_EOL;
        $this->saveBar($container);

        $entityManager = $container->get('doctrine.orm.entity_manager');
        $entityManager->find(Bar::class, 1);
    }

    public function enable(array $config, ContainerInterface $container) {
        echo "*******************************************".PHP_EOL;
        echo "Enable Emperor 1.0.1".PHP_EOL;
        echo "*******************************************".PHP_EOL;
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $entityManager->find(Bar::class, 1);
    }

    public function disable(array $config, ContainerInterface $container) {
        echo "*******************************************".PHP_EOL;
        echo "Disable Emperor 1.0.1".PHP_EOL;
        echo "*******************************************".PHP_EOL;
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $entityManager->find(Bar::class, 1);
    }

    public function update(array $config, ContainerInterface $container) {
        echo "*******************************************".PHP_EOL;
        echo "Update Emperor 1.0.1".PHP_EOL;
        echo "*******************************************".PHP_EOL;
        $this->saveBar($container);
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $entityManager->find(Bar::class, 1);
    }

    public function uninstall(array $config, ContainerInterface $container) {
        echo "*******************************************".PHP_EOL;
        echo "Uninstall Emperor 1.0.1".PHP_EOL;
        echo "*******************************************".PHP_EOL;
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $entityManager->find(Bar::class, 1);
    }

    private function saveBar(ContainerInterface $container)
    {
        /** @var EntityManager $entityManager */
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $bar = new Bar();
        $bar->id = 1;
        $bar->name = 'Emperor 1.0.1';
        $entityManager->persist($bar);
        $entityManager->flush($bar);
    }
}
