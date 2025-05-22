<?php

namespace Plugin\Emperor;

use Doctrine\ORM\EntityManager;
use Eccube\Plugin\AbstractPluginManager;
use Plugin\Emperor\Entity\Foo;
use Psr\Container\ContainerInterface;

class PluginManager extends AbstractPluginManager
{
    public function install(array $config, ContainerInterface $container) {
        echo "*******************************************".PHP_EOL;
        echo "Install Emperor 1.0.0".PHP_EOL;
        echo "*******************************************".PHP_EOL;
        /** @var EntityManager $entityManager */
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $foo = new Foo();
        $foo->id = 1;
        $foo->name = 'Emperor 1.0.0';
        $entityManager->persist($foo);
        $entityManager->flush($foo);
    }

    public function enable(array $config, ContainerInterface $container) {
        echo "*******************************************".PHP_EOL;
        echo "Enable Emperor 1.0.0".PHP_EOL;
        echo "*******************************************".PHP_EOL;
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $entityManager->find(Foo::class, 1);
    }

    public function disable(array $config, ContainerInterface $container) {
        echo "*******************************************".PHP_EOL;
        echo "Disable Emperor 1.0.0".PHP_EOL;
        echo "*******************************************".PHP_EOL;
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $entityManager->find(Foo::class, 1);
    }

    public function update(array $config, ContainerInterface $container) {
        echo "*******************************************".PHP_EOL;
        echo "Update Emperor 1.0.0".PHP_EOL;
        echo "*******************************************".PHP_EOL;
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $entityManager->find(Foo::class, 1);
    }

    public function uninstall(array $config, ContainerInterface $container) {
        echo "*******************************************".PHP_EOL;
        echo "Uninstall Emperor 1.0.0".PHP_EOL;
        echo "*******************************************".PHP_EOL;
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $entityManager->find(Foo::class, 1);
    }
}
