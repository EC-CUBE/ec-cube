<?php

namespace Plugin\ExamplePlugin;

use Eccube\Common\Constant;
use Eccube\Entity\Payment;
use Eccube\Plugin\AbstractPluginManager;
use Symfony\Component\Filesystem\Filesystem;

class PluginManager extends AbstractPluginManager
{
    public function __construct()
    {
    }

    /**
     * プラグインインストール時の処理
     *
     * @param $config
     * @param $app
     * @throws \Exception
     */
    public function install($config, $app)
    {
    }

    /**
     * プラグイン削除時の処理
     *
     * @param $config
     * @param $app
     */
    public function uninstall($config, $app)
    {
    }

    /**
     * プラグイン有効時の処理
     *
     * @param $config
     * @param $app
     * @throws \Exception
     */
    public function enable($config, $app)
    {
        $Payment = new Payment();

        $Member = $app['eccube.repository.member']->find(2);
        $rank = $app['eccube.repository.payment']->findOneBy([], ['rank' => 'DESC'])
            ->getRank() + 1;

        $Payment->setMethod('サンプルクレジットカード');
        $Payment->setCharge(0);
        $Payment->setRuleMin(0);
        $Payment->setFixFlg(Constant::ENABLED);
        $Payment->setChargeFlg(Constant::ENABLED);
        $Payment->setRank($rank);
        $Payment->setDelFlg(Constant::DISABLED);
        $Payment->setCreator($Member);

        $app['orm.em']->persist($Payment);
        $app['orm.em']->flush($Payment);

        $Delivery = $app['eccube.repository.delivery']->find(1);
        $PaymentOption = new \Eccube\Entity\PaymentOption();
        $PaymentOption
            ->setPaymentId($Payment->getId())
            ->setPayment($Payment)
            ->setDeliveryId($Delivery->getId())
            ->setDelivery($Delivery);
        $Delivery->addPaymentOption($PaymentOption);
        $app['orm.em']->persist($PaymentOption);

        $app['orm.em']->flush($Delivery);
        $app['orm.em']->flush($PaymentOption);
    }

    /**
     * プラグイン無効時の処理
     *
     * @param $config
     * @param $app
     */
    public function disable($config, $app)
    {

        $Payment = $app['eccube.repository.payment']->findOneBy(['method' => 'サンプルクレジットカード']);
        $PaymentOption = $app['eccube.repository.payment_option']->findOneBy(array('payment_id' => $Payment->getId()));
        if ($Payment) {
            $app['orm.em']->remove($Payment);
            $app['orm.em']->flush($Payment);
        }
        if ($PaymentOption) {
            $app['orm.em']->remove($PaymentOption);
            $app['orm.em']->flush($PaymentOption);
        }
    }

    public function update($config, $app)
    {
    }
}
