<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\SamplePayment;

use Eccube\Entity\Payment;
use Eccube\Plugin\AbstractPluginManager;
use Eccube\Repository\PaymentRepository;
use Plugin\SamplePayment\Entity\Config;
use Plugin\SamplePayment\Repository\ConfigRepository;
use Plugin\SamplePayment\Service\Method\CreditCard;
use Plugin\SamplePayment\Service\PaymentService;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PluginManager extends AbstractPluginManager
{
    public function enable($config, $app, ContainerInterface $container)
    {
        // TODO PluginServiceでインスタンス化されメソッドが呼ばれるので、Injectionできない.
        $paymentRepository = $container->get(PaymentRepository::class);
        $Payment = $paymentRepository->findOneBy(['method_class' => CreditCard::class]);
        if ($Payment) {
            return;
        }

        $Payment = new Payment();
        $Payment->setCharge(0);
        $Payment->setSortNo(999);
        $Payment->setVisible(true);
        $Payment->setMethod('サンプル決済(トークン)'); // todo nameでいいんじゃないか
        $Payment->setServiceClass(PaymentService::class);
        $Payment->setMethodClass(CreditCard::class);

        $entityManager = $container->get('doctrine.orm.entity_manager');
        $entityManager->persist($Payment);
        $entityManager->flush($Payment);

        $Config = new Config();
        $Config->setApiId('api-id');
        $Config->setApiPassword('api-password');
        $Config->setApiUrl('https://payment.example/com');

        $configRepository = $container->get(ConfigRepository::class);
        $Config = $configRepository->get();
        if (!$Config) {
            $Config = new Config();
            $Config->setApiId('api-id');
            $Config->setApiPassword('api-password');
            $Config->setApiUrl('https://payment.example/com');
        }

        $entityManager->persist($Config);
        $entityManager->flush($Config);
    }
}
