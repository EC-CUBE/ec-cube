<?php

namespace Plugin\Horizon;

use Doctrine\ORM\EntityManager;
use Eccube\Plugin\AbstractPluginManager;
use Plugin\Horizon\Entity\Dash;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PluginManager extends AbstractPluginManager
{
    public function install(array $config, ContainerInterface $container) {
        echo "*******************************************".PHP_EOL;
        echo "Install Horizon 1.0.0".PHP_EOL;
        echo "*******************************************".PHP_EOL;
        /** @var EntityManager $entityManager */
        $entityManager = $container->get('doctrine.orm.entity_manager');

        $horizon = new Dash();
        $horizon->id = 0;
        $horizon->name = 'Horizon';

        $entityManager->persist($horizon);
        $entityManager->flush($horizon);
    }

    public function enable(array $config, ContainerInterface $container) {
        echo "*******************************************".PHP_EOL;
        echo "Enable Horizon 1.0.0".PHP_EOL;
        echo "*******************************************".PHP_EOL;
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $entityManager->find(Dash::class, 1);
    }

    public function disable(array $config, ContainerInterface $container) {
        echo "*******************************************".PHP_EOL;
        echo "Disable Horizon 1.0.0".PHP_EOL;
        echo "*******************************************".PHP_EOL;
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $entityManager->find(Dash::class, 1);
    }

    public function update(array $config, ContainerInterface $container) {
        echo "*******************************************".PHP_EOL;
        echo "Update Horizon 1.0.0".PHP_EOL;
        echo "*******************************************".PHP_EOL;
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $entityManager->find(Dash::class, 1);
    }

    public function uninstall(array $config, ContainerInterface $container) {
        echo "*******************************************".PHP_EOL;
        echo "Uninstall Horizon 1.0.0".PHP_EOL;
        echo "*******************************************".PHP_EOL;

        /** @var EntityManager $entityManager */
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $entityManager->find(Dash::class, 1);
    }
}
